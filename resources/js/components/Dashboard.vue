<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Quiz App Dashboard</h1>
        
            <!-- Flexbox Tiles -->
            <div class="flex flex-col md:flex-row gap-8 max-w-4xl mx-auto">
                <!-- Take Quiz Tile -->
                <div 
                    class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl flex-1"
                    @click="$emit('navigate', 'quiz-sets')"
                >
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Take Quiz</h3>
                    <p class="text-gray-600">Start a new quiz and test your knowledge</p>
                </div>

                <!-- GK Blog Tile -->
                <div 
                    class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl flex-1"
                    @click="$emit('navigate', 'gk-blog')"
                >
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">GK Blog</h3>
                    <p class="text-gray-600">Enhance your knowledge with educational articles</p>
                </div>

                <!-- Previous Records Tile -->
                <div 
                    class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl flex-1"
                    @click="$emit('navigate', 'records')"
                >
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Previous Records</h3>
                    <p class="text-gray-600">View and filter previous quiz results</p>
                </div>
            </div>

            <!-- Quick Stats Section - FIXED WITH FLEXBOX -->
            <div class="mt-16 max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Quick Overview</h2>
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="bg-blue-50 rounded-lg p-6 text-center flex-1">
                        <div class="text-3xl font-bold text-blue-600 mb-2">{{ totalQuizzes }}</div>
                        <div class="text-gray-600">Available Quizzes</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-6 text-center flex-1">
                        <div class="text-3xl font-bold text-green-600 mb-2">{{ totalArticles }}</div>
                        <div class="text-gray-600">GK Articles</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-6 text-center flex-1">
                        <div class="text-3xl font-bold text-purple-600 mb-2">{{ totalAttempts }}</div>
                        <div class="text-gray-600">Quiz Attempts</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-16 max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Recent Activity</h2>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="space-y-4">
                        <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg">
                            <div :class="['w-10 h-10 rounded-full flex items-center justify-center', activity.bgColor]">
                                <i :class="[activity.icon, 'text-white text-sm']"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800 font-medium">{{ activity.message }}</p>
                                <p class="text-gray-500 text-sm">{{ activity.time }}</p>
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
                    icon: "fas fa-globe-americas",
                    bgColor: "bg-blue-500"
                },
                {
                    id: 2,
                    message: "Science & Technology article published",
                    time: "5 hours ago",
                    icon: "fas fa-flask",
                    bgColor: "bg-green-500"
                },
                {
                    id: 3,
                    message: "History quiz completed by 15 participants",
                    time: "1 day ago",
                    icon: "fas fa-history",
                    bgColor: "bg-purple-500"
                },
                {
                    id: 4,
                    message: "New World Facts article available",
                    time: "2 days ago",
                    icon: "fas fa-book-open",
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
                
                // Fallback to demo data (matching your screenshot)
                this.totalQuizzes = 5;
                this.totalArticles = 24;
                this.totalAttempts = 36;
            }
        }
    }
}
</script>

<style scoped>
/* Custom styles for smooth transitions */
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