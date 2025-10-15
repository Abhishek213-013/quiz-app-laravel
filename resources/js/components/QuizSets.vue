<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Simplified Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">🎯 Choose Quiz Set</h1>
                <p class="text-gray-600 mt-2">Select a category to start your quiz</p>
            </div>

            <!-- Stats Overview -->
            <div v-if="quizSets.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ quizSets.length }}</div>
                    <div class="text-sm text-blue-600">Total Sets</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">{{ totalQuestions }}</div>
                    <div class="text-sm text-green-600">Questions</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ totalTime }}</div>
                    <div class="text-sm text-purple-600">Total Time</div>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ categories.length }}</div>
                    <div class="text-sm text-orange-600">Categories</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Quiz Set Cards -->
                <div 
                    v-for="quizSet in quizSets" 
                    :key="quizSet.id"
                    class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-xl"
                    @click="selectQuizSet(quizSet.id)"
                >
                    <!-- Category Badge -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-full shadow-sm">
                            {{ getCategoryIcon(quizSet.category) }} {{ quizSet.category }}
                        </span>
                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">
                            ❓ {{ quizSet.question_count }}
                        </span>
                    </div>
                    
                    <!-- Quiz Set Title -->
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ quizSet.name }}</h3>
                    
                    <!-- Description -->
                    <p class="text-gray-600 mb-4 leading-relaxed">{{ quizSet.description }}</p>
                    
                    <!-- Stats -->
                    <div class="flex items-center justify-between text-sm text-gray-500 border-t border-gray-100 pt-3">
                        <span class="flex items-center">
                            ⏱️ {{ quizSet.time_limit }}s
                        </span>
                        <span class="flex items-center">
                            🕒 {{ Math.floor(quizSet.time_limit / 60) }}min
                        </span>
                        <span class="flex items-center">
                            📊 Medium
                        </span>
                    </div>
                    
                    <!-- Start Quiz Button -->
                    <button 
                        class="w-full mt-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-md"
                        @click.stop="selectQuizSet(quizSet.id)"
                    >
                        🚀 Start Quiz
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
                <p class="mt-4 text-gray-600 text-lg">Loading quiz sets...</p>
                <p class="text-sm text-gray-500">Getting everything ready for you</p>
            </div>

            <!-- Error State -->
            <div v-if="error" class="text-center py-8">
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
                    <div class="text-4xl mb-4">😞</div>
                    <h3 class="text-lg font-semibold text-red-800 mb-2">Oops! Something went wrong</h3>
                    <p class="text-red-600 mb-4">{{ error }}</p>
                    <button 
                        @click="fetchQuizSets"
                        class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                    >
                        🔄 Try Again
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && quizSets.length === 0 && !error" class="text-center py-12">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Quiz Sets Available</h3>
                <p class="text-gray-600 mb-6">Check back later for new quiz sets or contact the administrator.</p>
                <button 
                    @click="fetchQuizSets"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                >
                    🔄 Refresh
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'QuizSets',
    data() {
        return {
            quizSets: [],
            loading: false,
            error: null
        }
    },
    computed: {
        totalQuestions() {
            return this.quizSets.reduce((total, set) => total + (set.question_count || 0), 0);
        },
        totalTime() {
            const totalSeconds = this.quizSets.reduce((total, set) => total + (set.time_limit || 0), 0);
            const minutes = Math.floor(totalSeconds / 60);
            return minutes > 0 ? `${minutes}m` : `${totalSeconds}s`;
        },
        categories() {
            const uniqueCategories = new Set(this.quizSets.map(set => set.category));
            return Array.from(uniqueCategories);
        }
    },
    async mounted() {
        await this.fetchQuizSets();
    },
    methods: {
        async fetchQuizSets() {
            this.loading = true;
            this.error = null;
            
            try {
                console.log('🔄 Fetching quiz sets...');
                const response = await fetch('/api/quiz-sets');
                
                if (!response.ok) {
                    throw new Error(`Failed to fetch quiz sets: ${response.status} ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('✅ Quiz sets loaded:', data);
                
                this.quizSets = Array.isArray(data) ? data : [];
                
                if (this.quizSets.length === 0) {
                    console.log('ℹ️ No quiz sets found');
                }
                
            } catch (error) {
                console.error('❌ Error fetching quiz sets:', error);
                this.error = error.message || 'Failed to load quiz sets. Please check your connection and try again.';
                this.quizSets = [];
            } finally {
                this.loading = false;
            }
        },

        selectQuizSet(quizSetId) {
            console.log('🎯 Selected quiz set:', quizSetId);
            
            // Validate quiz set ID
            const selectedSet = this.quizSets.find(set => set.id === quizSetId);
            if (!selectedSet) {
                this.error = 'Invalid quiz set selected';
                return;
            }
            
            // Emit event to parent component to handle navigation
            this.$emit('select-set', quizSetId);
            
            // Optional: Show selection feedback
            this.showSelectionFeedback(selectedSet.name);
        },

        showSelectionFeedback(quizSetName) {
            // You can add a toast notification or other feedback here
            console.log(`🚀 Starting quiz: ${quizSetName}`);
        },

        getCategoryIcon(category) {
            const iconMap = {
                'Science': '🔬',
                'Geography': '🌍',
                'History': '📜',
                'Mathematics': '🧮',
                'Technology': '💻',
                'Sports': '⚽',
                'Entertainment': '🎬',
                'General': '🌟',
                'Literature': '📚',
                'Art': '🎨',
                'Music': '🎵',
                'Biology': '🧬',
                'Chemistry': '⚗️',
                'Physics': '⚛️',
                'World': '🗺️',
                'Current Affairs': '📰'
            };
            
            return iconMap[category] || '📝';
        }
    }
}
</script>

<style scoped>
/* Custom animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Apply animation to quiz set cards */
.grid > div {
    animation: fadeIn 0.6s ease-out;
}

/* Stagger animation for cards */
.grid > div:nth-child(1) { animation-delay: 0.1s; }
.grid > div:nth-child(2) { animation-delay: 0.2s; }
.grid > div:nth-child(3) { animation-delay: 0.3s; }
.grid > div:nth-child(4) { animation-delay: 0.4s; }
.grid > div:nth-child(5) { animation-delay: 0.5s; }
.grid > div:nth-child(6) { animation-delay: 0.6s; }

/* Hover effects */
.bg-white {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.bg-white:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: #3b82f6;
}

/* Loading animation enhancement */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Gradient text for category badges */
.bg-gradient-to-r {
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .grid {
        gap: 1rem;
    }
    
    .text-3xl {
        font-size: 1.875rem;
    }
}

/* Focus states for accessibility */
button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Smooth transitions for all interactive elements */
button, .bg-white {
    transition: all 0.3s ease;
}
</style>