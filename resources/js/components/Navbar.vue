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

            <!-- Desktop Actions (Theme Toggle) -->
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

            <!-- Navigation Links -->
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
                    {{ item.label }}
                </button>

                <!-- Mobile Theme Toggle -->
                <button 
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
            }
        },
        handleClickOutside(event) {
            // Close mobile menu when clicking outside
            if (this.mobileMenuOpen && !this.$el.contains(event.target)) {
                this.mobileMenuOpen = false;
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
    }
}
</script>

<style scoped>
/* Container for navbar content */
.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
}

/* Logo Section */
.logo-section {
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 10000;
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

/* Navigation Links - Desktop */
.nav-links {
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
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
    
    .mobile-theme-toggle {
        display: flex !important;
        width: 100%;
        border-radius: 8px;
        justify-content: flex-start;
        padding: 15px 20px;
        margin-top: 10px;
        border-top: 1px solid var(--theme-border, #f0f0f0);
    }
    
    .theme-label {
        display: inline;
        margin-left: 12px;
        font-weight: 500;
    }
    
    .nav-links {
        display: none; /* Hide by default on mobile */
        position: absolute;
        top: 100%;
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
    }
    
    .nav-links.active {
        display: flex; /* Show when active */
        max-height: 500px;
        padding: 10px 0;
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

/* Small Mobile Optimization */
@media (max-width: 480px) {
    nav {
        padding: 12px 15px;
    }
    
    .logo-section h1 {
        font-size: 18px;
    }
    
    .nav-button {
        width: calc(100% - 30px);
        margin: 0 15px;
        padding: 14px 16px;
        font-size: 14px;
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
}

/* Prevent body scroll when mobile menu is open */
body.mobile-menu-open {
    overflow: hidden;
}
</style>