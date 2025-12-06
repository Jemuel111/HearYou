// assets/js/player.js - Fixed Music Player Controls

window.currentSongData = null;
window.isPlaying = false;
window.currentProgress = 0;
window.currentVolume = 66; // Default 66%
let progressInterval = null;

// Load song in player
function loadSongInPlayer(song) {
    window.currentSongData = song;
    window.currentProgress = 0;
    
    // Show player
    const player = document.getElementById('musicPlayer');
    player.style.display = 'flex';
    
    // Update player info
    document.getElementById('playerCover').textContent = song.cover;
    document.getElementById('playerTitle').textContent = song.title;
    document.getElementById('playerArtist').textContent = song.artist;
    document.getElementById('timeTotal').textContent = song.duration;
    
    // Reset progress
    updateProgressBar(0);
    document.querySelector('.time-current').textContent = '0:00';
    
    // Update favorite button
    updatePlayerFavoriteButton();
    
    // Auto play
    window.isPlaying = true;
    updatePlayButton();
    
    // Start progress simulation
    startProgressSimulation();
}

// Update player favorite button
function updatePlayerFavoriteButton() {
    const favoriteBtn = document.querySelector('.player .favorite-btn');
    if (!favoriteBtn || !window.currentSongData) return;
    
    const isFavorite = favorites.includes(window.currentSongData.id);
    
    if (isFavorite) {
        favoriteBtn.classList.add('active');
    } else {
        favoriteBtn.classList.remove('active');
    }
    
    const svg = favoriteBtn.querySelector('svg');
    svg.setAttribute('fill', isFavorite ? 'currentColor' : 'none');
}

// Toggle play/pause
function togglePlay() {
    window.isPlaying = !window.isPlaying;
    updatePlayButton();
    
    if (window.isPlaying) {
        startProgressSimulation();
    } else {
        stopProgressSimulation();
    }
}

// Update play button icon
function updatePlayButton() {
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    
    if (window.isPlaying) {
        playIcon.style.display = 'none';
        pauseIcon.style.display = 'block';
    } else {
        playIcon.style.display = 'block';
        pauseIcon.style.display = 'none';
    }
}

// Toggle favorite from player
function toggleFavorite() {
    if (window.currentSongData) {
        toggleFavoriteFromList(window.currentSongData.id);
    }
}

// Play next song
function nextSong() {
    if (!window.currentSongData) return;
    
    const filteredSongs = getFilteredSongs();
    const currentIndex = filteredSongs.findIndex(s => s.id === window.currentSongData.id);
    const nextIndex = (currentIndex + 1) % filteredSongs.length;
    const nextSong = filteredSongs[nextIndex];
    
    loadSongInPlayer(nextSong);
}

// Play previous song
function previousSong() {
    if (!window.currentSongData) return;
    
    const filteredSongs = getFilteredSongs();
    const currentIndex = filteredSongs.findIndex(s => s.id === window.currentSongData.id);
    const prevIndex = (currentIndex - 1 + filteredSongs.length) % filteredSongs.length;
    const prevSong = filteredSongs[prevIndex];
    
    loadSongInPlayer(prevSong);
}

// Start progress simulation
function startProgressSimulation() {
    // Clear any existing interval
    stopProgressSimulation();
    
    if (!window.currentSongData) return;
    
    const duration = parseDuration(window.currentSongData.duration);
    const incrementPerSecond = 100 / duration;
    
    progressInterval = setInterval(() => {
        if (!window.isPlaying) return;
        
        window.currentProgress += incrementPerSecond;
        
        if (window.currentProgress >= 100) {
            window.currentProgress = 100;
            stopProgressSimulation();
            // Auto play next song
            setTimeout(() => {
                nextSong();
            }, 500);
            return;
        }
        
        updateProgressBar(window.currentProgress);
        updateCurrentTime();
    }, 1000);
}

// Stop progress simulation
function stopProgressSimulation() {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
}

// Update progress bar
function updateProgressBar(progress) {
    const progressFill = document.getElementById('progressFill');
    if (progressFill) {
        progressFill.style.width = progress + '%';
    }
}

// Update current time display
function updateCurrentTime() {
    if (!window.currentSongData) return;
    
    const duration = parseDuration(window.currentSongData.duration);
    const currentSeconds = Math.floor((window.currentProgress / 100) * duration);
    const timeCurrent = document.querySelector('.time-current');
    if (timeCurrent) {
        timeCurrent.textContent = formatTime(currentSeconds);
    }
}

// Parse duration string to seconds
function parseDuration(durationStr) {
    const parts = durationStr.split(':');
    return parseInt(parts[0]) * 60 + parseInt(parts[1]);
}

// Format seconds to MM:SS
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

// Initialize player controls
document.addEventListener('DOMContentLoaded', function() {
    // Progress bar click to seek
    const progressBar = document.querySelector('.progress');
    if (progressBar) {
        progressBar.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percentage = (clickX / rect.width) * 100;
            window.currentProgress = Math.max(0, Math.min(100, percentage));
            updateProgressBar(window.currentProgress);
            updateCurrentTime();
        });
    }
    
    // Volume bar click to adjust
    const volumeBar = document.querySelector('.volume-bar');
    if (volumeBar) {
        volumeBar.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percentage = (clickX / rect.width) * 100;
            window.currentVolume = Math.max(0, Math.min(100, percentage));
            updateVolumeBar(window.currentVolume);
        });
    }
    
    // Initialize volume bar
    updateVolumeBar(window.currentVolume);
});

// Update volume bar
function updateVolumeBar(volume) {
    const volumeFill = document.querySelector('.volume-fill');
    if (volumeFill) {
        volumeFill.style.width = volume + '%';
    }
}