<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("location: login.php");
    exit();
}
$userId = (int) $_SESSION['user_id'];
// $userId = (int) ($_SESSION['user_id'] ?? 1); // DEMO ONLY

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // cek user di database
    $cekUser = $connect->query("SELECT id FROM users WHERE id = $userId");
    if ($cekUser->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => "user $userId not found, check your demo data"]);
        exit;
    }

/**
 * GET
 */
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $data = $_GET['data'] ?? '';

        /**
         * daftar buku user
         */
        if ($data === 'books') {
            $stmt = $connect->prepare("
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
            ");
            $stmt->bind_param('i', $userId);
            $stmt->execute();

            $books = [];
            foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
                $row['collections'] = $row['collections'] ? explode('||', $row['collections']) : [];
                $row['collection_ids'] = $row['collection_ids'] ? array_map('intval', explode(',', $row['collection_ids'])) : [];
                $books[] = $row;
            }
            echo json_encode($books);

        /**
         * library: series, author, collection
         */
        } elseif ($data === 'groups') {
            $stmt = $connect->prepare("
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
            ");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $series = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $stmt = $connect->prepare("
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
            ");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $authors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $stmt = $connect->prepare("
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
            ");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $collections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $genres = $connect->query("SELECT id, name FROM genres ORDER BY name")->fetch_all(MYSQLI_ASSOC);

            echo json_encode([
                'series'      => $series,
                'authors'     => $authors,
                'collections' => $collections,
                'genres'      => $genres,
            ]);

        /**
         * detail: series, suthor, collection
         */
        } elseif ($data === 'detail') {
            $tipe = $_GET['tipe'] ?? '';
            $id   = (int) ($_GET['id'] ?? 0);

            if ($tipe === 'series') {
                $stmt = $connect->prepare("SELECT id, name, description FROM series WHERE id = ?");
                $stmt->bind_param('i', $id);
                $whereSql = 'b.series_id = ?';
            } elseif ($tipe === 'author') {
                $stmt = $connect->prepare("SELECT id, name, bio, nationality, birth_year FROM authors WHERE id = ?");
                $stmt->bind_param('i', $id);
                $whereSql = 'b.id IN (SELECT book_id FROM book_authors WHERE author_id = ?)';
            } elseif ($tipe === 'collection') {
                $stmt = $connect->prepare("SELECT id, name, description FROM collections WHERE id = ? AND user_id = ?");
                $stmt->bind_param('ii', $id, $userId);
                $whereSql = 'b.id IN (SELECT book_id FROM collection_books WHERE collection_id = ?)';
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'unknown type']);
                exit;
            }

            $stmt->execute();
            $info = $stmt->get_result()->fetch_assoc();
            if (!$info) {
                http_response_code(404);
                echo json_encode(['error' => 'data not found']);
                exit;
            }

            $stmt = $connect->prepare("
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
            ");
            $stmt->bind_param('iii', $userId, $userId, $id);
            $stmt->execute();
            $books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            echo json_encode(['info' => $info, 'books' => $books]);

        /**
         * search modal (add books)
         */
        } elseif ($data === 'search') {
            $q = '%' . trim($_GET['q'] ?? '') . '%';

            $stmt = $connect->prepare("
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
            ");
            $stmt->bind_param('iss', $userId, $q, $q);
            $stmt->execute();
            echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));

        } else {
            http_response_code(400);
            echo json_encode(['error' => 'unknown data type']);
        }
    }

/**
 * POST
 */
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $aksi   = $data['action'] ?? '';
        $bookId = (int) ($data['book_id'] ?? 0);
        $idBaru = null;

        $connect->begin_transaction();

        /**
         * add ke rak: detail series, author, collection
         */
        if ($aksi === 'rak') {
            $status = $data['status'] ?? '';
            if (!in_array($status, ['tbr', 'wishlist'], true)) {
                http_response_code(400);
                echo json_encode(['error' => 'invalid status']);
                exit;
            }

            $stmt = $connect->prepare("
                INSERT INTO user_books (user_id, book_id, status) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE id = id
            ");
            $stmt->bind_param('iis', $userId, $bookId, $status);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                http_response_code(409);
                echo json_encode(['error' => 'this book is already on your shelf']);
                exit;
            }

        /**
         * save status
         */
        } elseif ($aksi === 'status') {
            $status = $data['status'] ?? '';
            if (!in_array($status, ['reading', 'read', 'tbr', 'dnf', 'wishlist'], true)) {
                http_response_code(400);
                echo json_encode(['error' => 'invalid status']);
                exit;
            }

            $stmt = $connect->prepare("
                INSERT INTO user_books (user_id, book_id, status) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status)
            ");
            $stmt->bind_param('iis', $userId, $bookId, $status);
            $stmt->execute();

            if ($status === 'reading' || $status === 'read') {
                $stmt = $connect->prepare("
                    UPDATE user_books SET started_at = COALESCE(started_at, CURDATE())
                    WHERE user_id = ? AND book_id = ?
                ");
                $stmt->bind_param('ii', $userId, $bookId);
                $stmt->execute();
            }

            if ($status === 'read') {
                $stmt = $connect->prepare("
                    UPDATE user_books SET finished_at = COALESCE(finished_at, CURDATE())
                    WHERE user_id = ? AND book_id = ?
                ");
                $stmt->bind_param('ii', $userId, $bookId);
                $stmt->execute();
            }

            $idKoleksi = (int) ($data['collection_id'] ?? 0);
            if ($idKoleksi > 0) {
                $stmt = $connect->prepare("
                    INSERT INTO collection_books (collection_id, book_id)
                    SELECT id, ? FROM collections WHERE id = ? AND user_id = ?
                    ON DUPLICATE KEY UPDATE collection_id = collection_id
                ");
                $stmt->bind_param('iii', $bookId, $idKoleksi, $userId);
                $stmt->execute();
            }

        /**
         * add books ke collection
         */
        } elseif ($aksi === 'koleksi-tambah') {
            $idKoleksi = (int) ($data['collection_id'] ?? 0);

            $stmt = $connect->prepare("SELECT id FROM collections WHERE id = ? AND user_id = ?");
            $stmt->bind_param('ii', $idKoleksi, $userId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'collection not found']);
                exit;
            }

            $stmt = $connect->prepare("
                INSERT INTO collection_books (collection_id, book_id) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE collection_id = collection_id
            ");
            $stmt->bind_param('ii', $idKoleksi, $bookId);
            $stmt->execute();

        /**
         * delete book collection
         */
        } elseif ($aksi === 'koleksi-hapus') {
            $idKoleksi = (int) ($data['collection_id'] ?? 0);

            $stmt = $connect->prepare("
                DELETE cb FROM collection_books cb
                JOIN collections c ON c.id = cb.collection_id
                WHERE cb.collection_id = ? AND cb.book_id = ? AND c.user_id = ?
            ");
            $stmt->bind_param('iii', $idKoleksi, $bookId, $userId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'this book is not in that collection']);
                exit;
            }

        /**
         * log pages
         */
        } elseif ($aksi === 'log-harian') {
            $halaman = max(0, (int) ($data['pages'] ?? 0));
            $menit   = max(0, (int) ($data['minutes'] ?? 0));

            if ($halaman === 0 && $menit === 0) {
                http_response_code(400);
                echo json_encode(['error' => 'enter pages or minutes first']);
                exit;
            }

            $stmt = $connect->prepare("SELECT id FROM user_books WHERE user_id = ? AND book_id = ?");
            $stmt->bind_param('ii', $userId, $bookId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'add this book to your shelf first']);
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

        /**
         * save detail buku
         */
        } elseif ($aksi === 'simpan-buku') {
            $stmt = $connect->prepare("SELECT id FROM user_books WHERE user_id = ? AND book_id = ?");
            $stmt->bind_param('ii', $userId, $bookId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'add this book to your shelf first']);
                exit;
            }

            $status = $data['status'] ?? '';
            if (!in_array($status, ['reading', 'read', 'tbr', 'dnf', 'wishlist'], true)) {
                http_response_code(400);
                echo json_encode(['error' => 'invalid status']);
                exit;
            }

            $rating = (int) ($data['rating'] ?? 0);
            if ($rating < 0 || $rating > 5) {
                http_response_code(400);
                echo json_encode(['error' => 'rating must be 0 to 5']);
                exit;
            }

            $totalHalaman = max(0, (int) ($data['page_count'] ?? 0));
            $halamanBaca  = max(0, (int) ($data['current_page'] ?? 0));
            if ($totalHalaman > 0 && $halamanBaca > $totalHalaman) {
                $halamanBaca = $totalHalaman;
            }

            $mulai   = trim($data['started_at'] ?? '') ?: null;
            $selesai = trim($data['finished_at'] ?? '') ?: null;
            if (($mulai && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai)) ||
                ($selesai && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai))) {
                http_response_code(400);
                echo json_encode(['error' => 'invalid date format']);
                exit;
            }
            if ($mulai && $selesai && $selesai < $mulai) {
                http_response_code(400);
                echo json_encode(['error' => 'finish date cannot be before start date']);
                exit;
            }

            $deskripsi = trim($data['description'] ?? '') ?: null;
            $genreId   = (int) ($data['genre_id'] ?? 1);

            // series
            $seriId     = null;
            $urutanSeri = null;
            $seriBaru   = trim($data['series_baru'] ?? '');
            if ($seriBaru !== '') {
                $stmt = $connect->prepare("
                    INSERT INTO series (name, created_by) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
                ");
                $stmt->bind_param('si', $seriBaru, $userId);
                $stmt->execute();
                $seriId = $connect->insert_id;
            } elseif (!empty($data['series_id'])) {
                $seriId = (int) $data['series_id'];
            }
            if ($seriId !== null && ($data['series_order'] ?? '') !== '') {
                $urutanSeri = (float) $data['series_order'];
            }

            // books 
            $stmt = $connect->prepare("
                UPDATE books
                SET page_count = ?, description = ?, genre_id = ?, series_id = ?, series_order = ?
                WHERE id = ?
            ");
            $stmt->bind_param('isiidi', $totalHalaman, $deskripsi, $genreId, $seriId, $urutanSeri, $bookId);
            $stmt->execute();

            $stmt = $connect->prepare("
                UPDATE user_books
                SET status = ?, current_page = ?, started_at = ?, finished_at = ?
                WHERE user_id = ? AND book_id = ?
            ");
            $stmt->bind_param('sissii', $status, $halamanBaca, $mulai, $selesai, $userId, $bookId);
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

            // koleksi
            $stmt = $connect->prepare("
                DELETE cb FROM collection_books cb
                JOIN collections c ON c.id = cb.collection_id
                WHERE c.user_id = ? AND cb.book_id = ?
            ");
            $stmt->bind_param('ii', $userId, $bookId);
            $stmt->execute();

            foreach (array_map('intval', (array) ($data['collection_ids'] ?? [])) as $idKoleksi) {
                $stmt = $connect->prepare("
                    INSERT INTO collection_books (collection_id, book_id)
                    SELECT id, ? FROM collections WHERE id = ? AND user_id = ?
                    ON DUPLICATE KEY UPDATE collection_id = collection_id
                ");
                $stmt->bind_param('iii', $bookId, $idKoleksi, $userId);
                $stmt->execute();
            }

            foreach ((array) ($data['collection_baru'] ?? []) as $namaKoleksi) {
                $namaKoleksi = trim((string) $namaKoleksi);
                if ($namaKoleksi === '') {
                    continue;
                }

                $stmt = $connect->prepare("
                    INSERT INTO collections (user_id, name) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
                ");
                $stmt->bind_param('is', $userId, $namaKoleksi);
                $stmt->execute();
                $idKoleksiBaru = $connect->insert_id;

                $stmt = $connect->prepare("
                    INSERT INTO collection_books (collection_id, book_id) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE collection_id = collection_id
                ");
                $stmt->bind_param('ii', $idKoleksiBaru, $bookId);
                $stmt->execute();
            }

        /**
         * delete buku dari library
         */
        } elseif ($aksi === 'hapus-buku') {
            $stmt = $connect->prepare("
                DELETE cb FROM collection_books cb
                JOIN collections c ON c.id = cb.collection_id
                WHERE c.user_id = ? AND cb.book_id = ?
            ");
            $stmt->bind_param('ii', $userId, $bookId);
            $stmt->execute();

            $stmt = $connect->prepare("DELETE FROM reviews WHERE user_id = ? AND book_id = ?");
            $stmt->bind_param('ii', $userId, $bookId);
            $stmt->execute();

            $stmt = $connect->prepare("DELETE FROM reading_logs WHERE user_id = ? AND book_id = ?");
            $stmt->bind_param('ii', $userId, $bookId);
            $stmt->execute();

            $stmt = $connect->prepare("DELETE FROM user_books WHERE user_id = ? AND book_id = ?");
            $stmt->bind_param('ii', $userId, $bookId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'this book is not on your shelf']);
                exit;
            }

        } elseif ($aksi === 'series-baru') {
            $nama = trim($data['name'] ?? '');
            if ($nama === '') {
                http_response_code(400);
                echo json_encode(['error' => 'series name is required']);
                exit;
            }
            $deskripsi = trim($data['description'] ?? '') ?: null;

            $stmt = $connect->prepare("
                INSERT INTO series (name, description, created_by) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
            ");
            $stmt->bind_param('ssi', $nama, $deskripsi, $userId);
            $stmt->execute();
            $idBaru = $connect->insert_id;

        } elseif ($aksi === 'koleksi-baru') {
            $nama = trim($data['name'] ?? '');
            if ($nama === '') {
                http_response_code(400);
                echo json_encode(['error' => 'collection name is required']);
                exit;
            }
            $deskripsi = trim($data['description'] ?? '') ?: null;

            $stmt = $connect->prepare("
                INSERT INTO collections (user_id, name, description) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
            ");
            $stmt->bind_param('iss', $userId, $nama, $deskripsi);
            $stmt->execute();
            $idBaru = $connect->insert_id;

        } elseif ($aksi === 'series-hapus') {
            $seriesId = (int) ($data['series_id'] ?? 0);

            $stmt = $connect->prepare("SELECT COUNT(*) AS jumlah FROM books WHERE series_id = ?");
            $stmt->bind_param('i', $seriesId);
            $stmt->execute();
            $jumlah = (int) $stmt->get_result()->fetch_assoc()['jumlah'];
            if ($jumlah > 0) {
                http_response_code(400);
                echo json_encode(['error' => 'series still has books, empty it first']);
                exit;
            }

            $stmt = $connect->prepare("DELETE FROM series WHERE id = ?");
            $stmt->bind_param('i', $seriesId);
            $stmt->execute();

        } elseif ($aksi === 'koleksi-hapus-semua') {
            $idKoleksi = (int) ($data['collection_id'] ?? 0);

            $stmt = $connect->prepare("DELETE FROM collections WHERE id = ? AND user_id = ?");
            $stmt->bind_param('ii', $idKoleksi, $userId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'collection not found']);
                exit;
            }

        } else {
            http_response_code(400);
            echo json_encode(['error' => 'unknown action']);
            exit;
        }

        $connect->commit();

        $respon = ['success' => true];
        if ($idBaru !== null) {
            $respon['id'] = $idBaru;
        }
        echo json_encode($respon);
    }

    else {
        http_response_code(405);
        echo json_encode(['error' => 'method not allowed']);
    }

} catch (Exception $e) {
    $connect->rollback();
    error_log('library/api.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'server error: ' . $e->getMessage()]);
}