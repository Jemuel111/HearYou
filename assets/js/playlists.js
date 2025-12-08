// assets/js/playlists.js - Playlist Management

let userPlaylists = [];
let currentPlaylistId = null;

// Load user playlists
async function loadUserPlaylists() {
    try {
        const response = await fetch('api/playlists/manage.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            userPlaylists = data.playlists;
            displayPlaylists();
            return userPlaylists;
        }
    } catch (error) {
        console.error('Error loading playlists:', error);
    }
    return [];
}

// Display playlists in library
function displayPlaylists() {
    const container = document.getElementById('playlistsGrid');
    if (!container) return;
    
    container.innerHTML = '';
    
    // Add "Create Playlist" card
    const createCard = document.createElement('div');
    createCard.className = 'playlist-card create-playlist';
    createCard.onclick = () => showCreatePlaylistModal();
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
    
    // Add user playlists
    userPlaylists.forEach(playlist => {
        const card = createPlaylistCard(playlist);
        container.appendChild(card);
    });
}

// Create playlist card element
function createPlaylistCard(playlist) {
    const card = document.createElement('div');
    card.className = 'playlist-card';
    card.onclick = () => viewPlaylist(playlist.id);
    
    const colors = ['#9333ea', '#22c55e', '#ef4444', '#3b82f6', '#f59e0b', '#ec4899'];
    const emojis = ['🎵', '🎶', '🎧', '🎸', '🎹', '🎤', '🎺', '🥁'];
    const color = colors[playlist.id % colors.length];
    const emoji = emojis[playlist.id % emojis.length];
    
    card.innerHTML = `
        <div class="playlist-cover" style="background: linear-gradient(135deg, ${color}, ${color}dd);">${emoji}</div>
        <div class="playlist-info">
            <div class="playlist-name">${escapeHtml(playlist.name)}</div>
            <div class="playlist-count">${playlist.song_count || 0} songs</div>
        </div>
        <div class="playlist-actions">
            <button class="playlist-action-btn" onclick="event.stopPropagation(); editPlaylist(${playlist.id})" title="Edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-width="2"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="2"/>
                </svg>
            </button>
            <button class="playlist-action-btn" onclick="event.stopPropagation(); deletePlaylist(${playlist.id})" title="Delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <polyline points="3 6 5 6 21 6" stroke-width="2"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-width="2"/>
                </svg>
            </button>
        </div>
    `;
    
    return card;
}

// Show create playlist modal
function showCreatePlaylistModal() {
    const modal = document.getElementById('playlistModal');
    const form = document.getElementById('playlistForm');
    
    document.getElementById('modalTitle').textContent = 'Create New Playlist';
    document.getElementById('playlistId').value = '';
    document.getElementById('playlistName').value = '';
    document.getElementById('playlistDescription').value = '';
    document.getElementById('playlistPublic').checked = false;
    
    modal.style.display = 'flex';
}

// Show edit playlist modal
async function editPlaylist(playlistId) {
    try {
        const response = await fetch(`api/playlists/manage.php?action=get&id=${playlistId}`);
        const data = await response.json();
        
        if (data.success) {
            const playlist = data.playlist;
            
            document.getElementById('modalTitle').textContent = 'Edit Playlist';
            document.getElementById('playlistId').value = playlist.id;
            document.getElementById('playlistName').value = playlist.name;
            document.getElementById('playlistDescription').value = playlist.description || '';
            document.getElementById('playlistPublic').checked = playlist.is_public == 1;
            
            const modal = document.getElementById('playlistModal');
            modal.style.display = 'flex';
        }
    } catch (error) {
        console.error('Error loading playlist:', error);
        showNotification('Failed to load playlist', 'error');
    }
}

// Save playlist (create or update)
async function savePlaylist(event) {
    event.preventDefault();
    
    const playlistId = document.getElementById('playlistId').value;
    const name = document.getElementById('playlistName').value.trim();
    const description = document.getElementById('playlistDescription').value.trim();
    const isPublic = document.getElementById('playlistPublic').checked;
    
    if (!name) {
        showNotification('Please enter a playlist name', 'error');
        return;
    }
    
    const action = playlistId ? 'update' : 'create';
    const payload = { name, description, is_public: isPublic };
    
    if (playlistId) {
        payload.id = playlistId;
    }
    
    try {
        const response = await fetch(`api/playlists/manage.php?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            closePlaylistModal();
            await loadUserPlaylists();
            updateLibraryStats();
        } else {
            showNotification(data.message || 'Failed to save playlist', 'error');
        }
    } catch (error) {
        console.error('Error saving playlist:', error);
        showNotification('Failed to save playlist', 'error');
    }
}

// Delete playlist
async function deletePlaylist(playlistId) {
    if (!confirm('Are you sure you want to delete this playlist? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('api/playlists/manage.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: playlistId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            await loadUserPlaylists();
            updateLibraryStats();
        } else {
            showNotification(data.message || 'Failed to delete playlist', 'error');
        }
    } catch (error) {
        console.error('Error deleting playlist:', error);
        showNotification('Failed to delete playlist', 'error');
    }
}

// View playlist details
async function viewPlaylist(playlistId) {
    try {
        const response = await fetch(`api/playlists/manage.php?action=get&id=${playlistId}`);
        const data = await response.json();
        
        if (data.success) {
            currentPlaylistId = playlistId;
            displayPlaylistView(data.playlist);
        }
    } catch (error) {
        console.error('Error loading playlist:', error);
        showNotification('Failed to load playlist', 'error');
    }
}

// Display playlist view
function displayPlaylistView(playlist) {
    showView('playlist');
    
    const colors = ['#9333ea', '#22c55e', '#ef4444', '#3b82f6', '#f59e0b', '#ec4899'];
    const color = colors[playlist.id % colors.length];
    
    document.getElementById('playlistViewHeader').innerHTML = `
        <div class="playlist-view-hero" style="background: linear-gradient(135deg, ${color}33, transparent);">
            <div class="playlist-view-cover" style="background: linear-gradient(135deg, ${color}, ${color}dd);">
                🎵
            </div>
            <div class="playlist-view-info">
                <div class="playlist-view-type">Playlist</div>
                <h2 class="playlist-view-title">${escapeHtml(playlist.name)}</h2>
                <p class="playlist-view-description">${escapeHtml(playlist.description || 'No description')}</p>
                <div class="playlist-view-meta">
                    <span>${playlist.song_count || 0} songs</span>
                    ${playlist.is_public == 1 ? '<span>• Public</span>' : '<span>• Private</span>'}
                </div>
                <div class="playlist-view-actions">
                    <button class="btn btn-primary" onclick="playPlaylist(${playlist.id})">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        Play All
                    </button>
                    <button class="btn btn-secondary" onclick="editPlaylist(${playlist.id})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-width="2"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="2"/>
                        </svg>
                        Edit
                    </button>
                    <button class="btn btn-secondary" onclick="shufflePlaylist(${playlist.id})">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="16 3 21 3 21 8" stroke-width="2"/>
                            <line x1="4" y1="20" x2="21" y2="3" stroke-width="2"/>
                            <polyline points="21 16 21 21 16 21" stroke-width="2"/>
                            <line x1="15" y1="15" x2="21" y2="21" stroke-width="2"/>
                            <line x1="4" y1="4" x2="9" y2="9" stroke-width="2"/>
                        </svg>
                        Shuffle
                    </button>
                </div>
            </div>
        </div>
    `;
    
    const container = document.getElementById('playlistViewSongs');
    container.innerHTML = '';
    
    if (!playlist.songs || playlist.songs.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 64px; height: 64px; margin-bottom: 1rem; opacity: 0.3;">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/>
                    <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
                </svg>
                <h3>This playlist is empty</h3>
                <p>Add songs from your library to get started</p>
            </div>
        `;
        return;
    }
    
    playlist.songs.forEach((song, index) => {
        const songElement = createPlaylistSongElement(song, index, playlist.id);
        container.appendChild(songElement);
    });
}

// Create playlist song element
function createPlaylistSongElement(song, index, playlistId) {
    const songDiv = document.createElement('div');
    songDiv.className = 'song-item';
    songDiv.dataset.songId = song.id;
    
    const isFavorite = favorites.includes(song.id);
    
    songDiv.innerHTML = `
        <div class="song-index">${index + 1}</div>
        <div class="song-info" onclick="goToSongDetail(${song.id})">
            <div class="song-cover">${song.cover}</div>
            <div class="song-details">
                <div class="song-title">${escapeHtml(song.title)}</div>
                <div class="song-artist">${escapeHtml(song.artist)}</div>
            </div>
        </div>
        <div class="song-actions">
            <span class="song-duration">${song.duration}</span>
            <button class="favorite-btn ${isFavorite ? 'active' : ''}" onclick="toggleFavoriteFromList(${song.id})" title="Add to favorites">
                <svg viewBox="0 0 24 24" fill="${isFavorite ? 'currentColor' : 'none'}" stroke="currentColor">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2"/>
                </svg>
            </button>
            <button class="remove-from-playlist-btn" onclick="removeFromPlaylist(${playlistId}, ${song.id})" title="Remove from playlist">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <line x1="18" y1="6" x2="6" y2="18" stroke-width="2"/>
                    <line x1="6" y1="6" x2="18" y2="18" stroke-width="2"/>
                </svg>
            </button>
            <button class="play-song-btn" onclick="playSongFromList(${song.id})" title="Play song">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="5 3 19 12 5 21 5 3"/>
                </svg>
            </button>
        </div>
    `;
    
    return songDiv;
}

// Add song to playlist
async function addToPlaylist(songId, playlistId = null) {
    // If no playlist specified, show selection modal
    if (playlistId === null) {
        showAddToPlaylistModal(songId);
        return;
    }
    
    try {
        const response = await fetch('api/playlists/manage.php?action=add_song', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ playlist_id: playlistId, song_id: songId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Refresh if viewing this playlist
            if (currentPlaylistId === playlistId) {
                viewPlaylist(playlistId);
            }
        } else {
            showNotification(data.message || 'Failed to add song', 'error');
        }
    } catch (error) {
        console.error('Error adding song:', error);
        showNotification('Failed to add song to playlist', 'error');
    }
}

// Remove song from playlist
async function removeFromPlaylist(playlistId, songId) {
    if (!confirm('Remove this song from the playlist?')) {
        return;
    }
    
    try {
        const response = await fetch('api/playlists/manage.php?action=remove_song', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ playlist_id: playlistId, song_id: songId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            viewPlaylist(playlistId);
        } else {
            showNotification(data.message || 'Failed to remove song', 'error');
        }
    } catch (error) {
        console.error('Error removing song:', error);
        showNotification('Failed to remove song', 'error');
    }
}

// Play entire playlist
async function playPlaylist(playlistId) {
    try {
        const response = await fetch(`api/playlists/manage.php?action=get&id=${playlistId}`);
        const data = await response.json();
        
        if (data.success && data.playlist.songs && data.playlist.songs.length > 0) {
            const firstSong = data.playlist.songs[0];
            loadSongInPlayer(firstSong);
            showNotification(`Playing ${data.playlist.name}`, 'success');
        }
    } catch (error) {
        console.error('Error playing playlist:', error);
    }
}

// Shuffle playlist
async function shufflePlaylist(playlistId) {
    try {
        const response = await fetch(`api/playlists/manage.php?action=get&id=${playlistId}`);
        const data = await response.json();
        
        if (data.success && data.playlist.songs && data.playlist.songs.length > 0) {
            const songs = [...data.playlist.songs];
            const shuffled = songs.sort(() => Math.random() - 0.5);
            const firstSong = shuffled[0];
            loadSongInPlayer(firstSong);
            showNotification('Playing shuffled playlist', 'success');
        }
    } catch (error) {
        console.error('Error shuffling playlist:', error);
    }
}

// Show add to playlist modal
function showAddToPlaylistModal(songId) {
    const modal = document.getElementById('addToPlaylistModal');
    const list = document.getElementById('playlistSelectionList');
    
    list.innerHTML = '';
    
    if (userPlaylists.length === 0) {
        list.innerHTML = '<p style="text-align: center; color: var(--gray-400); padding: 2rem;">No playlists yet. Create one first!</p>';
    } else {
        userPlaylists.forEach(playlist => {
            const item = document.createElement('div');
            item.className = 'playlist-selection-item';
            item.onclick = () => {
                addToPlaylist(songId, playlist.id);
                closeAddToPlaylistModal();
            };
            
            item.innerHTML = `
                <div class="playlist-selection-cover">🎵</div>
                <div class="playlist-selection-info">
                    <div class="playlist-selection-name">${escapeHtml(playlist.name)}</div>
                    <div class="playlist-selection-count">${playlist.song_count || 0} songs</div>
                </div>
            `;
            
            list.appendChild(item);
        });
    }
    
    modal.dataset.songId = songId;
    modal.style.display = 'flex';
}

// Close modals
function closePlaylistModal() {
    document.getElementById('playlistModal').style.display = 'none';
}

function closeAddToPlaylistModal() {
    document.getElementById('addToPlaylistModal').style.display = 'none';
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    // Load playlists if logged in
    if (typeof isLoggedIn !== 'undefined' && isLoggedIn) {
        loadUserPlaylists();
    }
    
    // Add event listeners
    const playlistForm = document.getElementById('playlistForm');
    if (playlistForm) {
        playlistForm.addEventListener('submit', savePlaylist);
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        const playlistModal = document.getElementById('playlistModal');
        const addModal = document.getElementById('addToPlaylistModal');
        
        if (event.target === playlistModal) {
            closePlaylistModal();
        }
        if (event.target === addModal) {
            closeAddToPlaylistModal();
        }
    };
});