<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

$userId = (int) ($_SESSION['user_id'] ?? 1);
$q = '%' . trim($_GET['q'] ?? '') . '%';

$sql = "
    SELECT
        b.id,
        b.title,
        b.cover_url,
        b.description,
        b.publisher,
        b.published_year,
        b.isbn,
        b.page_count,
        ub.status,
        GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors
    FROM books b
    LEFT JOIN user_books ub ON ub.book_id = b.id AND ub.user_id = ?
    LEFT JOIN book_authors ba ON ba.book_id = b.id
    LEFT JOIN authors a ON a.id = ba.author_id
    WHERE b.title LIKE ?
       OR b.id IN (
            SELECT ba2.book_id FROM book_authors ba2
            JOIN authors a2 ON a2.id = ba2.author_id
            WHERE a2.name LIKE ?
       )
    GROUP BY b.id, b.title, b.cover_url, b.description, b.publisher,
             b.published_year, b.isbn, b.page_count, ub.status
    ORDER BY b.title
    LIMIT 50
";

$stmt = $connect->prepare($sql);
$stmt->bind_param('iss', $userId, $q, $q);
$stmt->execute();
$result = $stmt->get_result();

$hasil = [];
while ($row = $result->fetch_assoc()) {
    $hasil[] = $row;
}

echo json_encode($hasil);