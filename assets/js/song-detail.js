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
        { id: 1, title: "Midnight Rain", artist: "Luna Echo", emotion: "sad", cover: "🌧️", duration: "3:45" },
        { id: 2, title: "Summer Vibes", artist: "DJ Sunshine", emotion: "happy", cover: "☀️", duration: "3:20" },
        { id: 3, title: "Deep Thoughts", artist: "Mind Wave", emotion: "calm", cover: "🌊", duration: "4:12" },
        { id: 4, title: "Energy Burst", artist: "Power Pulse", emotion: "energetic", cover: "⚡", duration: "2:58" },
        { id: 5, title: "Lonely Nights", artist: "Soul Singer", emotion: "sad", cover: "🌙", duration: "4:30" },
        { id: 6, title: "Party Time", artist: "Beat Masters", emotion: "happy", cover: "🎉", duration: "3:15" },
        { id: 7, title: "Morning Peace", artist: "Zen Garden", emotion: "calm", cover: "🍃", duration: "5:00" },
        { id: 8, title: "Workout Mix", artist: "Fit Beats", emotion: "energetic", cover: "🏃", duration: "3:40" },
        { id: 9, title: "Heartbreak Blues", artist: "Emotion Express", emotion: "sad", cover: "💔", duration: "4:15" },
        { id: 10, title: "Feel Good", artist: "Happy Souls", emotion: "happy", cover: "😊", duration: "3:30" },
        { id: 11, title: "Meditation Flow", artist: "Inner Peace", emotion: "calm", cover: "🧘", duration: "6:00" },
        { id: 12, title: "Adrenaline Rush", artist: "Extreme Sports", emotion: "energetic", cover: "🎸", duration: "3:25" }
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