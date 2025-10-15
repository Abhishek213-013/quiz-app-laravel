<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">MindSpark Dashboard</h1>
        
            <!-- Enhanced Flexbox Tiles with better responsive classes -->
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 md:gap-8 max-w-4xl mx-auto">
                <!-- Take Quiz Tile -->
                <div 
                    class="bg-white rounded-lg shadow-lg p-4 sm:p-6 text-center cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-xl flex-1 border-2 border-transparent hover:border-blue-200"
                    @click="$emit('navigate', 'quiz-sets')"
                >
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <span class="text-xl sm:text-2xl text-white">📝</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2">Take Quiz</h3>
                    <p class="text-sm sm:text-base text-gray-600">Start a new quiz and test your knowledge</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden">(Mobile Layout)</div>
                </div>

                <!-- GK Blog Tile -->
                <div 
                    class="bg-white rounded-lg shadow-lg p-4 sm:p-6 text-center cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-xl flex-1 border-2 border-transparent hover:border-green-200"
                    @click="$emit('navigate', 'gk-blog')"
                >
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <span class="text-xl sm:text-2xl text-white">📚</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2">GK Blog</h3>
                    <p class="text-sm sm:text-base text-gray-600">Enhance your knowledge with educational articles</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden">(Mobile Layout)</div>
                </div>

                <!-- Previous Records Tile -->
                <div 
                    class="bg-white rounded-lg shadow-lg p-4 sm:p-6 text-center cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-xl flex-1 border-2 border-transparent hover:border-purple-200"
                    @click="$emit('navigate', 'records')"
                >
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <span class="text-xl sm:text-2xl text-white">📊</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2">Previous Records</h3>
                    <p class="text-sm sm:text-base text-gray-600">View and filter previous quiz results</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden">(Mobile Layout)</div>
                </div>
            </div>

            <!-- Responsive Breakpoint Indicator (for debugging) -->
            <div class="mt-8 max-w-4xl mx-auto text-center">
                <div class="inline-block bg-gray-100 rounded-lg px-4 py-2">
                    <div class="text-sm text-gray-600">
                        Current layout: 
                        <span class="font-semibold text-blue-600 sm:hidden">MOBILE (vertical)</span>
                        <span class="font-semibold text-green-600 hidden sm:inline md:hidden">SMALL (horizontal)</span>
                        <span class="font-semibold text-purple-600 hidden md:inline">DESKTOP (horizontal)</span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Section -->
            <div class="mt-16 max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Quick Overview</h2>
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <div class="bg-blue-50 rounded-lg p-4 sm:p-6 text-center flex-1 border-l-4 border-blue-500">
                        <div class="text-2xl sm:text-3xl font-bold text-blue-600 mb-2">{{ totalQuizzes }}</div>
                        <div class="text-gray-600 text-sm sm:text-base">Available Quizzes</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 sm:p-6 text-center flex-1 border-l-4 border-green-500">
                        <div class="text-2xl sm:text-3xl font-bold text-green-600 mb-2">{{ totalArticles }}</div>
                        <div class="text-gray-600 text-sm sm:text-base">GK Articles</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 sm:p-6 text-center flex-1 border-l-4 border-purple-500">
                        <div class="text-2xl sm:text-3xl font-bold text-purple-600 mb-2">{{ totalAttempts }}</div>
                        <div class="text-gray-600 text-sm sm:text-base">Quiz Attempts</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-16 max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Recent Activity</h2>
                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                    <div class="space-y-4">
                        <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center space-x-3 sm:space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                            <div :class="['w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center', activity.bgColor]">
                                <span class="text-white text-xs sm:text-sm">{{ activity.icon }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800 font-medium text-sm sm:text-base">{{ activity.message }}</p>
                                <p class="text-gray-500 text-xs sm:text-sm">{{ activity.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Dashboard',
    data() {
        return {
            totalQuizzes: 5,
            totalArticles: 24,
            totalAttempts: 36,
            recentActivities: [
                {
                    id: 1,
                    message: "New Geography quiz set added",
                    time: "2 hours ago",
                    icon: '🌎',
                    bgColor: "bg-blue-500"
                },
                {
                    id: 2,
                    message: "Science & Technology article published",
                    time: "5 hours ago",
                    icon: '🧪',
                    bgColor: "bg-green-500"
                },
                {
                    id: 3,
                    message: "History quiz completed by 15 participants",
                    time: "1 day ago",
                    icon: '📜',
                    bgColor: "bg-purple-500"
                },
                {
                    id: 4,
                    message: "New World Facts article available",
                    time: "2 days ago",
                    icon: '📖',
                    bgColor: "bg-orange-500"
                }
            ]
        }
    },
    async mounted() {
        await this.fetchDashboardStats();
    },
    methods: {
        async fetchDashboardStats() {
            try {
                // Fetch quiz sets count
                const quizSetsResponse = await axios.get('/api/quiz-sets');
                this.totalQuizzes = quizSetsResponse.data.length;

                // Fetch quiz attempts count
                const recordsResponse = await axios.get('/api/quiz-results');
                this.totalAttempts = recordsResponse.data.length;

                // For articles, we can set a static number or fetch from API if available
                this.totalArticles = 24; // Static count for now

            } catch (error) {
                console.error('Error fetching dashboard stats:', error);
                
                // Fallback to demo data
                this.totalQuizzes = 5;
                this.totalArticles = 24;
                this.totalAttempts = 36;
            }
        }
    }
}
</script>

<style scoped>
/* Enhanced custom styles */
.transform {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}

.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>