<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

$userId = (int) ($_SESSION['user_id'] ?? 1);
$tipe   = $_GET['tipe'] ?? '';
$id     = (int) ($_GET['id'] ?? 0);

function ambilBuku($connect, $userId, $whereSql, $id)
{
    $sql = "
        SELECT
            b.id,
            b.title,
            b.cover_url,
            b.page_count,
            b.series_order,
            GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors,
            ub.status,
            ub.current_page,
            ub.started_at,
            ub.finished_at,
            r.rating
        FROM books b
        LEFT JOIN user_books ub ON ub.book_id = b.id AND ub.user_id = ?
        LEFT JOIN reviews r ON r.book_id = b.id AND r.user_id = ?
        LEFT JOIN book_authors ba ON ba.book_id = b.id
        LEFT JOIN authors a ON a.id = ba.author_id
        WHERE $whereSql
        GROUP BY b.id, b.title, b.cover_url, b.page_count, b.series_order,
                 ub.status, ub.current_page, ub.started_at, ub.finished_at, r.rating
        ORDER BY b.series_order, b.title
    ";

    $stmt = $connect->prepare($sql);
    $stmt->bind_param('iii', $userId, $userId, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $hasil = [];
    while ($row = $result->fetch_assoc()) {
        $hasil[] = $row;
    }
    return $hasil;
}

$info  = null;
$books = [];

if ($tipe === 'series') {
    $stmt = $connect->prepare("SELECT id, name, description FROM series WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $info = $stmt->get_result()->fetch_assoc();

    $books = ambilBuku($connect, $userId, 'b.series_id = ?', $id);

} elseif ($tipe === 'author') {
    $stmt = $connect->prepare("SELECT id, name, bio, nationality, birth_year FROM authors WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $info = $stmt->get_result()->fetch_assoc();

    $books = ambilBuku($connect, $userId, 'b.id IN (SELECT book_id FROM book_authors WHERE author_id = ?)', $id);

} elseif ($tipe === 'collection') {
    $stmt = $connect->prepare("SELECT id, name, description FROM collections WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $id, $userId);
    $stmt->execute();
    $info = $stmt->get_result()->fetch_assoc();

    $books = ambilBuku($connect, $userId, 'b.id IN (SELECT book_id FROM collection_books WHERE collection_id = ?)', $id);

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Tipe tidak dikenal']);
    exit;
}

if (!$info) {
    http_response_code(404);
    echo json_encode(['error' => 'Data tidak ditemukan']);
    exit;
}

echo json_encode(['info' => $info, 'books' => $books]);