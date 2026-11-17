<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

$userId = (int) ($_SESSION['user_id'] ?? 1);

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
        b.genre_id,
        b.series_id,
        b.series_order,
        s.name AS series_name,
        g.name AS genre_name,
        ub.status,
        ub.current_page,
        ub.started_at,
        ub.finished_at,
        ub.added_at,
        r.rating,
        GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors,
        GROUP_CONCAT(DISTINCT c.name SEPARATOR '||') AS collections,
        GROUP_CONCAT(DISTINCT c.id) AS collection_ids
    FROM user_books ub
    JOIN books b ON b.id = ub.book_id
    LEFT JOIN series s ON s.id = b.series_id
    LEFT JOIN genres g ON g.id = b.genre_id
    LEFT JOIN book_authors ba ON ba.book_id = b.id
    LEFT JOIN authors a ON a.id = ba.author_id
    LEFT JOIN collection_books cb ON cb.book_id = b.id
    LEFT JOIN collections c ON c.id = cb.collection_id AND c.user_id = ub.user_id
    LEFT JOIN reviews r ON r.book_id = b.id AND r.user_id = ub.user_id
    WHERE ub.user_id = ?
    GROUP BY ub.id, b.id, s.name, g.name, r.rating
    ORDER BY b.title
";

$stmt = $connect->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $row['collections'] = $row['collections'] ? explode('||', $row['collections']) : [];
    $row['collection_ids'] = $row['collection_ids'] ? array_map('intval', explode(',', $row['collection_ids'])) : [];
    $books[] = $row;
}

echo json_encode($books);