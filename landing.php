<?php
// landing.php - Beautiful Landing Page
session_start();

// If already logged in, redirect to app
if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HearYou - Discover Music That Matches Your Mood</title>
    <meta name="description" content="HearYou is an AI-powered music streaming platform that recommends songs based on your emotions. Find the perfect soundtrack for every moment.">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M9 18V5l12-2v13M9 18l-7 2V7l7-2M9 18l7 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>HearYou</span>
            </div>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#pricing">Pricing</a>
                <a href="auth.php" class="btn-login">Login</a>
                <a href="auth.php" class="btn-signup">Get Started</a>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <line x1="3" y1="12" x2="21" y2="12" stroke-width="2"/>
                    <line x1="3" y1="6" x2="21" y2="6" stroke-width="2"/>
                    <line x1="3" y1="18" x2="21" y2="18" stroke-width="2"/>
                </svg>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    Music That Understands
                    <span class="gradient-text">Your Emotions</span>
                </h1>
                <p class="hero-subtitle">
                    Discover the perfect soundtrack for every moment with AI-powered recommendations that match your mood.
                </p>
                <div class="hero-buttons">
                    <a href="auth.php" class="btn-primary">Start Listening Free</a>
                    <a href="index.php" class="btn-secondary">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        Try as Guest
                    </a>
                </div>
                <p class="hero-note">No credit card required • Free forever</p>
            </div>
            <div class="hero-visual">
                <div class="floating-card card-1">
                    <div class="card-emoji">😢</div>
                    <div class="card-text">Sad Vibes</div>
                </div>
                <div class="floating-card card-2">
                    <div class="card-emoji">😊</div>
                    <div class="card-text">Happy Mood</div>
                </div>
                <div class="floating-card card-3">
                    <div class="card-emoji">😌</div>
                    <div class="card-text">Calm & Chill</div>
                </div>
                <div class="floating-card card-4">
                    <div class="card-emoji">⚡</div>
                    <div class="card-text">Energetic</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="section-container">
            <div class="section-header">
                <h2>Why Choose HearYou?</h2>
                <p>Experience music streaming like never before</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">😢😊😌⚡</div>
                    <h3>Emotion-Based Filtering</h3>
                    <p>Filter songs by how you feel. Whether you're sad, happy, calm, or energetic, we've got the perfect playlist.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🤖</div>
                    <h3>AI Music Companion</h3>
                    <p>Chat with our intelligent AI that understands your emotions and recommends songs tailored to your mood.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">❤️</div>
                    <h3>Your Personal Library</h3>
                    <p>Save your favorite tracks, create playlists, and build your music collection organized by emotions.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎵</div>
                    <h3>Seamless Playback</h3>
                    <p>Beautiful player with full controls, crossfade, gapless playback, and high-quality audio streaming.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔍</div>
                    <h3>Smart Search</h3>
                    <p>Find any song instantly by title, artist, album, or even by mood. Our search understands what you need.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌙</div>
                    <h3>Beautiful Interface</h3>
                    <p>Stunning dark mode design with smooth animations. Easy to use on desktop, tablet, and mobile.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="section-container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Get started in 3 simple steps</p>
            </div>
            <div class="steps-grid">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Choose Your Mood</h3>
                        <p>Select from Sad, Happy, Calm, or Energetic. Our AI understands what you need to hear.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Discover Music</h3>
                        <p>Browse curated playlists or chat with our AI for personalized recommendations.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Enjoy & Save</h3>
                        <p>Listen to your perfect soundtrack and save favorites to your personal library.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
        <div class="section-container">
            <div class="section-header">
                <h2>Choose Your Plan</h2>
                <p>Free forever, or upgrade for premium features</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <div class="plan-name">Free</div>
                    <div class="plan-price">$0<span>/month</span></div>
                    <ul class="plan-features">
                        <li>✓ Emotion-based filtering</li>
                        <li>✓ AI recommendations</li>
                        <li>✓ Unlimited streaming</li>
                        <li>✓ Create playlists</li>
                        <li>✓ Standard audio quality</li>
                    </ul>
                    <a href="auth.php" class="btn-plan">Get Started</a>
                </div>
                <div class="pricing-card featured">
                    <div class="plan-badge">Most Popular</div>
                    <div class="plan-name">Premium</div>
                    <div class="plan-price">$9.99<span>/month</span></div>
                    <ul class="plan-features">
                        <li>✓ Everything in Free</li>
                        <li>✓ High-quality audio (320kbps)</li>
                        <li>✓ Offline downloads</li>
                        <li>✓ No ads</li>
                        <li>✓ Advanced AI features</li>
                        <li>✓ Priority support</li>
                    </ul>
                    <a href="auth.php" class="btn-plan primary">Upgrade Now</a>
                </div>
                <div class="pricing-card">
                    <div class="plan-name">Family</div>
                    <div class="plan-price">$14.99<span>/month</span></div>
                    <ul class="plan-features">
                        <li>✓ Everything in Premium</li>
                        <li>✓ Up to 6 accounts</li>
                        <li>✓ Family mix playlists</li>
                        <li>✓ Parental controls</li>
                        <li>✓ Shared library</li>
                    </ul>
                    <a href="auth.php" class="btn-plan">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-content">
            <h2>Ready to Find Your Perfect Sound?</h2>
            <p>Join thousands of users who've discovered their soundtrack with HearYou</p>
            <a href="auth.php" class="btn-cta">Start Listening Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <div class="footer-logo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M9 18V5l12-2v13M9 18l-7 2V7l7-2M9 18l7 2" stroke-width="2"/>
                    </svg>
                    <span>HearYou</span>
                </div>
                <p>Music that understands your emotions</p>
            </div>
            <div class="footer-section">
                <h4>Product</h4>
                <a href="#features">Features</a>
                <a href="#pricing">Pricing</a>
                <a href="index.php">Try Demo</a>
                <a href="auth.php">Sign Up</a>
            </div>
            <div class="footer-section">
                <h4>Company</h4>
                <a href="#">About Us</a>
                <a href="#">Blog</a>
                <a href="#">Careers</a>
                <a href="#">Contact</a>
            </div>
            <div class="footer-section">
                <h4>Legal</h4>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
                <a href="#">DMCA</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 HearYou. All rights reserved.</p>
            <div class="footer-social">
                <a href="#" aria-label="Twitter">𝕏</a>
                <a href="#" aria-label="Facebook">f</a>
                <a href="#" aria-label="Instagram">📷</a>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Animate on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .step, .pricing-card').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>