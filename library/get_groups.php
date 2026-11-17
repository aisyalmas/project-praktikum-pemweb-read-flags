<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

$userId = (int) ($_SESSION['user_id'] ?? 1);

$seriesSql = "
    SELECT
        s.id,
        s.name,
        s.description,
        COUNT(DISTINCT b.id) AS total_buku,
        COUNT(DISTINCT ub.book_id) AS dimiliki,
        GROUP_CONCAT(DISTINCT b.cover_url SEPARATOR '||') AS covers
    FROM series s
    LEFT JOIN books b ON b.series_id = s.id
    LEFT JOIN user_books ub ON ub.book_id = b.id AND ub.user_id = ?
    GROUP BY s.id, s.name, s.description
    ORDER BY s.name
";

$authorsSql = "
    SELECT
        a.id,
        a.name,
        a.bio,
        a.nationality,
        a.birth_year,
        COUNT(DISTINCT ba.book_id) AS total_buku,
        COUNT(DISTINCT ub.book_id) AS dimiliki
    FROM authors a
    LEFT JOIN book_authors ba ON ba.author_id = a.id
    LEFT JOIN user_books ub ON ub.book_id = ba.book_id AND ub.user_id = ?
    GROUP BY a.id, a.name, a.bio, a.nationality, a.birth_year
    ORDER BY a.name
";

$collectionsSql = "
    SELECT
        c.id,
        c.name,
        c.description,
        COUNT(cb.book_id) AS total_buku,
        COALESCE(SUM(CASE WHEN ub.status = 'read' THEN 1 ELSE 0 END), 0) AS terbaca
    FROM collections c
    LEFT JOIN collection_books cb ON cb.collection_id = c.id
    LEFT JOIN user_books ub ON ub.book_id = cb.book_id AND ub.user_id = c.user_id
    WHERE c.user_id = ?
    GROUP BY c.id, c.name, c.description
    ORDER BY c.name
";

$genres = $connect->query("SELECT id, name FROM genres ORDER BY name")->fetch_all(MYSQLI_ASSOC);

function ambilSemua($connect, $sql, $userId)
{
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $hasil = [];
    while ($row = $result->fetch_assoc()) {
        $hasil[] = $row;
    }
    return $hasil;
}

echo json_encode([
    'series'      => ambilSemua($connect, $seriesSql, $userId),
    'authors'     => ambilSemua($connect, $authorsSql, $userId),
    'collections' => ambilSemua($connect, $collectionsSql, $userId),
    'genres'      => $genres,
]);