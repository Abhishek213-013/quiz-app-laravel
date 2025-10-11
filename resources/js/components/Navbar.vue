<template>
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and App Name -->
                <div class="flex items-center space-x-3">
                    <i class="fas fa-brain sidebar-logo"></i>
                    <h1 class="text-xl font-bold text-gray-800">MindSpark</h1>
                </div>

                <!-- Centered Navigation -->
                <div class="flex items-center space-x-8">
                    <button 
                        v-for="item in navigation" 
                        :key="item.route"
                        @click="$emit('navigate', item.route)"
                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        :class="{ 'bg-gray-200 text-gray-900': currentView === item.route }"
                    >
                        {{ item.label }}
                    </button>
                </div>

                <!-- Empty div for balance -->
                <div class="w-24"></div>
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
            ]
        }
    }
}
</script>