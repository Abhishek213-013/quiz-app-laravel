<template>
    <nav style="background: white; color: black; padding: 15px; position: relative; z-index: 9999;">
        <!-- Navbar Content -->
        <div class="nav-container">
            <!-- Logo -->
            <div class="logo-section">
                <span style="font-size: 20px;">🧠</span>
                <h1 style="margin: 0; font-size: 20px; font-weight: bold;">MindSpark</h1>
            </div>

            <!-- Hamburger Menu (Mobile Only) -->
            <button 
                class="hamburger" 
                @click="toggleMobileMenu"
                aria-label="Toggle navigation menu"
            >
                <i class="fas fa-bars" v-if="!mobileMenuOpen"></i>
                <i class="fas fa-times" v-else></i>
            </button>

            <!-- Navigation Links -->
            <div 
                class="nav-links" 
                :class="{ 'active': mobileMenuOpen }"
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
                    {{ item.label }}
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
            mobileMenuOpen: false
        }
    },
    methods: {
        includeFontAwesome() {
            // Check if Font Awesome is already loaded
            if (!document.querySelector('link[href*="font-awesome"]')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
                document.head.appendChild(link);
            }
        },
        handleNavigation(route) {
            console.log('🔼 Navbar: Emitting navigate event with route:', route);
            this.$emit('navigate', route);
            this.mobileMenuOpen = false; // Close mobile menu after navigation
        },
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },
        handleResize() {
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
        
        // Add Font Awesome dynamically if not already included
        this.includeFontAwesome();
        
        // Add event listeners
        window.addEventListener('resize', this.handleResize);
        document.addEventListener('click', this.handleClickOutside);
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
}

/* Logo Section */
.logo-section {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Navigation Links - Desktop */
.nav-links {
    display: flex;
    gap: 15px;
    transition: all 0.3s ease;
}

/* Navigation Button */
.nav-button {
    padding: 8px 16px;
    background: transparent;
    color: black;
    border: 1px solid transparent;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
}

.nav-button:hover {
    background: #f0f0f0;
    border-color: #ddd;
}

.nav-button.active {
    background: #3498db;
    color: white;
}

/* Hamburger Menu - Hidden on desktop by default */
.hamburger {
    display: none; /* Hidden on desktop */
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #2c3e50;
    padding: 5px 10px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
    width: 40px;
    height: 40px;
    align-items: center;
    justify-content: center;
}

.hamburger:hover {
    background: #f0f0f0;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .hamburger {
        display: flex !important; /* Only visible on mobile */
    }
    
    .nav-links {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: white;
        flex-direction: column;
        padding: 0;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    
    .nav-links.active {
        max-height: 300px;
        padding: 10px 0;
    }
    
    .nav-button {
        width: 100%;
        text-align: left;
        padding: 12px 20px;
        border-radius: 0;
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .nav-button:last-child {
        border-bottom: none;
    }
    
    .nav-button:hover {
        background: #f8f9fa;
    }
}

/* Small Mobile Optimization */
@media (max-width: 480px) {
    nav {
        padding: 10px 15px;
    }
    
    .logo-section h1 {
        font-size: 18px;
    }
    
    .nav-button {
        padding: 14px 20px;
    }
    
    .hamburger {
        width: 36px;
        height: 36px;
        font-size: 18px;
    }
}

/* Ensure icons are properly sized */
.hamburger i {
    font-size: inherit;
}
</style>