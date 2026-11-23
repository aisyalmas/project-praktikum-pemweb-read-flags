<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    header("location: ../auth/login.php");
    exit();
}
$userId = (int) $_SESSION['user_id'];
// $userId = (int) ($_SESSION['user_id'] ?? 1); // DEMO ONLY
$tahun  = (int) date('Y');

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
 * POST: TUGAS, CHALLENGE
 */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $aksi = $data['action'] ?? '';

        if ($aksi === 'task-tambah') {
            $judul = trim($data['title'] ?? '');
            if ($judul === '') {
                http_response_code(400);
                echo json_encode(['error' => 'task cannot be empty']);
                exit;
            }

            $stmt = $connect->prepare("INSERT INTO reading_tasks (user_id, title) VALUES (?, ?)");
            $stmt->bind_param('is', $userId, $judul);
            $stmt->execute();

        } elseif ($aksi === 'task-centang') {
            $id = (int) ($data['id'] ?? 0);

            $stmt = $connect->prepare("UPDATE reading_tasks SET is_done = 1 - is_done WHERE id = ? AND user_id = ?");
            $stmt->bind_param('ii', $id, $userId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'task not found']);
                exit;
            }

        } elseif ($aksi === 'task-hapus') {
            $id = (int) ($data['id'] ?? 0);

            $stmt = $connect->prepare("DELETE FROM reading_tasks WHERE id = ? AND user_id = ?");
            $stmt->bind_param('ii', $id, $userId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'task not found']);
                exit;
            }

        } elseif ($aksi === 'challenge-simpan') {
            $nama   = trim($data['name'] ?? '');
            $target = (int) ($data['target'] ?? 0);
            if ($nama === '' || $target < 1) {
                http_response_code(400);
                echo json_encode(['error' => 'challenge name and target are required']);
                exit;
            }

            $stmt = $connect->prepare("SELECT id FROM challenges WHERE user_id = ? AND target_year = ? ORDER BY id DESC LIMIT 1");
            $stmt->bind_param('ii', $userId, $tahun);
            $stmt->execute();
            $ada = $stmt->get_result()->fetch_assoc();

            if ($ada) {
                $idChallenge = (int) $ada['id'];
                $stmt = $connect->prepare("UPDATE challenges SET name = ?, target_books = ? WHERE id = ?");
                $stmt->bind_param('sii', $nama, $target, $idChallenge);
            } else {
                $stmt = $connect->prepare("INSERT INTO challenges (user_id, name, target_books, target_year) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('isii', $userId, $nama, $target, $tahun);
            }
            $stmt->execute();

        } else {
            http_response_code(400);
            echo json_encode(['error' => 'unknown action']);
            exit;
        }

        echo json_encode(['success' => true]);
        exit;
    }

/**
 * GET: DATA DASHBOARD
 */

/**
 * semua buku
 */
    $stmt = $connect->prepare("
        SELECT b.id, b.title, b.cover_url, b.page_count, ub.current_page, ub.status, ub.added_at,
            (SELECT GROUP_CONCAT(a.name SEPARATOR ', ')
             FROM book_authors ba JOIN authors a ON a.id = ba.author_id
             WHERE ba.book_id = b.id) AS authors
        FROM user_books ub
        JOIN books b ON b.id = ub.book_id
        WHERE ub.user_id = ?
        ORDER BY ub.added_at DESC
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $buku = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/**
 * now reading
 * buku reading yang paling lama belum selesai
 */
    $stmt = $connect->prepare("
        SELECT b.id, b.title, b.cover_url, b.page_count, ub.current_page,
            (SELECT GROUP_CONCAT(a.name SEPARATOR ', ')
             FROM book_authors ba JOIN authors a ON a.id = ba.author_id
             WHERE ba.book_id = b.id) AS authors
        FROM user_books ub
        JOIN books b ON b.id = ub.book_id
        WHERE ub.user_id = ? AND ub.status = 'reading'
        ORDER BY COALESCE(ub.started_at, DATE(ub.added_at)) ASC
        LIMIT 1
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $bacaSekarang = $stmt->get_result()->fetch_assoc();

    $nowReading = null;
    if ($bacaSekarang) {
        $total   = (int) $bacaSekarang['page_count'];
        $halaman = (int) $bacaSekarang['current_page'];
        $nowReading = [
            'id'           => (int) $bacaSekarang['id'],
            'title'        => $bacaSekarang['title'],
            'cover_url'    => $bacaSekarang['cover_url'],
            'authors'      => $bacaSekarang['authors'],
            'page_count'   => $total,
            'current_page' => $halaman,
            'percent'      => $total > 0 ? (int) round($halaman / $total * 100) : 0
        ];
    }

/**
 * log baca per hari
 */
    $stmt = $connect->prepare("
        SELECT log_date AS tanggal, SUM(pages_read) AS halaman, SUM(minutes_read) AS menit
        FROM reading_logs
        WHERE user_id = ?
        GROUP BY log_date
        ORDER BY log_date DESC
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $logs = [];
    $peta = [];
    foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $row['halaman'] = (int) $row['halaman'];
        $row['menit']   = (int) $row['menit'];
        $logs[] = $row;
        $peta[$row['tanggal']] = $row;
    }

/**
 * streak
 */
    $streak = 0;
    $cek = new DateTime('today');
    if (!isset($peta[$cek->format('Y-m-d')])) {
        $cek->modify('-1 day');
    }
    while (isset($peta[$cek->format('Y-m-d')])) {
        $streak++;
        $cek->modify('-1 day');
    }

    $hariIni = $peta[date('Y-m-d')] ?? ['halaman' => 0, 'menit' => 0];

    // pace
    $mingguLalu  = date('Y-m-d', strtotime('-6 days'));
    $totalMinggu = 0;
    foreach ($logs as $l) {
        if ($l['tanggal'] >= $mingguLalu) {
            $totalMinggu += $l['halaman'];
        }
    }
    $pace = (int) round($totalMinggu / 7);

/**
 * reading task
 */
    $stmt = $connect->prepare("
        SELECT id, title, is_done
        FROM reading_tasks
        WHERE user_id = ?
        ORDER BY is_done ASC, id DESC
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $tasks = [];
    foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $row['id']      = (int) $row['id'];
        $row['is_done'] = (int) $row['is_done'] === 1;
        $tasks[] = $row;
    }

/**
 * challege taun ini
 */
    $stmt = $connect->prepare("
        SELECT id, name, target_books
        FROM challenges
        WHERE user_id = ? AND target_year = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->bind_param('ii', $userId, $tahun);
    $stmt->execute();
    $tantangan = $stmt->get_result()->fetch_assoc();

    $challenge = null;
    if ($tantangan) {
        $stmt = $connect->prepare("
            SELECT COUNT(*) AS jumlah
            FROM user_books
            WHERE user_id = ? AND status = 'read' AND YEAR(finished_at) = ?
        ");
        $stmt->bind_param('ii', $userId, $tahun);
        $stmt->execute();
        $selesai = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

        $challenge = [
            'name'      => $tantangan['name'],
            'target'    => (int) $tantangan['target_books'],
            'completed' => $selesai
        ];
    }

    echo json_encode([
        'buku'        => $buku,
        'now_reading' => $nowReading,
        'hari_ini'    => ['halaman' => (int) $hariIni['halaman'], 'menit' => (int) $hariIni['menit']],
        'pace'        => $pace,
        'streak'      => $streak,
        'logs'        => $logs,
        'tasks'       => $tasks,
        'challenge'   => $challenge
    ]);

} catch (Exception $e) {
    error_log('get_dashboard.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'server error: ' . $e->getMessage()]);
}