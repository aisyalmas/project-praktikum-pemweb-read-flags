<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

$userId = (int) ($_SESSION['user_id'] ?? 1);

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak valid']);
    exit;
}

$bookId       = (int) ($data['book_id'] ?? 0);
$status       = $data['status'] ?? '';
$rating       = (int) ($data['rating'] ?? 0);
$totalPages   = max(0, (int) ($data['page_count'] ?? 0));
$currentPage  = max(0, (int) ($data['current_page'] ?? 0));
$startedAt    = !empty($data['started_at'])  ? $data['started_at']  : null;
$finishedAt   = !empty($data['finished_at']) ? $data['finished_at'] : null;
$description  = trim($data['description'] ?? '');
$description  = $description === '' ? null : $description;
$genreId      = (int) ($data['genre_id'] ?? 1);
$seriesId     = !empty($data['series_id']) ? (int) $data['series_id'] : null;
$seriesBaru   = trim($data['series_baru'] ?? '');
$seriesOrder  = ($data['series_order'] ?? '') !== '' ? (float) $data['series_order'] : null;
$koleksiId    = array_map('intval', (array) ($data['collection_ids'] ?? []));
$koleksiBaru  = (array) ($data['collection_baru'] ?? []);

$statusBoleh = ['reading', 'read', 'tbr', 'dnf', 'wishlist'];

if ($bookId <= 0 || !in_array($status, $statusBoleh, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Status atau buku tidak valid']);
    exit;
}

if ($rating < 0 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Rating harus 0 sampai 5']);
    exit;
}

if (($startedAt && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startedAt)) ||
    ($finishedAt && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $finishedAt))) {
    http_response_code(400);
    echo json_encode(['error' => 'Format tanggal salah']);
    exit;
}

if ($totalPages > 0 && $currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$connect->begin_transaction();

try {
    if ($seriesBaru !== '') {
        $stmt = $connect->prepare(
            "INSERT INTO series (name, created_by) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)"
        );
        $stmt->bind_param('si', $seriesBaru, $userId);
        $stmt->execute();
        $seriesId = $connect->insert_id;
    }

    $stmt = $connect->prepare("
        UPDATE books
        SET page_count = ?, description = ?, genre_id = ?, series_id = ?, series_order = ?
        WHERE id = ?
    ");
    $stmt->bind_param('isiidi', $totalPages, $description, $genreId, $seriesId, $seriesOrder, $bookId);
    $stmt->execute();

    $stmt = $connect->prepare("
        UPDATE user_books
        SET status = ?, current_page = ?, started_at = ?, finished_at = ?
        WHERE user_id = ? AND book_id = ?
    ");
    $stmt->bind_param('sissii', $status, $currentPage, $startedAt, $finishedAt, $userId, $bookId);
    $stmt->execute();

    if ($rating > 0) {
        $stmt = $connect->prepare("
            INSERT INTO reviews (user_id, book_id, rating) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating)
        ");
        $stmt->bind_param('iii', $userId, $bookId, $rating);
        $stmt->execute();
    } else {
        $stmt = $connect->prepare("DELETE FROM reviews WHERE user_id = ? AND book_id = ?");
        $stmt->bind_param('ii', $userId, $bookId);
        $stmt->execute();
    }

    $stmt = $connect->prepare("
        DELETE cb FROM collection_books cb
        JOIN collections c ON c.id = cb.collection_id
        WHERE c.user_id = ? AND cb.book_id = ?
    ");
    $stmt->bind_param('ii', $userId, $bookId);
    $stmt->execute();

    foreach ($koleksiId as $cid) {
        $stmt = $connect->prepare("
            INSERT IGNORE INTO collection_books (collection_id, book_id)
            SELECT id, ? FROM collections WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param('iii', $bookId, $cid, $userId);
        $stmt->execute();
    }

    foreach ($koleksiBaru as $namaKoleksi) {
        $namaKoleksi = trim($namaKoleksi);
        if ($namaKoleksi === '') {
            continue;
        }

        $stmt = $connect->prepare("
            INSERT INTO collections (user_id, name) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
        ");
        $stmt->bind_param('is', $userId, $namaKoleksi);
        $stmt->execute();
        $cid = $connect->insert_id;

        $stmt = $connect->prepare("INSERT IGNORE INTO collection_books (collection_id, book_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $cid, $bookId);
        $stmt->execute();
    }

    $connect->commit();
    echo json_encode(['success' => true]);

} catch (Throwable $e) {
    $connect->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
}

if (($data['action'] ?? '') === 'hapus') {
    $bookId = (int) ($data['book_id'] ?? 0);
    if ($bookId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Buku tidak valid']);
        exit;
    }

    $stmt = $connect->prepare("DELETE FROM user_books WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param('ii', $userId, $bookId);
    $stmt->execute();

    $stmt = $connect->prepare("
        DELETE cb FROM collection_books cb
        JOIN collections c ON c.id = cb.collection_id
        WHERE c.user_id = ? AND cb.book_id = ?
    ");
    $stmt->bind_param('ii', $userId, $bookId);
    $stmt->execute();

    $connect->query("DELETE FROM reviews WHERE user_id = $userId AND book_id = $bookId");
    $connect->query("DELETE FROM reading_logs WHERE user_id = $userId AND book_id = $bookId");

    echo json_encode(['success' => true]);
    exit;
}