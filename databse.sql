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

-- Insert Sample Songs
INSERT INTO songs (title, artist, emotion, cover, duration, file_path) VALUES
('Midnight Rain', 'Luna Echo', 'sad', '🌧️', '3:45', 'songs/midnight_rain.mp3'),
('Summer Vibes', 'DJ Sunshine', 'happy', '☀️', '3:20', 'songs/summer_vibes.mp3'),
('Deep Thoughts', 'Mind Wave', 'calm', '🌊', '4:12', 'songs/deep_thoughts.mp3'),
('Energy Burst', 'Power Pulse', 'energetic', '⚡', '2:58', 'songs/energy_burst.mp3'),
('Lonely Nights', 'Soul Singer', 'sad', '🌙', '4:30', 'songs/lonely_nights.mp3'),
('Party Time', 'Beat Masters', 'happy', '🎉', '3:15', 'songs/party_time.mp3'),
('Morning Peace', 'Zen Garden', 'calm', '🍃', '5:00', 'songs/morning_peace.mp3'),
('Workout Mix', 'Fit Beats', 'energetic', '🏃', '3:40', 'songs/workout_mix.mp3'),
('Heartbreak Blues', 'Emotion Express', 'sad', '💔', '4:15', 'songs/heartbreak_blues.mp3'),
('Feel Good', 'Happy Souls', 'happy', '😊', '3:30', 'songs/feel_good.mp3'),
('Meditation Flow', 'Inner Peace', 'calm', '🧘', '6:00', 'songs/meditation_flow.mp3'),
('Adrenaline Rush', 'Extreme Sports', 'energetic', '🎸', '3:25', 'songs/adrenaline_rush.mp3');