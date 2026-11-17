<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

$userId = (int) ($_SESSION['user_id'] ?? 1);

$data   = json_decode(file_get_contents('php://input'), true);
$aksi   = $data['action'] ?? '';
$bookId = (int) ($data['book_id'] ?? 0);

// wishlist, tbr
if ($aksi === 'rak') {
    $status = $data['status'] ?? '';
    if (!in_array($status, ['tbr', 'wishlist'], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Status tidak valid']);
        exit;
    }

    $stmt = $connect->prepare("INSERT IGNORE INTO user_books (user_id, book_id, status) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $userId, $bookId, $status);
    $stmt->execute();

    echo json_encode(['success' => true]);

} elseif ($aksi === 'log') {
    $halaman = max(0, (int) ($data['pages'] ?? 0));
    if ($halaman === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Jumlah halaman harus lebih dari 0']);
        exit;
    }

    $stmt = $connect->prepare("
        INSERT INTO reading_logs (user_id, book_id, log_date, pages_read)
        VALUES (?, ?, CURDATE(), ?)
        ON DUPLICATE KEY UPDATE pages_read = pages_read + VALUES(pages_read)
    ");
    $stmt->bind_param('iii', $userId, $bookId, $halaman);
    $stmt->execute();

    $stmt = $connect->prepare("
        UPDATE user_books ub
        JOIN books b ON b.id = ub.book_id
        SET
            ub.current_page = CASE
                WHEN b.page_count > 0 THEN LEAST(ub.current_page + ?, b.page_count)
                ELSE ub.current_page + ?
            END,
            ub.status = IF(ub.status = 'tbr', 'reading', ub.status),
            ub.started_at = COALESCE(ub.started_at, CURDATE())
        WHERE ub.user_id = ? AND ub.book_id = ?
    ");
    $stmt->bind_param('iiii', $halaman, $halaman, $userId, $bookId);
    $stmt->execute();

    echo json_encode(['success' => true]);

} elseif ($aksi === 'koleksi-tambah') {
    $collectionId = (int) ($data['collection_id'] ?? 0);

    $stmt = $connect->prepare("
        INSERT IGNORE INTO collection_books (collection_id, book_id)
        SELECT id, ? FROM collections WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param('iii', $bookId, $collectionId, $userId);
    $stmt->execute();

    echo json_encode(['success' => true]);

} elseif ($aksi === 'koleksi-hapus') {
    $collectionId = (int) ($data['collection_id'] ?? 0);

    $stmt = $connect->prepare("
        DELETE cb FROM collection_books cb
        JOIN collections c ON c.id = cb.collection_id
        WHERE cb.collection_id = ? AND cb.book_id = ? AND c.user_id = ?
    ");
    $stmt->bind_param('iii', $collectionId, $bookId, $userId);
    $stmt->execute();

    echo json_encode(['success' => true]);

} elseif ($aksi === 'status') {
    $status = $data['status'] ?? '';
    $boleh  = ['reading', 'read', 'tbr', 'dnf', 'wishlist'];
    if (!in_array($status, $boleh, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Status tidak valid']);
        exit;
    }

    $stmt = $connect->prepare("
        INSERT INTO user_books (user_id, book_id, status) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ");
    $stmt->bind_param('iis', $userId, $bookId, $status);
    $stmt->execute();

    $collectionId = (int) ($data['collection_id'] ?? 0);
    if ($collectionId > 0) {
        $stmt = $connect->prepare("
            INSERT IGNORE INTO collection_books (collection_id, book_id)
            SELECT id, ? FROM collections WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param('iii', $bookId, $collectionId, $userId);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);

} elseif ($aksi === 'series-baru') {
    $nama      = trim($data['name'] ?? '');
    $deskripsi = trim($data['description'] ?? '');
    $deskripsi = $deskripsi === '' ? null : $deskripsi;

    if ($nama === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Nama series wajib diisi']);
        exit;
    }

    $stmt = $connect->prepare("
        INSERT INTO series (name, description, created_by) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
    ");
    $stmt->bind_param('ssi', $nama, $deskripsi, $userId);
    $stmt->execute();

    echo json_encode(['success' => true, 'id' => $connect->insert_id]);

} elseif ($aksi === 'koleksi-baru') {
    $nama      = trim($data['name'] ?? '');
    $deskripsi = trim($data['description'] ?? '');
    $deskripsi = $deskripsi === '' ? null : $deskripsi;

    if ($nama === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Nama koleksi wajib diisi']);
        exit;
    }

    $stmt = $connect->prepare("
        INSERT INTO collections (user_id, name, description) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
    ");
    $stmt->bind_param('iss', $userId, $nama, $deskripsi);
    $stmt->execute();

    echo json_encode(['success' => true, 'id' => $connect->insert_id]);

} elseif ($aksi === 'series-hapus') {
    $seriesId = (int) ($data['series_id'] ?? 0);

    $stmt = $connect->prepare("SELECT COUNT(*) AS jumlah FROM books WHERE series_id = ?");
    $stmt->bind_param('i', $seriesId);
    $stmt->execute();
    $jumlah = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

    if ($jumlah > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Series masih punya buku, kosongkan dulu']);
        exit;
    }

    $stmt = $connect->prepare("DELETE FROM series WHERE id = ?");
    $stmt->bind_param('i', $seriesId);
    $stmt->execute();

    echo json_encode(['success' => true]);

} elseif ($aksi === 'koleksi-hapus-semua') {
    $collectionId = (int) ($data['collection_id'] ?? 0);

    $stmt = $connect->prepare("
        DELETE cb FROM collection_books cb
        JOIN collections c ON c.id = cb.collection_id
        WHERE c.id = ? AND c.user_id = ?
    ");
    $stmt->bind_param('ii', $collectionId, $userId);
    $stmt->execute();

    $stmt = $connect->prepare("DELETE FROM collections WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $collectionId, $userId);
    $stmt->execute();

    echo json_encode(['success' => true]);

} elseif ($aksi === 'log-harian') { // log di dashboard
    $halaman = max(0, (int) ($data['pages'] ?? 0));
    $menit   = max(0, (int) ($data['minutes'] ?? 0));

    if ($halaman === 0 && $menit === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Isi halaman atau menit dulu']);
        exit;
    }

    $stmt = $connect->prepare("
        INSERT INTO reading_logs (user_id, book_id, log_date, pages_read, minutes_read)
        VALUES (?, ?, CURDATE(), ?, ?)
        ON DUPLICATE KEY UPDATE
            pages_read   = pages_read + VALUES(pages_read),
            minutes_read = minutes_read + VALUES(minutes_read)
    ");
    $stmt->bind_param('iiii', $userId, $bookId, $halaman, $menit);
    $stmt->execute();

    $stmt = $connect->prepare("
        UPDATE user_books ub
        JOIN books b ON b.id = ub.book_id
        SET
            ub.current_page = CASE
                WHEN b.page_count > 0 THEN LEAST(ub.current_page + ?, b.page_count)
                ELSE ub.current_page + ?
            END,
            ub.status       = IF(ub.status = 'tbr', 'reading', ub.status),
            ub.started_at   = COALESCE(ub.started_at, CURDATE()),
            ub.last_read_at = NOW()
        WHERE ub.user_id = ? AND ub.book_id = ?
    ");
    $stmt->bind_param('iiii', $halaman, $halaman, $userId, $bookId);
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Aksi tidak dikenal']);
}

