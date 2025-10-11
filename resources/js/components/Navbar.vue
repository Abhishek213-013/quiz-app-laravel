<template>
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and App Name -->
                <div class="flex items-center space-x-3">
                    <i class="fas fa-brain text-blue-600 text-xl"></i>
                    <h1 class="text-xl font-bold text-gray-800">MindSpark</h1>
                </div>

                <!-- Desktop Navigation - Hidden on mobile -->
                <div class="hidden md:flex items-center space-x-8">
                    <button 
                        v-for="item in navigation" 
                        :key="item.route"
                        @click="handleNavigation(item.route)"
                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        :class="{ 'bg-gray-200 text-gray-900': currentView === item.route }"
                    >
                        {{ item.label }}
                    </button>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button 
                        @click="toggleMobileSidebar"
                        class="text-gray-600 hover:text-gray-900 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                    >
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div 
            v-if="mobileSidebarOpen" 
            class="fixed inset-0 z-50 md:hidden"
        >
            <!-- Background overlay -->
            <div 
                class="fixed inset-0 bg-black bg-opacity-50"
                @click="toggleMobileSidebar"
            ></div>
            
            <!-- Sidebar panel -->
            <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-xl">
                <div class="flex flex-col h-full">
                    <!-- Sidebar header -->
                    <div class="flex items-center justify-between p-4 border-b">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-brain text-blue-600 text-xl"></i>
                            <h2 class="text-lg font-bold text-gray-800">MindSpark</h2>
                        </div>
                        <button 
                            @click="toggleMobileSidebar"
                            class="text-gray-400 hover:text-gray-600 p-2 rounded-md"
                        >
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Navigation links -->
                    <div class="flex-1 px-4 py-6 space-y-2">
                        <button 
                            v-for="item in navigation" 
                            :key="item.route"
                            @click="handleMobileNavigation(item.route)"
                            class="w-full text-left text-gray-600 hover:text-gray-900 hover:bg-gray-100 px-4 py-3 rounded-md text-base font-medium transition-colors flex items-center space-x-3"
                            :class="{ 'bg-blue-50 text-blue-700 border-r-2 border-blue-600': currentView === item.route }"
                        >
                            <i :class="getMobileIcon(item.route)" class="w-5 text-center"></i>
                            <span>{{ item.label }}</span>
                        </button>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 border-t">
                        <div class="text-xs text-gray-500 text-center">
                            © 2024 MindSpark
                        </div>
                    </div>
                </div>
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
            mobileSidebarOpen: false,
            navigation: [
                { route: 'dashboard', label: 'Dashboard' },
                { route: 'quiz-sets', label: 'Take Quiz' },
                { route: 'gk-blog', label: 'GK Blog' },
                { route: 'records', label: 'Previous Records' }
            ]
        }
    },
    methods: {
        handleNavigation(route) {
            this.$emit('navigate', route);
        },
        
        handleMobileNavigation(route) {
            this.handleNavigation(route);
            this.toggleMobileSidebar(); // Close sidebar after navigation
        },
        
        toggleMobileSidebar() {
            this.mobileSidebarOpen = !this.mobileSidebarOpen;
            
            // Prevent body scroll when sidebar is open
            if (this.mobileSidebarOpen) {
                document.body.classList.add('sidebar-open');
            } else {
                document.body.classList.remove('sidebar-open');
            }
        },
        
        getMobileIcon(route) {
            const icons = {
                'dashboard': 'fas fa-home',
                'quiz-sets': 'fas fa-clipboard-list',
                'gk-blog': 'fas fa-book-open',
                'records': 'fas fa-chart-bar'
            };
            return icons[route] || 'fas fa-circle';
        }
    },
    
    // Close sidebar when clicking outside (optional enhancement)
    mounted() {
        document.addEventListener('keydown', this.handleEscape);
    },
    
    beforeUnmount() {
        document.removeEventListener('keydown', this.handleEscape);
        document.body.classList.remove('sidebar-open');
    }
}
</script>

<style scoped>
/* Smooth transitions for mobile sidebar */
.fixed {
    transition: all 0.3s ease-in-out;
}

/* Prevent body scroll when sidebar is open */
.sidebar-open {
    overflow: hidden;
}
</style>