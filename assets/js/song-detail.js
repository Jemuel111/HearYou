// assets/js/song-detail.js - Song Detail Logic

// Load similar songs
async function loadSimilarSongs(emotion, currentSongId) {
    try {
        const response = await fetch(`api/songs.php?emotion=${emotion}`);
        const data = await response.json();
        
        if (data.success) {
            // Filter out current song and limit to 4 songs
            const similarSongs = data.songs
                .filter(song => song.id !== currentSongId)
                .slice(0, 4);
            
            displaySimilarSongs(similarSongs);
        }
    } catch (error) {
        console.error('Error loading similar songs:', error);
        loadDemoSimilarSongs(emotion, currentSongId);
    }
}

// Load demo similar songs
function loadDemoSimilarSongs(emotion, currentSongId) {
    const allSongs = [
        { id: 1, title: "Multo", artist: "Cup of Joe", emotion: "sad", cover: "👻", duration: "3:20" },
        { id: 2, title: "Tibok", artist: "Earl Agustin", emotion: "energetic", cover: "❤️", duration: "2:58" },
        { id: 3, title: "Marilag", artist: "Dionela", emotion: "happy", cover: "🌸", duration: "3:05" },
        { id: 4, title: "Sa Bawat Sandali", artist: "Amiel Sol", emotion: "calm", cover: "🌅", duration: "3:45" },
        { id: 5, title: "My Day", artist: "HELLMERRY", emotion: "energetic", cover: "🔥", duration: "3:10" },
        { id: 6, title: "Isa Lang", artist: "Arthur Nery", emotion: "sad", cover: "💧", duration: "3:33" },
        { id: 7, title: "Tingin", artist: "Cup of Joe & Janine", emotion: "happy", cover: "💖", duration: "3:15" },
        { id: 8, title: "Saksi Ang Langit", artist: "December Avenue", emotion: "sad", cover: "🌧️", duration: "4:02" },
        { id: 9, title: "Youll Be In My Heart", artist: "NIKI", emotion: "calm", cover: "💙", duration: "3:40" },
        { id: 10, title: "Back to Friends", artist: "Sombr", emotion: "calm", cover: "🤍", duration: "3:25" },
        { id: 11, title: "Palagi", artist: "TJ Monterde & KZ Tandingan", emotion: "happy", cover: "🌞", duration: "3:50" },
        { id: 12, title: "Dilaw", artist: "Maki", emotion: "happy", cover: "🌼", duration: "3:22" },
        { id: 13, title: "Blink Twice", artist: "BINI", emotion: "energetic", cover: "✨", duration: "2:50" },
        { id: 14, title: "DAM", artist: "SB19", emotion: "energetic", cover: "⚡️", duration: "3:30" },
        { id: 15, title: "Time", artist: "SB19", emotion: "calm", cover: "🕰️", duration: "3:45" },
        { id: 16, title: "Dungka!", artist: "SB19", emotion: "energetic", cover: "🎶", duration: "3:35" },
        { id: 17, title: "Karera", artist: "BINI", emotion: "energetic", cover: "🏁", duration: "3:10" },
        { id: 18, title: "Pantropiko", artist: "BINI", emotion: "happy", cover: "🇵🇭", duration: "3:27" },
        { id: 19, title: "Salamin, Salamin", artist: "BINI", emotion: "calm", cover: "🪞", duration: "3:45" },
        { id: 20, title: "Sining", artist: "Dionela & Jay R", emotion: "sad", cover: "🎨", duration: "3:55" },
        { id: 21, title: "Walang Alam", artist: "Hev Abi", emotion: "sad", cover: "😔", duration: "3:30" },
        { id: 22, title: "Babaero", artist: "gins&melodies & Hev Abi", emotion: "sad", cover: "💔", duration: "3:40" },
        { id: 23, title: "Makasarili Malambing", artist: "Kristina Dawn & Hev Abi", emotion: "sad", cover: "🌙", duration: "3:50" },
        { id: 24, title: "Take All The Love", artist: "Arthur Nery", emotion: "calm", cover: "🤲", duration: "3:35" },
        { id: 25, title: "You and I", artist: "Various Artists", emotion: "happy", cover: "💞", duration: "3:40" },
        { id: 26, title: "Lets Go", artist: "Various Artists", emotion: "energetic", cover: "🚀", duration: "3:15" },
        { id: 27, title: "Night Drive", artist: "Various Artists", emotion: "calm", cover: "🌙", duration: "4:00" },
        { id: 28, title: "Sunrise", artist: "Various Artists", emotion: "happy", cover: "🌄", duration: "3:35" },
        { id: 29, title: "Heartbeat", artist: "Various Artists", emotion: "energetic", cover: "💓", duration: "3:20" },
        { id: 30, title: "Quiet Moments", artist: "Various Artists", emotion: "calm", cover: "🌿", duration: "4:10" }

    ];
    
    const similarSongs = allSongs
        .filter(song => song.emotion === emotion && song.id !== currentSongId)
        .slice(0, 4);
    
    displaySimilarSongs(similarSongs);
}

// Display similar songs
function displaySimilarSongs(songs) {
    const container = document.getElementById('similarSongs');
    container.innerHTML = '';
    
    if (songs.length === 0) {
        container.innerHTML = '<p style="color: var(--gray-400); text-align: center;">No similar songs found</p>';
        return;
    }
    
    songs.forEach(song => {
        const songCard = document.createElement('div');
        songCard.className = 'similar-song-card';
        songCard.onclick = () => window.location.href = `song-detail.php?id=${song.id}`;
        
        songCard.innerHTML = `
            <div class="similar-song-cover">${song.cover}</div>
            <div class="similar-song-title">${song.title}</div>
            <div class="similar-song-artist">${song.artist}</div>
        `;
        
        container.appendChild(songCard);
    });
}

// Smooth scroll to sections
document.addEventListener('DOMContentLoaded', function() {
    // Add intersection observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all content sections
    document.querySelectorAll('.content-section').forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(section);
    });
});

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Space bar to play/pause
    if (e.code === 'Space' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        document.querySelector('.btn-play-large')?.click();
    }
    
    // F key to favorite
    if (e.code === 'KeyF' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        document.querySelector('.btn-favorite-large')?.click();
    }
    
    // S key to share
    if (e.code === 'KeyS' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        document.querySelector('.btn-share')?.click();
    }
    
    // Escape to go back
    if (e.code === 'Escape') {
        window.history.back();
    }
});