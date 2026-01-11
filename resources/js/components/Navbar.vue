<template>
    <nav 
        :style="{
            background: theme === 'light' ? 'white' : '#1a1a1a',
            color: theme === 'light' ? 'black' : 'white',
            padding: '15px',
            position: 'relative',
            zIndex: '9999'
        }"
    >
        <!-- Navbar Content -->
        <div class="nav-container">
            <!-- Logo -->
            <div class="logo-section">
                <span style="font-size: 20px;">🧠</span>
                <h1 style="margin: 0; font-size: 20px; font-weight: bold;">MindSpark</h1>
            </div>

            <!-- Navigation Links (Center) -->
            <div 
                class="nav-links" 
                :class="{ 'active': mobileMenuOpen }"
                v-show="mobileMenuOpen || windowWidth > 768"
            >
                <button 
                    v-for="item in navigation" 
                    :key="item.route"
                    @click="handleNavigation(item.route)"
                    :class="{
                        'nav-button': true,
                        'active': currentView === item.route
                    }"
                    :aria-current="currentView === item.route ? 'page' : null"
                >
                    <span class="nav-icon">{{ getIconForRoute(item.route) }}</span>
                    <span class="nav-text">{{ item.label }}</span>
                </button>
            </div>

            <!-- Right Side Actions -->
            <div class="nav-right">
                <!-- Desktop Theme Toggle -->
                <div class="desktop-actions">
                    <button 
                        class="theme-toggle"
                        @click="toggleTheme"
                        :aria-label="theme === 'light' ? 'Switch to dark theme' : 'Switch to light theme'"
                        title="Toggle theme"
                    >
                        <span class="theme-icon">
                            {{ theme === 'light' ? '🌙' : '☀️' }}
                        </span>
                    </button>
                </div>

                <!-- Hamburger Menu (Mobile Only) -->
                <button 
                    class="hamburger" 
                    @click.stop="toggleMobileMenu"
                    aria-label="Toggle navigation menu"
                    aria-expanded="mobileMenuOpen"
                >
                    <span class="hamburger-icon" v-if="!mobileMenuOpen">☰</span>
                    <span class="hamburger-icon" v-else>✕</span>
                </button>
            </div>

            <!-- Mobile Theme Toggle (Hidden on Desktop) -->
            <button 
                v-if="mobileMenuOpen && windowWidth <= 768"
                class="theme-toggle mobile-theme-toggle"
                @click="toggleTheme"
                :aria-label="theme === 'light' ? 'Switch to dark theme' : 'Switch to light theme'"
            >
                <span class="theme-icon">
                    {{ theme === 'light' ? '🌙' : '☀️' }}
                </span>
                <span class="theme-label">
                    {{ theme === 'light' ? 'Dark Mode' : 'Light Mode' }}
                </span>
            </button>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'Navbar',
    props: {
        currentView: String
    },
    data() {
        return {
            navigation: [
                { route: 'dashboard', label: 'Dashboard' },
                { route: 'quiz-sets', label: 'Take Quiz' },
                { route: 'gk-blog', label: 'GK Blog' },
                { route: 'records', label: 'Previous Records' }
            ],
            mobileMenuOpen: false,
            windowWidth: window.innerWidth,
            theme: 'light' // Default theme
        }
    },
    methods: {
        getIconForRoute(route) {
            const icons = {
                'dashboard': '🏠',
                'quiz-sets': '📝',
                'gk-blog': '📚',
                'records': '📊'
            };
            return icons[route] || '🔗';
        },
        handleNavigation(route) {
            console.log('🔼 Navbar: Emitting navigate event with route:', route);
            this.$emit('navigate', route);
            this.mobileMenuOpen = false; // Close mobile menu after navigation
        },
        toggleMobileMenu() {
            console.log('Hamburger clicked, current state:', this.mobileMenuOpen);
            this.mobileMenuOpen = !this.mobileMenuOpen;
            
            // Toggle body scroll lock
            if (this.mobileMenuOpen) {
                document.body.classList.add('mobile-menu-open');
            } else {
                document.body.classList.remove('mobile-menu-open');
            }
        },
        toggleTheme() {
            // Toggle between light and dark themes
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            
            // Emit event to parent component
            this.$emit('theme-change', this.theme);
            
            // Store theme preference in localStorage
            localStorage.setItem('app-theme', this.theme);
            
            // Apply theme to document body
            this.applyThemeToBody();
            
            console.log('Theme changed to:', this.theme);
        },
        applyThemeToBody() {
            // Apply theme class to body for global styling
            if (this.theme === 'dark') {
                document.body.classList.add('dark-theme');
                document.body.classList.remove('light-theme');
            } else {
                document.body.classList.add('light-theme');
                document.body.classList.remove('dark-theme');
            }
        },
        loadThemePreference() {
            // Load theme from localStorage, default to light
            const savedTheme = localStorage.getItem('app-theme');
            if (savedTheme) {
                this.theme = savedTheme;
            } else {
                // Check system preference
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                this.theme = prefersDark ? 'dark' : 'light';
            }
            
            this.applyThemeToBody();
        },
        handleResize() {
            this.windowWidth = window.innerWidth;
            // Close mobile menu when resizing to desktop
            if (window.innerWidth > 768) {
                this.mobileMenuOpen = false;
                document.body.classList.remove('mobile-menu-open');
            }
        },
        handleClickOutside(event) {
            // Close mobile menu when clicking outside
            if (this.mobileMenuOpen && !this.$el.contains(event.target)) {
                this.mobileMenuOpen = false;
                document.body.classList.remove('mobile-menu-open');
            }
        }
    },
    mounted() {
        console.log('🧭 Responsive Navbar mounted');
        
        // Load saved theme preference
        this.loadThemePreference();
        
        // Add event listeners
        window.addEventListener('resize', this.handleResize);
        document.addEventListener('click', this.handleClickOutside);
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('app-theme')) {
                this.theme = e.matches ? 'dark' : 'light';
                this.applyThemeToBody();
                this.$emit('theme-change', this.theme);
            }
        });
    },
    beforeUnmount() {
        // Clean up event listeners
        window.removeEventListener('resize', this.handleResize);
        document.removeEventListener('click', this.handleClickOutside);
        document.body.classList.remove('mobile-menu-open');
    }
}
</script>

<style scoped>
/* Container for navbar content - Updated for proper alignment */
.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    min-height: 50px;
}

/* Logo Section - Left aligned */
.logo-section {
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 10000;
    flex: 1;
    min-width: 120px;
}

/* Navigation Links - Centered */
.nav-links {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    transition: all 0.3s ease;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: auto;
}

/* Right side container for theme toggle and hamburger */
.nav-right {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
    justify-content: flex-end;
    min-width: 120px;
}

/* Desktop Actions */
.desktop-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Theme Toggle Button */
.theme-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid transparent;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: inherit;
}

.theme-toggle:hover {
    background: var(--theme-hover-bg, #f0f0f0);
    border-color: var(--theme-border, #ddd);
}

.theme-icon {
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.theme-label {
    display: none;
}

/* Mobile Theme Toggle */
.mobile-theme-toggle {
    display: none;
}

/* Navigation Button */
.nav-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: transparent;
    color: inherit;
    border: 1px solid transparent;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    white-space: nowrap; /* Prevent text from wrapping */
    text-align: center;
    flex-shrink: 0; /* Prevent buttons from shrinking */
    min-width: 0; /* Allow button to shrink if needed */
}

.nav-button:hover {
    background: var(--theme-hover-bg, #f0f0f0);
    border-color: var(--theme-border, #ddd);
}

.nav-button.active {
    background: #3498db;
    color: white;
}

/* Navigation Icon */
.nav-icon {
    font-size: 16px;
    display: inline-block;
    flex-shrink: 0; /* Prevent icon from shrinking */
}

/* Navigation Text */
.nav-text {
    white-space: nowrap; /* Ensure text doesn't wrap */
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hamburger Menu - Hidden on desktop by default */
.hamburger {
    display: none; /* Hidden on desktop */
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: inherit;
    padding: 5px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
    width: 40px;
    height: 40px;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    position: relative;
}

.hamburger:hover {
    background: var(--theme-hover-bg, #f0f0f0);
}

.hamburger-icon {
    display: block;
    line-height: 1;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .hamburger {
        display: flex !important; /* Force show on mobile */
    }
    
    .desktop-actions {
        display: none;
    }
    
    .nav-links {
        position: fixed; /* Change to fixed for mobile overlay */
        top: 70px; /* Below navbar */
        left: 0;
        width: 100%;
        background: var(--theme-bg, white);
        flex-direction: column;
        padding: 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        border-top: 1px solid var(--theme-border, #e0e0e0);
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
        transform: none;
        z-index: 9998;
        gap: 0;
    }
    
    .nav-links.active {
        display: flex; /* Show when active */
        max-height: 500px;
        padding: 10px 0;
    }
    
    .mobile-theme-toggle {
        display: flex !important;
        width: calc(100% - 40px);
        border-radius: 8px;
        justify-content: flex-start;
        padding: 15px 20px;
        margin: 10px 20px 0;
        border-top: 1px solid var(--theme-border, #f0f0f0);
        position: relative;
        z-index: 9999;
        white-space: nowrap;
    }
    
    .theme-label {
        display: inline;
        margin-left: 12px;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .nav-button {
        width: calc(100% - 40px);
        margin: 0 20px;
        text-align: left;
        padding: 15px 20px;
        border-radius: 8px;
        border: none;
        border-bottom: 1px solid var(--theme-border, #f0f0f0);
        justify-content: flex-start;
        margin-bottom: 5px;
        white-space: nowrap; /* Keep text on one line */
    }
    
    .nav-button:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .nav-button:hover {
        background: var(--theme-hover-bg, #f8f9fa);
    }
    
    .nav-icon {
        min-width: 24px;
        text-align: center;
    }
}

/* Medium screens adjustment */
@media (min-width: 769px) and (max-width: 1024px) {
    .nav-links {
        gap: 8px; /* Reduce gap on medium screens */
    }
    
    .nav-button {
        padding: 8px 12px;
        font-size: 14px;
        white-space: nowrap; /* Ensure text doesn't wrap */
    }
    
    .logo-section h1 {
        font-size: 18px;
    }
}

/* Small desktop screens (769px - 900px) */
@media (min-width: 769px) and (max-width: 900px) {
    .nav-links {
        gap: 6px;
    }
    
    .nav-button {
        padding: 6px 10px;
        font-size: 13px;
    }
    
    .nav-icon {
        font-size: 14px;
        margin-right: 4px;
    }
    
    .logo-section h1 {
        font-size: 16px;
    }
}

/* Very small desktop screens */
@media (min-width: 769px) and (max-width: 820px) {
    .nav-links {
        gap: 4px;
    }
    
    .nav-button {
        padding: 5px 8px;
        font-size: 12px;
    }
    
    .nav-icon {
        font-size: 12px;
        margin-right: 3px;
    }
    
    .logo-section h1 {
        font-size: 15px;
    }
}

/* Small Mobile Optimization */
@media (max-width: 480px) {
    nav {
        padding: 12px 15px;
    }
    
    .logo-section h1 {
        font-size: 18px;
        white-space: nowrap;
    }
    
    .nav-button {
        width: calc(100% - 30px);
        margin: 0 15px;
        padding: 14px 16px;
        font-size: 14px;
        white-space: nowrap; /* Keep text on one line */
    }
    
    .hamburger {
        width: 36px;
        height: 36px;
        font-size: 20px;
    }
    
    .nav-icon {
        font-size: 14px;
    }
    
    .theme-toggle {
        width: 36px;
        height: 36px;
    }
    
    .mobile-theme-toggle {
        width: calc(100% - 30px);
        margin: 10px 15px 0;
    }
}

/* Focus styles for accessibility */
.nav-button:focus,
.hamburger:focus,
.theme-toggle:focus {
    outline: 2px solid #3498db;
    outline-offset: 2px;
}

/* Active state improvements */
.nav-button.active {
    box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
}

/* Smooth transitions for all interactive elements */
.nav-button,
.hamburger,
.theme-toggle {
    transition: all 0.2s ease-in-out;
}

/* Logo hover effect */
.logo-section:hover {
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

/* Ensure all buttons maintain single line text */
button {
    white-space: nowrap;
}
</style>

<style>
/* Global theme variables */
:root {
    --theme-bg: white;
    --theme-text: black;
    --theme-hover-bg: #f0f0f0;
    --theme-border: #ddd;
}

.dark-theme {
    --theme-bg: #1a1a1a;
    --theme-text: #ffffff;
    --theme-hover-bg: #2a2a2a;
    --theme-border: #444;
}

body {
    background-color: var(--theme-bg);
    color: var(--theme-text);
    transition: background-color 0.3s ease, color 0.3s ease;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
}

/* Prevent body scroll when mobile menu is open */
body.mobile-menu-open {
    overflow: hidden;
}

/* Global link styles */
a {
    color: inherit;
    text-decoration: none;
}

/* Global button reset */
button {
    font-family: inherit;
    font-size: inherit;
    cursor: pointer;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Selection color */
::selection {
    background-color: rgba(52, 152, 219, 0.3);
    color: inherit;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: var(--theme-bg);
}

::-webkit-scrollbar-thumb {
    background: var(--theme-border);
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #888;
}

/* Prevent text wrapping in navigation */
.nav-text {
    white-space: nowrap;
}
</style>