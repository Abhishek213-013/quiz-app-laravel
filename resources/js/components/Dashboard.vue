<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-4xl font-bold text-center mb-12 dashboard-title">MindSpark Dashboard</h1>
        
            <!-- Enhanced Flexbox Tiles with better responsive classes -->
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 md:gap-8 max-w-4xl mx-auto">
                <!-- Take Quiz Tile -->
                <div 
                    class="dashboard-tile quiz-tile cursor-pointer transform transition-all duration-300 hover:scale-105"
                    @click="$emit('navigate', 'quiz-sets')"
                >
                    <div class="icon-container bg-blue-500 mb-3 sm:mb-4">
                        <span class="text-xl sm:text-2xl text-white">📝</span>
                    </div>
                    <h3 class="tile-title mb-2">Take Quiz</h3>
                    <p class="tile-description">Start a new quiz and test your knowledge</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden theme-hint">(Mobile Layout)</div>
                </div>

                <!-- GK Blog Tile -->
                <div 
                    class="dashboard-tile blog-tile cursor-pointer transform transition-all duration-300 hover:scale-105"
                    @click="$emit('navigate', 'gk-blog')"
                >
                    <div class="icon-container bg-green-500 mb-3 sm:mb-4">
                        <span class="text-xl sm:text-2xl text-white">📚</span>
                    </div>
                    <h3 class="tile-title mb-2">GK Blog</h3>
                    <p class="tile-description">Enhance your knowledge with educational articles</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden theme-hint">(Mobile Layout)</div>
                </div>

                <!-- Previous Records Tile -->
                <div 
                    class="dashboard-tile records-tile cursor-pointer transform transition-all duration-300 hover:scale-105"
                    @click="$emit('navigate', 'records')"
                >
                    <div class="icon-container bg-purple-500 mb-3 sm:mb-4">
                        <span class="text-xl sm:text-2xl text-white">📊</span>
                    </div>
                    <h3 class="tile-title mb-2">Previous Records</h3>
                    <p class="tile-description">View and filter previous quiz results</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden theme-hint">(Mobile Layout)</div>
                </div>
            </div>

            <!-- Responsive Breakpoint Indicator (for debugging) -->
            <div class="mt-8 max-w-4xl mx-auto text-center">
                <div class="breakpoint-indicator">
                    <div class="text-sm">
                        Current layout: 
                        <span class="font-semibold text-blue-600 sm:hidden">MOBILE (vertical)</span>
                        <span class="font-semibold text-green-600 hidden sm:inline md:hidden">SMALL (horizontal)</span>
                        <span class="font-semibold text-purple-600 hidden md:inline">DESKTOP (horizontal)</span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Section -->
            <div class="mt-16 max-w-4xl mx-auto">
                <h2 class="section-title mb-8">Quick Overview</h2>
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <div class="stat-card blue-card">
                        <div class="stat-number text-blue-600 mb-2">{{ totalQuizzes }}</div>
                        <div class="stat-label">Available Quizzes</div>
                    </div>
                    <div class="stat-card green-card">
                        <div class="stat-number text-green-600 mb-2">{{ totalArticles }}</div>
                        <div class="stat-label">GK Articles</div>
                    </div>
                    <div class="stat-card purple-card">
                        <div class="stat-number text-purple-600 mb-2">{{ totalAttempts }}</div>
                        <div class="stat-label">Quiz Attempts</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-16 max-w-4xl mx-auto">
                <h2 class="section-title mb-8">Recent Activity</h2>
                <div class="activity-container">
                    <div class="space-y-4">
                        <div 
                            v-for="activity in recentActivities" 
                            :key="activity.id" 
                            class="activity-item"
                        >
                            <div :class="['activity-icon', activity.bgColor]">
                                <span class="text-white text-xs sm:text-sm">{{ activity.icon }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="activity-message">{{ activity.message }}</p>
                                <p class="activity-time">{{ activity.time }}</p>
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
    props: {
        theme: {
            type: String,
            default: 'light'
        }
    },
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
    computed: {
        themeClass() {
            return `theme-${this.theme}`;
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
/* Base theme variables */
:root {
    --dashboard-bg: #ffffff;
    --dashboard-title-color: #1f2937;
    --tile-bg: #ffffff;
    --tile-border: transparent;
    --tile-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --tile-title-color: #1f2937;
    --tile-description-color: #4b5563;
    --tile-hover-border: rgba(59, 130, 246, 0.2);
    --stat-card-bg: #f9fafb;
    --activity-container-bg: #ffffff;
    --activity-item-hover: #f9fafb;
    --activity-message-color: #1f2937;
    --activity-time-color: #6b7280;
    --breakpoint-indicator-bg: #f3f4f6;
    --theme-hint-color: #9ca3af;
}

.dark-theme {
    --dashboard-bg: #111827;
    --dashboard-title-color: #f3f4f6;
    --tile-bg: #1f2937;
    --tile-border: #374151;
    --tile-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
    --tile-title-color: #f3f4f6;
    --tile-description-color: #d1d5db;
    --tile-hover-border: rgba(96, 165, 250, 0.3);
    --stat-card-bg: #1f2937;
    --activity-container-bg: #1f2937;
    --activity-item-hover: #374151;
    --activity-message-color: #f3f4f6;
    --activity-time-color: #9ca3af;
    --breakpoint-indicator-bg: #374151;
    --theme-hint-color: #6b7280;
}

/* Base styles */
.flex-1 {
    background-color: var(--dashboard-bg);
    transition: background-color 0.3s ease;
}

.container {
    min-height: 100vh;
}

.dashboard-title {
    color: var(--dashboard-title-color);
    transition: color 0.3s ease;
}

/* Tile styles */
.dashboard-tile {
    background: var(--tile-bg);
    border: 2px solid var(--tile-border);
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: var(--tile-shadow);
    flex: 1;
    transition: all 0.3s ease;
}

@media (min-width: 640px) {
    .dashboard-tile {
        padding: 1.5rem;
    }
}

.dashboard-tile:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--tile-hover-border);
}

.quiz-tile:hover {
    --tile-hover-border: rgba(59, 130, 246, 0.3);
}

.blog-tile:hover {
    --tile-hover-border: rgba(34, 197, 94, 0.3);
}

.records-tile:hover {
    --tile-hover-border: rgba(168, 85, 247, 0.3);
}

.icon-container {
    width: 3rem;
    height: 3rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
    margin-right: auto;
}

@media (min-width: 640px) {
    .icon-container {
        width: 4rem;
        height: 4rem;
    }
}

.tile-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--tile-title-color);
    transition: color 0.3s ease;
}

@media (min-width: 640px) {
    .tile-title {
        font-size: 1.25rem;
    }
}

.tile-description {
    font-size: 0.875rem;
    color: var(--tile-description-color);
    transition: color 0.3s ease;
}

@media (min-width: 640px) {
    .tile-description {
        font-size: 1rem;
    }
}

.theme-hint {
    color: var(--theme-hint-color);
}

/* Breakpoint indicator */
.breakpoint-indicator {
    display: inline-block;
    background: var(--breakpoint-indicator-bg);
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    transition: background-color 0.3s ease;
}

/* Stat cards */
.stat-card {
    background: var(--stat-card-bg);
    border-radius: 0.75rem;
    padding: 1rem;
    flex: 1;
    border-left-width: 4px;
    transition: background-color 0.3s ease;
}

@media (min-width: 640px) {
    .stat-card {
        padding: 1.5rem;
    }
}

.blue-card {
    border-left-color: #3b82f6;
}

.green-card {
    border-left-color: #10b981;
}

.purple-card {
    border-left-color: #8b5cf6;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
}

@media (min-width: 640px) {
    .stat-number {
        font-size: 1.875rem;
    }
}

.stat-label {
    color: var(--tile-description-color);
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

@media (min-width: 640px) {
    .stat-label {
        font-size: 1rem;
    }
}

/* Activity section */
.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    color: var(--dashboard-title-color);
    transition: color 0.3s ease;
}

.activity-container {
    background: var(--activity-container-bg);
    border-radius: 0.75rem;
    box-shadow: var(--tile-shadow);
    padding: 1rem;
    transition: all 0.3s ease;
}

@media (min-width: 640px) {
    .activity-container {
        padding: 1.5rem;
    }
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    transition: background-color 0.2s ease;
}

.activity-item:hover {
    background: var(--activity-item-hover);
}

.activity-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

@media (min-width: 640px) {
    .activity-icon {
        width: 2.5rem;
        height: 2.5rem;
    }
}

.activity-message {
    color: var(--activity-message-color);
    font-weight: 500;
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

@media (min-width: 640px) {
    .activity-message {
        font-size: 1rem;
    }
}

.activity-time {
    color: var(--activity-time-color);
    font-size: 0.75rem;
    transition: color 0.3s ease;
}

@media (min-width: 640px) {
    .activity-time {
        font-size: 0.875rem;
    }
}

/* Transition effects */
.transform {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}
</style>