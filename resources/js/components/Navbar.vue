<template>
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex-shrink-0 flex items-center">
                    <!-- Logo Image -->
                    <img 
                        src="/images/logo.png" 
                        alt="Quiz App Logo"
                        class="h-8 w-8 mr-3"
                        @click="goToDashboard"
                    />
                    <!-- Brand Name -->
                    <h1 class="text-xl font-bold text-gray-800 cursor-pointer" @click="goToDashboard">
                        MindSpark
                    </h1>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <button
                            v-for="item in navigation" 
                            :key="item.name"
                            @click="navigateTo(item.route)"
                            :class="[
                                'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200',
                                currentView === item.route 
                                    ? 'bg-blue-500 text-white' 
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600'
                            ]"
                        >
                            {{ item.name }}
                        </button>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button 
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path 
                                :class="{'hidden': isMobileMenuOpen, 'block': !isMobileMenuOpen}" 
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path 
                                :class="{'hidden': !isMobileMenuOpen, 'block': isMobileMenuOpen}" 
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="isMobileMenuOpen" class="md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t">
                    <button
                        v-for="item in navigation" 
                        :key="item.name"
                        @click="navigateTo(item.route)"
                        :class="[
                            'block w-full text-left px-3 py-2 rounded-md text-base font-medium transition-colors duration-200',
                            currentView === item.route 
                                ? 'bg-blue-500 text-white' 
                                : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600'
                        ]"
                    >
                        {{ item.name }}
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'Navbar',
    props: {
        currentView: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            isMobileMenuOpen: false,
            navigation: [
                { name: 'Take Quiz', route: 'quiz-sets' },
                { name: 'Manage Quizzes', route: 'manage-quizzes' },
                { name: 'Previous Records', route: 'records' }
            ]
        }
    },
    methods: {
        navigateTo(route) {
            this.isMobileMenuOpen = false;
            this.$emit('navigate', route);
        },
        
        goToDashboard() {
            this.$emit('navigate', 'dashboard');
        }
    }
}
</script>