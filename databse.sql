-- database.sql - Database Schema for HearYou

CREATE DATABASE IF NOT EXISTS hearyou_db;
USE hearyou_db;

-- Songs Table
CREATE TABLE IF NOT EXISTS songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    emotion ENUM('sad', 'happy', 'calm', 'energetic') NOT NULL,
    cover VARCHAR(10) DEFAULT '🎵',
    duration VARCHAR(10) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    album VARCHAR(255),
    release_year INT,
    genre VARCHAR(100),
    play_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_emotion (emotion),
    INDEX idx_artist (artist)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Users Table (for future authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    profile_picture VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Favorites Table
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    song_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, song_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Playlists Table
CREATE TABLE IF NOT EXISTS playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    cover_image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Playlist Songs Table
CREATE TABLE IF NOT EXISTS playlist_songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    playlist_id INT NOT NULL,
    song_id INT NOT NULL,
    position INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_playlist_song (playlist_id, song_id),
    INDEX idx_playlist_id (playlist_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Listening History Table
CREATE TABLE IF NOT EXISTS listening_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    song_id INT NOT NULL,
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duration_played INT DEFAULT 0,
    completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    INDEX idx_user_song (user_id, song_id),
    INDEX idx_played_at (played_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chat History Table
CREATE TABLE IF NOT EXISTS chat_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    role ENUM('user', 'assistant') NOT NULL,
    detected_emotion ENUM('sad', 'happy', 'calm', 'energetic'),
    recommended_song_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recommended_song_id) REFERENCES songs(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO songs (id, title, artist, emotion, cover, duration, album, release_year, genre)
VALUES
(1, 'Multo', 'Cup of Joe', 'sad', '👻', '3:20', 'Ghost Stories', 2025, 'OPM'),
(2, 'Tibok', 'Earl Agustin', 'energetic', '❤️', '2:58', 'Heartbeat', 2025, 'Pop'),
(3, 'Marilag', 'Dionela', 'happy', '🌸', '3:05', 'Spring Vibes', 2025, 'Pop'),
(4, 'Sa Bawat Sandali', 'Amiel Sol', 'calm', '🌅', '3:45', 'Evening Calm', 2024, 'Ballad'),
(5, 'My Day', 'HELLMERRY', 'energetic', '🔥', '3:10', 'Rise Up', 2025, 'Pop Rock'),
(6, 'Isa Lang', 'Arthur Nery', 'sad', '💧', '3:33', 'Heartfelt', 2024, 'R&B'),
(7, 'Tingin', 'Cup of Joe & Janine', 'happy', '💖', '3:15', 'Eyes on You', 2025, 'Pop'),
(8, 'Saksi Ang Langit', 'December Avenue', 'sad', '🌧️', '4:02', 'Sky Witness', 2024, 'Alternative Rock'),
(9, 'Youll Be In My Heart', 'NIKI', 'calm', '💙', '3:40', 'Blue Heart', 2024, 'R&B'),
(10, 'Back to Friends', 'Sombr', 'calm', '🤍', '3:25', 'Chill Nights', 2025, 'Lo-Fi'),
(11, 'Palagi', 'TJ Monterde & KZ Tandingan', 'happy', '🌞', '3:50', 'Always', 2024, 'Pop'),
(12, 'Dilaw', 'Maki', 'happy', '🌼', '3:22', 'Yellow Days', 2025, 'Pop'),
(13, 'Blink Twice', 'BINI', 'energetic', '✨', '2:50', 'Shining', 2025, 'Dance Pop'),
(14, 'DAM', 'SB19', 'energetic', '⚡️', '3:30', 'Power Moves', 2025, 'Pop'),
(15, 'Time', 'SB19', 'calm', '🕰️', '3:45', 'Timeless', 2024, 'Ballad'),
(16, 'Dungka!', 'SB19', 'energetic', '🎶', '3:35', 'Rhythm', 2025, 'Pop'),
(17, 'Karera', 'BINI', 'energetic', '🏁', '3:10', 'Race', 2025, 'Dance Pop'),
(18, 'Pantropiko', 'BINI', 'happy', '🇵🇭', '3:27', 'Patriotic Beats', 2025, 'Pop'),
(19, 'Salamin, Salamin', 'BINI', 'calm', '🪞', '3:45', 'Mirror', 2025, 'Pop'),
(20, 'Sining', 'Dionela & Jay R', 'sad', '🎨', '3:55', 'Artistry', 2024, 'R&B'),
(21, 'Walang Alam', 'Hev Abi', 'sad', '😔', '3:30', 'Lost Thoughts', 2025, 'OPM'),
(22, 'Babaero', 'gins&melodies & Hev Abi', 'sad', '💔', '3:40', 'Cheating Heart', 2025, 'OPM'),
(23, 'Makasarili Malambing', 'Kristina Dawn & Hev Abi', 'sad', '🌙', '3:50', 'Selfish Love', 2025, 'Ballad'),
(24, 'Take All The Love', 'Arthur Nery', 'calm', '🤲', '3:35', 'Love All', 2024, 'R&B'),
(25, 'You and I', 'Various Artists', 'happy', '💞', '3:40', 'Duets', 2025, 'Pop'),
(26, 'Lets Go', 'Various Artists', 'energetic', '🚀', '3:15', 'Launch', 2025, 'Dance'),
(27, 'Night Drive', 'Various Artists', 'calm', '🌙', '4:00', 'Midnight Ride', 2024, 'Chill'),
(28, 'Sunrise', 'Various Artists', 'happy', '🌄', '3:35', 'New Dawn', 2025, 'Pop'),
(29, 'Heartbeat', 'Various Artists', 'energetic', '💓', '3:20', 'Pulse', 2025, 'Pop'),
(30, 'Quiet Moments', 'Various Artists', 'calm', '🌿', '4:10', 'Serenity', 2024, 'Chill');