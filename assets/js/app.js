// assets/js/app.js - Enhanced Application Logic

let allSongs = [];
let currentFilter = null;
let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
let searchQuery = '';

// Initialize the app
document.addEventListener('DOMContentLoaded', function() {
    loadSongs();
    initializeEventListeners();
    
    // Show loading state
    showLoadingState();
});

// Show loading state
function showLoadingState() {
    const container = document.getElementById('allSongsList');
    if (container) {
        container.innerHTML = '<div class="loading-state"><div class="spinner"></div><p>Loading your music...</p></div>';
    }
}

// Load songs from API
async function loadSongs() {
    try {
        const response = await fetch('api/songs.php');
        const data = await response.json();
        
        if (data.success) {
            allSongs = data.songs;
            displayAllSongs();
            
            // Show success notification if demo mode
            if (data.demo_mode) {
                console.log('Running in demo mode with sample data');
            }
        }
    } catch (error) {
        console.error('Error loading songs:', error);
        // Fallback to demo data if API fails
        loadDemoData();
    }
}

// Fallback demo data
function loadDemoData() {
    allSongs = [
        { id: 1, title: "Midnight Rain", artist: "Luna Echo", emotion: "sad", cover: "🌧️", duration: "3:45", album: "Night Sessions" },
        { id: 2, title: "Summer Vibes", artist: "DJ Sunshine", emotion: "happy", cover: "☀️", duration: "3:20", album: "Bright Days" },
        { id: 3, title: "Deep Thoughts", artist: "Mind Wave", emotion: "calm", cover: "🌊", duration: "4:12", album: "Meditation" },
        { id: 4, title: "Energy Burst", artist: "Power Pulse", emotion: "energetic", cover: "⚡", duration: "2:58", album: "Pump Up" },
        { id: 5, title: "Lonely Nights", artist: "Soul Singer", emotion: "sad", cover: "🌙", duration: "4:30", album: "Heartbreak Hotel" },
        { id: 6, title: "Party Time", artist: "Beat Masters", emotion: "happy", cover: "🎉", duration: "3:15", album: "Dance Floor" },
        { id: 7, title: "Morning Peace", artist: "Zen Garden", emotion: "calm", cover: "🍃", duration: "5:00", album: "Tranquility" },
        { id: 8, title: "Workout Mix", artist: "Fit Beats", emotion: "energetic", cover: "🏃", duration: "3:40", album: "Gym Motivation" },
        { id: 9, title: "Heartbreak Blues", artist: "Emotion Express", emotion: "sad", cover: "💔", duration: "4:15", album: "Tears & Rain" },
        { id: 10, title: "Feel Good", artist: "Happy Souls", emotion: "happy", cover: "😊", duration: "3:30", album: "Positive Vibes" },
        { id: 11, title: "Meditation Flow", artist: "Inner Peace", emotion: "calm", cover: "🧘", duration: "6:00", album: "Zen Masters" },
        { id: 12, title: "Adrenaline Rush", artist: "Extreme Sports", emotion: "energetic", cover: "🎸", duration: "3:25", album: "High Octane" }
    ];
    displayAllSongs();
}

// Display all songs
function displayAllSongs() {
    const container = document.getElementById('allSongsList');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (allSongs.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No songs available</p></div>';
        return;
    }
    
    allSongs.forEach(song => {
        container.appendChild(createSongElement(song));
    });
}

// Create song element with enhanced details
function createSongElement(song) {
    const songDiv = document.createElement('div');
    songDiv.className = 'song-item';
    songDiv.dataset.songId = song.id;
    
    // Make song title clickable
    songDiv.style.cursor = 'pointer';
    
    const isFavorite = favorites.includes(song.id);
    
    songDiv.innerHTML = `
        <div class="song-info">
            <div class="song-cover">${song.cover}</div>
            <div class="song-details">
                <div class="song-title">${song.title}</div>
                <div class="song-artist">${song.artist}</div>
            </div>
        </div>
        <div class="song-actions">
            <span class="song-duration">${song.duration}</span>
            <button class="favorite-btn ${isFavorite ? 'active' : ''}" onclick="event.stopPropagation(); toggleFavoriteFromList(${song.id})" title="Add to favorites">
                <svg viewBox="0 0 24 24" fill="${isFavorite ? 'currentColor' : 'none'}" stroke="currentColor">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2"/>
                </svg>
            </button>
            <button class="play-song-btn" onclick="event.stopPropagation(); playSongFromList(${song.id})" title="Play song">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="5 3 19 12 5 21 5 3"/>
                </svg>
            </button>
        </div>
    `;
    
    // Add click listener to entire song item
    songDiv.addEventListener('click', function(e) {
        // Don't navigate if clicking on buttons
        if (!e.target.closest('.favorite-btn') && !e.target.closest('.play-song-btn')) {
            goToSongDetail(song.id);
        }
    });
    
    return songDiv;
}

// Navigate to song detail page
function goToSongDetail(songId) {
    window.location.href = `song-detail.php?id=${songId}`;
}

// Filter songs by mood
function filterByMood(emotion) {
    currentFilter = emotion;
    searchQuery = ''; // Clear search when filtering
    
    // Update active state on sidebar buttons
    document.querySelectorAll('.mood-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    const activeBtn = document.querySelector(`.mood-btn.${emotion}`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
    
    // Show filtered view
    showView('filtered');
    
    // Filter songs
    const filteredSongs = allSongs.filter(song => song.emotion === emotion);
    
    // Update header
    const emotionEmojis = {
        sad: '😢',
        happy: '😊',
        calm: '😌',
        energetic: '⚡'
    };
    
    const emotionLabels = {
        sad: 'Sad',
        happy: 'Happy',
        calm: 'Calm',
        energetic: 'Energetic'
    };
    
    document.getElementById('filteredHeader').innerHTML = `
        <h2>${emotionEmojis[emotion]} ${emotionLabels[emotion]} Songs</h2>
        <p class="subtitle">Songs that match your ${emotion} mood • ${filteredSongs.length} tracks</p>
    `;
    
    // Display filtered songs
    const container = document.getElementById('filteredSongsList');
    container.innerHTML = '';
    
    if (filteredSongs.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No songs found for this mood</p></div>';
        return;
    }
    
    filteredSongs.forEach(song => {
        container.appendChild(createSongElement(song));
    });
}

// Search functionality
function performSearch() {
    const searchInput = document.getElementById('searchInput');
    searchQuery = searchInput.value.toLowerCase().trim();
    
    if (!searchQuery) {
        displayAllSongs();
        return;
    }
    
    const searchResults = allSongs.filter(song => 
        song.title.toLowerCase().includes(searchQuery) ||
        song.artist.toLowerCase().includes(searchQuery) ||
        (song.album && song.album.toLowerCase().includes(searchQuery)) ||
        song.emotion.toLowerCase().includes(searchQuery)
    );
    
    // Update UI
    const container = document.getElementById('searchResultsList');
    container.innerHTML = '';
    
    document.getElementById('searchResultsHeader').innerHTML = `
        <h2>Search Results</h2>
        <p class="subtitle">Found ${searchResults.length} ${searchResults.length === 1 ? 'song' : 'songs'} for "${searchQuery}"</p>
    `;
    
    if (searchResults.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 64px; height: 64px; margin-bottom: 1rem;">
                    <circle cx="11" cy="11" r="8" stroke-width="2"/>
                    <path d="m21 21-4.35-4.35" stroke-width="2"/>
                </svg>
                <h3>No results found</h3>
                <p>Try searching for a different song, artist, or mood</p>
            </div>
        `;
        return;
    }
    
    searchResults.forEach(song => {
        container.appendChild(createSongElement(song));
    });
}

// Show library with favorites
function showLibrary() {
    // Show library view
    showView('library');
    
    // Load all library sections
    loadFavorites();
    loadPlaylists();
    loadRecentlyPlayed();
    loadMostPlayed();
    updateLibraryStats();
}

// Update library statistics
function updateLibraryStats() {
    // Update total songs count
    const totalSongsEl = document.getElementById('totalSongsCount');
    if (totalSongsEl) totalSongsEl.textContent = allSongs.length;
    
    // Update favorites count
    const favoritesCountEl = document.getElementById('favoritesCount');
    if (favoritesCountEl) favoritesCountEl.textContent = favorites.length;
    
    // Update playlists count
    const playlists = JSON.parse(localStorage.getItem('playlists')) || [];
    const playlistsCountEl = document.getElementById('playlistsCount');
    if (playlistsCountEl) playlistsCountEl.textContent = playlists.length;
    
    // Update time listened (demo data)
    const hoursListenedEl = document.getElementById('hoursListened');
    if (hoursListenedEl) {
        const hours = Math.floor(Math.random() * 100);
        hoursListenedEl.textContent = `${hours}h`;
    }
}

// Load favorites section
function loadFavorites() {
    const favoriteSongs = allSongs.filter(song => favorites.includes(song.id));
    
    const container = document.getElementById('favoritesList');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (favoriteSongs.length === 0) {
        container.innerHTML = `
            <div class="empty-state-small">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.5;">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2"/>
                </svg>
                <p>No favorites yet</p>
                <small>Songs you like will appear here</small>
            </div>
        `;
        return;
    }
    
    favoriteSongs.slice(0, 6).forEach(song => {
        container.appendChild(createLibraryCard(song));
    });
}

// Load playlists
function loadPlaylists() {
    const container = document.getElementById('playlistsGrid');
    if (!container) return;
    
    // Get or create playlists from localStorage
    let playlists = JSON.parse(localStorage.getItem('playlists')) || [];
    
    // Create default playlists if none exist
    if (playlists.length === 0) {
        playlists = [
            { id: 1, name: 'My Mix', cover: '🎵', songCount: 12, color: '#9333ea' },
            { id: 2, name: 'Chill Vibes', cover: '🌊', songCount: 8, color: '#22c55e' },
            { id: 3, name: 'Workout', cover: '💪', songCount: 15, color: '#ef4444' },
            { id: 4, name: 'Focus', cover: '🎯', songCount: 10, color: '#3b82f6' }
        ];
        localStorage.setItem('playlists', JSON.stringify(playlists));
    }
    
    container.innerHTML = '';
    
    playlists.forEach(playlist => {
        const card = document.createElement('div');
        card.className = 'playlist-card';
        card.onclick = () => alert('Playlist feature coming soon!');
        
        card.innerHTML = `
            <div class="playlist-cover" style="background: linear-gradient(135deg, ${playlist.color}, ${playlist.color}dd);">${playlist.cover}</div>
            <div class="playlist-info">
                <div class="playlist-name">${playlist.name}</div>
                <div class="playlist-count">${playlist.songCount} songs</div>
            </div>
        `;
        
        container.appendChild(card);
    });
    
    // Add "Create Playlist" card
    const createCard = document.createElement('div');
    createCard.className = 'playlist-card create-playlist';
    createCard.onclick = () => createNewPlaylist();
    createCard.innerHTML = `
        <div class="playlist-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="12" y1="5" x2="12" y2="19" stroke-width="2"/>
                <line x1="5" y1="12" x2="19" y2="12" stroke-width="2"/>
            </svg>
        </div>
        <div class="playlist-info">
            <div class="playlist-name">Create Playlist</div>
        </div>
    `;
    container.appendChild(createCard);
}

// Load recently played
function loadRecentlyPlayed() {
    const container = document.getElementById('recentlyPlayedList');
    if (!container) return;
    
    let recentlyPlayed = JSON.parse(localStorage.getItem('recentlyPlayed')) || [];
    
    // If empty, add some demo data
    if (recentlyPlayed.length === 0 && allSongs.length > 0) {
        recentlyPlayed = [3, 7, 1, 5].map(id => allSongs.find(s => s.id === id)).filter(Boolean);
    }
    
    container.innerHTML = '';
    
    if (recentlyPlayed.length === 0) {
        container.innerHTML = `
            <div class="empty-state-small">
                <p>No recently played songs</p>
            </div>
        `;
        return;
    }
    
    recentlyPlayed.slice(0, 6).forEach(song => {
        container.appendChild(createLibraryCard(song));
    });
}

// Load most played
function loadMostPlayed() {
    const container = document.getElementById('mostPlayedList');
    if (!container) return;
    
    // Demo: Use some random songs
    const mostPlayed = [2, 6, 10, 4].map(id => allSongs.find(s => s.id === id)).filter(Boolean);
    
    container.innerHTML = '';
    
    if (mostPlayed.length === 0) {
        container.innerHTML = `
            <div class="empty-state-small">
                <p>No play history yet</p>
            </div>
        `;
        return;
    }
    
    mostPlayed.forEach(song => {
        container.appendChild(createLibraryCard(song));
    });
}

// Create library card
function createLibraryCard(song) {
    const card = document.createElement('div');
    card.className = 'library-card';
    card.onclick = () => goToSongDetail(song.id);
    
    const isFavorite = favorites.includes(song.id);
    
    card.innerHTML = `
        <div class="library-card-cover">${song.cover}</div>
        <div class="library-card-info">
            <div class="library-card-title">${song.title}</div>
            <div class="library-card-artist">${song.artist}</div>
        </div>
        <button class="library-card-play" onclick="event.stopPropagation(); playSongFromList(${song.id})" title="Play">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <polygon points="5 3 19 12 5 21 5 3"/>
            </svg>
        </button>
    `;
    
    return card;
}

// Create new playlist
function createNewPlaylist() {
    const name = prompt('Enter playlist name:');
    if (!name) return;
    
    const playlists = JSON.parse(localStorage.getItem('playlists')) || [];
    const colors = ['#9333ea', '#22c55e', '#ef4444', '#3b82f6', '#f59e0b', '#ec4899'];
    const emojis = ['🎵', '🎶', '🎧', '🎸', '🎹', '🎤', '🎺', '🥁'];
    
    const newPlaylist = {
        id: Date.now(),
        name: name,
        cover: emojis[Math.floor(Math.random() * emojis.length)],
        songCount: 0,
        color: colors[Math.floor(Math.random() * colors.length)]
    };
    
    playlists.push(newPlaylist);
    localStorage.setItem('playlists', JSON.stringify(playlists));
    
    loadPlaylists();
    updateLibraryStats();
}

// Show all favorites
function showAllFavorites() {
    const favoriteSongs = allSongs.filter(song => favorites.includes(song.id));
    
    if (favoriteSongs.length === 0) {
        alert('You have no favorite songs yet! Start adding songs to your favorites.');
        return;
    }
    
    // Show filtered view with favorites
    showView('filtered');
    
    document.getElementById('filteredHeader').innerHTML = `
        <h2>❤️ All Favorite Songs</h2>
        <p class="subtitle">Your liked music collection • ${favoriteSongs.length} tracks</p>
    `;
    
    const container = document.getElementById('filteredSongsList');
    container.innerHTML = '';
    
    favoriteSongs.forEach(song => {
        container.appendChild(createSongElement(song));
    });
}

// Show all playlists
function showAllPlaylists() {
    const playlists = JSON.parse(localStorage.getItem('playlists')) || [];
    
    if (playlists.length === 0) {
        alert('You have no playlists yet! Create your first playlist to organize your music.');
        return;
    }
    
    alert('Full playlist management coming soon! You can view and edit all your playlists here.');
}

// Show all recently played
function showAllRecentlyPlayed() {
    let recentlyPlayed = JSON.parse(localStorage.getItem('recentlyPlayed')) || [];
    
    // If empty, add demo data
    if (recentlyPlayed.length === 0 && allSongs.length > 0) {
        recentlyPlayed = [3, 7, 1, 5, 2, 9].map(id => allSongs.find(s => s.id === id)).filter(Boolean);
    }
    
    if (recentlyPlayed.length === 0) {
        alert('No recently played songs yet! Start listening to build your history.');
        return;
    }
    
    showView('filtered');
    
    document.getElementById('filteredHeader').innerHTML = `
        <h2>🕐 Recently Played</h2>
        <p class="subtitle">Your listening history • ${recentlyPlayed.length} tracks</p>
    `;
    
    const container = document.getElementById('filteredSongsList');
    container.innerHTML = '';
    
    recentlyPlayed.forEach(song => {
        container.appendChild(createSongElement(song));
    });
}

// Show all most played
function showAllMostPlayed() {
    // Demo: Use some random songs
    const mostPlayed = [2, 6, 10, 4, 8, 12, 1, 5].map(id => allSongs.find(s => s.id === id)).filter(Boolean);
    
    if (mostPlayed.length === 0) {
        alert('No play history yet! Songs you listen to frequently will appear here.');
        return;
    }
    
    showView('filtered');
    
    document.getElementById('filteredHeader').innerHTML = `
        <h2>🔥 Most Played</h2>
        <p class="subtitle">Your top tracks • ${mostPlayed.length} songs</p>
    `;
    
    const container = document.getElementById('filteredSongsList');
    container.innerHTML = '';
    
    mostPlayed.forEach(song => {
        container.appendChild(createSongElement(song));
    });
}

// Show different views
function showView(viewName) {
    // Hide all views
    document.querySelectorAll('.view').forEach(view => {
        view.classList.remove('active');
    });
    
    // Show selected view
    const targetView = document.getElementById(viewName + 'View');
    if (targetView) {
        targetView.classList.add('active');
    }
    
    // Update nav buttons
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Handle specific view logic
    if (viewName === 'home') {
        document.querySelector('.nav-btn').classList.add('active');
        document.querySelectorAll('.mood-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        currentFilter = null;
        searchQuery = '';
        displayAllSongs();
    } else if (viewName === 'search') {
        document.querySelectorAll('.nav-btn')[1].classList.add('active');
        setTimeout(() => {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.focus();
        }, 100);
    } else if (viewName === 'library') {
        document.querySelectorAll('.nav-btn')[2].classList.add('active');
        showLibrary();
    }
}

// Toggle favorite from list
function toggleFavoriteFromList(songId) {
    const index = favorites.indexOf(songId);
    
    if (index > -1) {
        favorites.splice(index, 1);
    } else {
        favorites.push(songId);
    }
    
    localStorage.setItem('favorites', JSON.stringify(favorites));
    
    // Update UI
    const buttons = document.querySelectorAll(`[data-song-id="${songId}"] .favorite-btn`);
    buttons.forEach(btn => {
        btn.classList.toggle('active');
        const svg = btn.querySelector('svg');
        svg.setAttribute('fill', favorites.includes(songId) ? 'currentColor' : 'none');
    });
    
    // Update player favorite button if this song is playing
    if (window.currentSongData && window.currentSongData.id === songId) {
        updatePlayerFavoriteButton();
    }
    
    // Refresh library if we're in library view
    const libraryView = document.getElementById('libraryView');
    if (libraryView && libraryView.classList.contains('active')) {
        showLibrary();
    }
}

// Play song from list
function playSongFromList(songId) {
    const song = allSongs.find(s => s.id === songId);
    if (song) {
        loadSongInPlayer(song);
    }
}

// Get filtered songs for player navigation
function getFilteredSongs() {
    if (currentFilter) {
        return allSongs.filter(song => song.emotion === currentFilter);
    }
    if (searchQuery) {
        return allSongs.filter(song => 
            song.title.toLowerCase().includes(searchQuery) ||
            song.artist.toLowerCase().includes(searchQuery)
        );
    }
    return allSongs;
}

// Initialize event listeners
function initializeEventListeners() {
    // Search input listener
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    // Chat input listener
    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
}

// Toggle chat panel
function toggleChat() {
    const chatPanel = document.getElementById('chatPanel');
    chatPanel.classList.toggle('active');
}