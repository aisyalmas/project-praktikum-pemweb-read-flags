-- 01_database.sql
-- skema database readflags: 14 tabel, foreign key, index
-- jalankan pertama kali
-- ini menghapus tabel lama lalu buat ulang

CREATE DATABASE IF NOT EXISTS readflags
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE readflags;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS collection_books;
DROP TABLE IF EXISTS collections;
DROP TABLE IF EXISTS challenge_books;
DROP TABLE IF EXISTS challenges;
DROP TABLE IF EXISTS reading_tasks;
DROP TABLE IF EXISTS reading_logs;
DROP TABLE IF EXISTS user_books;
DROP TABLE IF EXISTS book_authors;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS series;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bio VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    bio TEXT DEFAULT NULL,
    nationality VARCHAR(100) DEFAULT NULL,
    birth_year SMALLINT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS series (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_series_users
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_id VARCHAR(255) NOT NULL UNIQUE,
    title VARCHAR(500) NOT NULL,
    description TEXT DEFAULT NULL,
    cover_url TEXT DEFAULT NULL,
    publisher VARCHAR(255) DEFAULT NULL,
    published_year SMALLINT DEFAULT NULL,
    isbn VARCHAR(20) DEFAULT NULL,
    page_count SMALLINT UNSIGNED DEFAULT NULL,
    genre_id INT NOT NULL DEFAULT 1,
    series_id INT DEFAULT NULL,
    series_order DECIMAL(4,1) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_books_title (title),
    INDEX idx_books_isbn (isbn),

    CONSTRAINT fk_books_genres
        FOREIGN KEY (genre_id) REFERENCES genres(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_books_series
        FOREIGN KEY (series_id) REFERENCES series(id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS book_authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    author_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_book_author (book_id, author_id),
    CONSTRAINT fk_ba_books
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_ba_authors
        FOREIGN KEY (author_id) REFERENCES authors(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    status ENUM('reading','read','tbr','dnf','wishlist') NOT NULL DEFAULT 'tbr',
    current_page SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    started_at DATE DEFAULT NULL,
    finished_at DATE DEFAULT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_read_at DATETIME DEFAULT NULL,

    UNIQUE KEY uq_user_book (user_id, book_id),
    CONSTRAINT fk_ub_users
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_ub_books
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reading_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    log_date DATE NOT NULL,
    pages_read SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    minutes_read SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_log_per_day (user_id, book_id, log_date),
    CONSTRAINT fk_logs_users
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_logs_books
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reading_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    is_done TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_tasks_users
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    target_books SMALLINT UNSIGNED NOT NULL,
    target_year SMALLINT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_challenges_users
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS challenge_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_id INT NOT NULL,
    book_id INT NOT NULL,
    UNIQUE KEY uq_challenge_book (challenge_id, book_id),
    CONSTRAINT fk_cb_challenges
        FOREIGN KEY (challenge_id) REFERENCES challenges(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_cb_books
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_collection_name (user_id, name),
    CONSTRAINT fk_collections_users
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS collection_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    collection_id INT NOT NULL,
    book_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_collection_book (collection_id, book_id),
    CONSTRAINT fk_colbooks_collections
        FOREIGN KEY (collection_id) REFERENCES collections(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_colbooks_books
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    content TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5),
    CONSTRAINT uq_user_review UNIQUE (user_id, book_id),
    CONSTRAINT fk_reviews_users
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_reviews_books
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);
