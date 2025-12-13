<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle 'utilisateur'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php');
    exit();
}

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'utilisateur') {
    // Rediriger vers une page d'erreur ou de tableau de bord admin si nécessaire
    header('Location: unauthorized.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM FLUX - Digital Innovation Studio</title>
    <link rel="stylesheet" href="templatemo-prism-flux.css">
<!-- 

TemplateMo 600 Prism Flux

https://templatemo.com/tm-600-prism-flux

-->
</head>
<style>
#chatButton {
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 60px;
    height: 60px;
    background: #00DCB9;
    color: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 26px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 99999;
    transition: 0.2s ease;
}
#chatButton:hover {
    transform: scale(1.1);
    background: #00DCB9;
}

#chatWindow {
    position: fixed;
    bottom: 95px;
    right: 25px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    display: none;
    flex-direction: column;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    z-index: 100000;
    overflow: hidden;
    opacity: 0;
    transform: scale(0.8);
    transition: opacity 0.25s ease, transform 0.25s ease;
}
#chatWindow.open {
    display: flex;
    opacity: 1;
    transform: scale(1);
}

#chatHeader {
    background: #00DCB9;
    color: white;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
#chatHeader button {
    background: none;
    border: none;
    color: white;
    font-size: 22px;
    cursor: pointer;
}
iframe {
    flex: 1;
    width: 100%;
    border: none;
}
</style>
<body>
    <!-- Loading Screen -->
    <div class="loader" id="loader">
        <div class="loader-content">
            <div class="loader-prism">
                <div class="prism-face"></div>
                <div class="prism-face"></div>
                <div class="prism-face"></div>
            </div>
            <div style="color: var(--accent-purple); font-size: 18px; text-transform: uppercase; letter-spacing: 3px;">Refracting Reality...</div>
        </div>
    </div>

    <!-- Navigation Header -->
    <style>
        /* Styles pour la navigation */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 999;
            padding: 10px 0;
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 15px;
        }
        
        .nav-menu li {
            margin: 0;
            padding: 0;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 16px;
            display: inline-block;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .menu-toggle {
            display: none;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            margin-right: 30px;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .prism { color: #6e45e2; }
        .flux { color: #88d3ce; }
    </style>
    
    <header class="header" id="header">
        <nav class="nav-container">
            <a href="#home" class="logo">
                <div class="logo-icon">
                    <div class="logo-prism">
                        <div class="prism-shape"></div>
                    </div>
                </div>
                <span class="logo-text">
                    <span class="prism">PRISM</span>
                    <span class="flux">FLUX</span>
                </span>
            </a>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <li><a href="#stats" class="nav-link">Metrics</a></li>
                <li><a href="#skills" class="nav-link">Arsenal</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
                <li><a href="#" class="nav-link" style="background-color: #ff3333; color: white;" onclick="if(confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) { window.location.href='logout.php'; return false; }">Déconnexion</a></li>
            </ul>
            
            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- Hero Section with 3D Carousel -->
    <section class="hero" id="home">
        <div class="carousel-container">
            <div class="carousel" id="carousel">
                <!-- Carousel items will be generated by JavaScript -->
            </div>
            
            <div class="carousel-controls">
                <button class="carousel-btn" id="prevBtn">‹</button>
                <button class="carousel-btn" id="nextBtn">›</button>
            </div>
            
            <div class="carousel-indicators" id="indicators">
                <!-- Indicators will be generated by JavaScript -->
            </div>
        </div>
    </section>

    <!-- NEW: Prism Philosophy Section (About) -->
    <section class="philosophy-section" id="about">
        <div class="philosophy-container">
            <div class="prism-line"></div>
            
            <h2 class="philosophy-headline">
                Refracting Ideas<br>Into Reality
            </h2>
            
            <p class="philosophy-subheading">
                At PRISM FLUX, we transform complex challenges into elegant solutions through the convergence of 
                cutting-edge technology and visionary design. Every project is a spectrum of possibilities 
                waiting to be discovered.
            </p>
            
            <div class="philosophy-pillars">
                <div class="pillar">
                    <div class="pillar-icon">💎</div>
                    <h3 class="pillar-title">Innovation</h3>
                    <p class="pillar-description">
                        Breaking boundaries with revolutionary approaches that redefine industry standards and push the limits of what's possible. Elevate your designs with premium vector stickers from <a href="https://www.vectorsticker.com" rel="nofollow" target="_blank">VectorSticker</a>.
                    </p>
                </div>
                
                <div class="pillar">
                    <div class="pillar-icon">🔬</div>
                    <h3 class="pillar-title">Precision</h3>
                    <p class="pillar-description">
                        Meticulous attention to detail ensures every pixel, every line of code, and every interaction is perfectly crafted by <a href="https://templatemo.com" rel="nofollow" target="_blank" style="color: var(--accent-cyan); text-decoration: none;">TemplateMo</a>, enhanced with stunning visuals from <a href="https://unsplash.com" rel="nofollow" target="_blank" style="color: var(--accent-cyan); text-decoration: none;">Unsplash</a>.
                    </p>
                </div>
                
                <div class="pillar">
                    <div class="pillar-icon">∞</div>
                    <h3 class="pillar-title">Evolution</h3>
                    <p class="pillar-description">
                        Continuous adaptation and growth, staying ahead of trends while building timeless solutions for tomorrow. Boost your productivity with the easy-to-use timer tools at <a href="https://timermo.com" rel="nofollow" target="_blank">TimerMo</a>.
                    </p>
                </div>
            </div>
            
            <div class="philosophy-particles" id="particles">
                <!-- Particles will be generated by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Stats Section with Content -->
    <section class="stats-section" id="stats">
        <div class="section-header">
            <h2 class="section-title">Performance Metrics</h2>
            <p class="section-subtitle">Real-time analytics and achievements powered by cutting-edge technology</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🚀</div>
                <div class="stat-number" data-target="150">0</div>
                <div class="stat-label">Projects Completed</div>
                <p class="stat-description">Successfully delivered enterprise-level solutions across multiple industries with 100% on-time completion rate.</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⚡</div>
                <div class="stat-number" data-target="99">0</div>
                <div class="stat-label">Client Satisfaction %</div>
                <p class="stat-description">Maintaining excellence through continuous feedback loops and agile development methodologies.</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🏆</div>
                <div class="stat-number" data-target="25">0</div>
                <div class="stat-label">Industry Awards</div>
                <p class="stat-description">Recognized globally for innovation in digital transformation and technological advancement.</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💎</div>
                <div class="stat-number" data-target="500">0</div>
                <div class="stat-label">Code Commits Daily</div>
                <p class="stat-description">Continuous integration and deployment with automated testing ensuring maximum code quality.</p>
            </div>
        </div>
    </section>

    <!-- Enhanced Skills Section - Technical Arsenal -->
    <section class="skills-section" id="skills">
        <div class="skills-container">
            <div class="section-header">
                <h2 class="section-title">Technical Arsenal</h2>
                <p class="section-subtitle">Mastery of cutting-edge technologies and frameworks</p>
            </div>
            
            <div class="skill-categories">
                <div class="category-tab active" data-category="all">All Skills</div>
                <div class="category-tab" data-category="frontend">Frontend</div>
                <div class="category-tab" data-category="backend">Backend</div>
                <div class="category-tab" data-category="cloud">Cloud & DevOps</div>
                <div class="category-tab" data-category="emerging">Emerging Tech</div>
            </div>

            <div class="skills-hexagon-grid" id="skillsGrid">
                <!-- Hexagonal skill items will be generated by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="section-header">
            <h2 class="section-title">Initialize Connection</h2>
            <p class="section-subtitle">Ready to transform your vision into reality? Let's connect.</p>
        </div>
        
        <div class="contact-container">
            <div class="contact-info">
                <a href="https://maps.google.com/?q=Silicon+Valley+CA+94025" target="_blank" class="info-item">
                    <div class="info-icon">📍</div>
                    <div class="info-text">
                        <h4>Location</h4>
                        <p>Silicon Valley, CA 94025</p>
                    </div>
                </a>
                
                <a href="mailto:hello@prismflux.io" class="info-item">
                    <div class="info-icon">📧</div>
                    <div class="info-text">
                        <h4>Email</h4>
                        <p>hello@prismflux.io</p>
                    </div>
                </a>
                
                <a href="tel:+15551234567" class="info-item">
                    <div class="info-icon">📱</div>
                    <div class="info-text">
                        <h4>Phone</h4>
                        <p>+1 (555) 123-4567</p>
                    </div>
                </a>
                
                <a href="https://calendly.com" target="_blank" class="info-item">
                    <div class="info-icon">📅</div>
                    <div class="info-text">
                        <h4>Schedule Meeting</h4>
                        <p>Book a consultation</p>
                    </div>
                </a>
            </div>
            
            <form class="contact-form" id="contactForm">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Transmit Message</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="logo-icon">
                        <div class="logo-prism">
                            <div class="prism-shape"></div>
                        </div>
                    </div>
                    <span class="logo-text">
                        <span class="prism">PRISM</span>
                        <span class="flux">FLUX</span>
                    </span>
                </div>
                <p class="footer-description">
                    Refracting complex challenges into brilliant solutions through the convergence of art, science, and technology.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-icon">f</a>
                    <a href="#" class="social-icon">t</a>
                    <a href="#" class="social-icon">in</a>
                    <a href="#" class="social-icon">ig</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Services</h4>
                <div class="footer-links">
                    <a href="#">Web Development</a>
                    <a href="#">App Development</a>
                    <a href="#">Cloud Solutions</a>
                    <a href="#">AI Integration</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Company</h4>
                <div class="footer-links">
                    <a href="#">About Us</a>
                    <a href="#">Our Team</a>
                    <a href="#">Careers</a>
                    <a href="#">Press Kit</a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Resources</h4>
                <div class="footer-links">
                    <a href="#">Documentation</a>
                    <a href="#">API Reference</a>
                    <a href="#">Blog</a>
                    <a href="#">Support</a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                &copy; 2026 PRISM FLUX. All rights reserved.
            </div>
            <div class="footer-credits">
                Designed by <a href="https://templatemo.com" rel="nofollow" target="_blank">TemplateMo</a>
            </div>
        </div>
    </footer>
    <div id="chatButton">💬</div>

<div id="chatWindow">
    <div id="chatHeader">
        Chatbot
        <button id="closeChat">×</button>
    </div>
    <iframe id="chatIframe"></iframe>
</div>
    <style>
        /* Ajout d'un espace pour éviter que le contenu ne soit caché sous la barre de navigation fixe */
        body {
            padding-top: 70px;
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.9);
                flex-direction: column;
                padding: 20px;
            }
            
            .nav-menu.active {
                display: flex;
            }
            
            .menu-toggle {
                display: block;
                cursor: pointer;
            }
            
            .menu-toggle span {
                display: block;
                width: 25px;
                height: 3px;
                background: white;
                margin: 5px 0;
                transition: 0.3s;
            }
        }
    </style>
    <script>
const chatButton = document.getElementById("chatButton");
const chatWindow = document.getElementById("chatWindow");
const chatIframe = document.getElementById("chatIframe");
const closeChat = document.getElementById("closeChat");

const chatbotUrl = "https://www.chatbase.co/chatbot-iframe/WAO818oBk6Ity1yhCsPT8";
let iframeLoaded = false;

chatButton.addEventListener("click", () => {
    chatWindow.style.display = "flex";
    if (!iframeLoaded) {
        chatIframe.src = chatbotUrl;
        iframeLoaded = true;
    }
    setTimeout(() => {
        chatWindow.classList.add("open");
    }, 10);
});

closeChat.addEventListener("click", () => {
    chatWindow.classList.remove("open");
    setTimeout(() => {
        chatWindow.style.display = "none";
    }, 250);
});
</script>
    
    <script>
        // Script pour le menu mobile
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            menuToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
            });
            
            // Fermer le menu quand on clique sur un lien (sauf le lien de déconnexion)
            document.querySelectorAll('.nav-link:not([href*="logout.php"])').forEach(link => {
                link.addEventListener('click', (e) => {
                    // Ne pas fermer le menu si c'est un lien externe ou une ancre sur la même page
                    if (!link.getAttribute('href').startsWith('http') && !link.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        navMenu.classList.remove('active');
                        window.location.href = link.getAttribute('href');
                    } else {
                        navMenu.classList.remove('active');
                    }
                });
            });
        });
    </script>
    <script src="templatemo-prism-scripts.js"></script>
</body>
</html>