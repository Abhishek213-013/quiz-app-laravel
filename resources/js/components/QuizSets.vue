<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Simplified Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold quizsets-title">🎯 Choose Quiz Set</h1>
                <p class="quizsets-subtitle mt-2">Select a category to start your quiz</p>
            </div>

            <!-- Stats Overview -->
            <div v-if="quizSets.length > 0" class="stats-grid mb-8">
                <div class="stat-card blue-stat">
                    <div class="stat-number blue-stat-number">{{ quizSets.length }}</div>
                    <div class="stat-label blue-stat-label">Total Sets</div>
                </div>
                <div class="stat-card green-stat">
                    <div class="stat-number green-stat-number">{{ totalQuestions }}</div>
                    <div class="stat-label green-stat-label">Questions</div>
                </div>
                <div class="stat-card purple-stat">
                    <div class="stat-number purple-stat-number">{{ totalTime }}</div>
                    <div class="stat-label purple-stat-label">Total Time</div>
                </div>
                <div class="stat-card orange-stat">
                    <div class="stat-number orange-stat-number">{{ categories.length }}</div>
                    <div class="stat-label orange-stat-label">Categories</div>
                </div>
            </div>

            <!-- Quiz Sets Grid -->
            <div class="quizsets-grid">
                <!-- Quiz Set Cards -->
                <div 
                    v-for="quizSet in quizSets" 
                    :key="quizSet.id"
                    class="quizset-card"
                    @click="selectQuizSet(quizSet.id)"
                >
                    <!-- Category Badge -->
                    <div class="category-section">
                        <span class="category-badge">
                            {{ getCategoryIcon(quizSet.category) }} {{ quizSet.category }}
                        </span>
                        <span class="question-count">
                            ❓ {{ quizSet.question_count }}
                        </span>
                    </div>
                    
                    <!-- Quiz Set Title -->
                    <h3 class="quizset-title">{{ quizSet.name }}</h3>
                    
                    <!-- Description -->
                    <p class="quizset-description">{{ quizSet.description }}</p>
                    
                    <!-- Stats -->
                    <div class="quizset-stats">
                        <span class="stat-item">
                            ⏱️ {{ quizSet.time_limit }}s
                        </span>
                        <span class="stat-item">
                            🕒 {{ Math.floor(quizSet.time_limit / 60) }}min
                        </span>
                        <span class="stat-item">
                            📊 Medium
                        </span>
                    </div>
                    
                    <!-- Start Quiz Button -->
                    <button 
                        class="start-quiz-btn"
                        @click.stop="selectQuizSet(quizSet.id)"
                    >
                        🚀 Start Quiz
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
                <div class="loading-spinner"></div>
                <p class="loading-text">Loading quiz sets...</p>
                <p class="loading-subtext">Getting everything ready for you</p>
            </div>

            <!-- Error State -->
            <div v-if="error" class="error-state">
                <div class="error-content">
                    <div class="error-icon">😞</div>
                    <h3 class="error-title">Oops! Something went wrong</h3>
                    <p class="error-message">{{ error }}</p>
                    <button 
                        @click="fetchQuizSets"
                        class="retry-btn"
                    >
                        🔄 Try Again
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && quizSets.length === 0 && !error" class="empty-state">
                <div class="empty-icon">📚</div>
                <h3 class="empty-title">No Quiz Sets Available</h3>
                <p class="empty-message">Check back later for new quiz sets or contact the administrator.</p>
                <button 
                    @click="fetchQuizSets"
                    class="refresh-btn"
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
    props: {
        theme: {
            type: String,
            default: 'light'
        }
    },
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
            
            const selectedSet = this.quizSets.find(set => set.id === quizSetId);
            if (!selectedSet) {
                this.error = 'Invalid quiz set selected';
                return;
            }
            
            this.$emit('select-set', quizSetId);
            this.showSelectionFeedback(selectedSet.name);
        },

        showSelectionFeedback(quizSetName) {
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
/* Theme Variables */
:root {
    --quizsets-bg: #ffffff;
    --quizsets-title-color: #1f2937;
    --quizsets-subtitle-color: #4b5563;
    --card-bg: #ffffff;
    --card-border: #e5e7eb;
    --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --card-hover-border: #3b82f6;
    --text-primary: #1f2937;
    --text-secondary: #4b5563;
    --text-tertiary: #6b7280;
    --stat-card-bg: #f9fafb;
    --loading-color: #3b82f6;
    --error-bg: #fef2f2;
    --error-border: #fecaca;
    --error-title: #991b1b;
    --error-text: #dc2626;
    --empty-icon-color: #6b7280;
}

.dark-theme {
    --quizsets-bg: #111827;
    --quizsets-title-color: #f3f4f6;
    --quizsets-subtitle-color: #d1d5db;
    --card-bg: #1f2937;
    --card-border: #374151;
    --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
    --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
    --card-hover-border: #60a5fa;
    --text-primary: #f3f4f6;
    --text-secondary: #d1d5db;
    --text-tertiary: #9ca3af;
    --stat-card-bg: #1f2937;
    --loading-color: #60a5fa;
    --error-bg: #1f2937;
    --error-border: #7f1d1d;
    --error-title: #fca5a5;
    --error-text: #fca5a5;
    --empty-icon-color: #9ca3af;
}

/* Base Styles */
.flex-1 {
    background-color: var(--quizsets-bg);
    min-height: 100vh;
    transition: background-color 0.3s ease;
}

.container {
    transition: all 0.3s ease;
}

/* Header */
.quizsets-title {
    color: var(--quizsets-title-color);
    transition: color 0.3s ease;
}

.quizsets-subtitle {
    color: var(--quizsets-subtitle-color);
    transition: color 0.3s ease;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (min-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.stat-card {
    background: var(--stat-card-bg);
    padding: 1rem;
    border-radius: 0.5rem;
    text-align: center;
    transition: background-color 0.3s ease;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
}

/* Theme-specific stat colors */
.blue-stat-number { color: #3b82f6; }
.dark-theme .blue-stat-number { color: #60a5fa; }
.blue-stat-label { color: var(--text-secondary); }

.green-stat-number { color: #10b981; }
.dark-theme .green-stat-number { color: #34d399; }
.green-stat-label { color: var(--text-secondary); }

.purple-stat-number { color: #8b5cf6; }
.dark-theme .purple-stat-number { color: #a78bfa; }
.purple-stat-label { color: var(--text-secondary); }

.orange-stat-number { color: #f59e0b; }
.dark-theme .orange-stat-number { color: #fbbf24; }
.orange-stat-label { color: var(--text-secondary); }

/* Quiz Sets Grid */
.quizsets-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .quizsets-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .quizsets-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Quiz Set Card */
.quizset-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 0.75rem;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: fadeIn 0.6s ease-out;
}

.quizset-card:hover {
    transform: scale(1.05);
    box-shadow: var(--card-hover-shadow);
    border-color: var(--card-hover-border);
}

/* Category Section */
.category-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.category-badge {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.dark-theme .category-badge {
    background: linear-gradient(135deg, #60a5fa, #2563eb);
}

.question-count {
    background: var(--stat-card-bg);
    color: var(--text-tertiary);
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

/* Quiz Set Content */
.quizset-title {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: bold;
    margin-bottom: 0.75rem;
    transition: color 0.3s ease;
}

.quizset-description {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 1rem;
    transition: color 0.3s ease;
}

/* Stats */
.quizset-stats {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.875rem;
    color: var(--text-tertiary);
    border-top: 1px solid var(--card-border);
    padding-top: 0.75rem;
    transition: color 0.3s ease, border-color 0.3s ease;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Start Quiz Button */
.start-quiz-btn {
    width: 100%;
    margin-top: 1rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    font-weight: 600;
    padding: 0.75rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.start-quiz-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.dark-theme .start-quiz-btn {
    background: linear-gradient(135deg, #34d399, #10b981);
}

.dark-theme .start-quiz-btn:hover {
    background: linear-gradient(135deg, #10b981, #059669);
}

/* Loading State */
.loading-state {
    text-align: center;
    padding: 3rem 0;
}

.loading-spinner {
    display: inline-block;
    width: 3rem;
    height: 3rem;
    border: 4px solid var(--loading-color);
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-text {
    color: var(--text-primary);
    margin-top: 1rem;
    font-size: 1.125rem;
    transition: color 0.3s ease;
}

.loading-subtext {
    color: var(--text-tertiary);
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

/* Error State */
.error-state {
    text-align: center;
    padding: 2rem 0;
}

.error-content {
    background: var(--error-bg);
    border: 1px solid var(--error-border);
    border-radius: 0.75rem;
    padding: 1.5rem;
    max-width: 28rem;
    margin: 0 auto;
}

.error-icon {
    font-size: 2.25rem;
    margin-bottom: 1rem;
}

.error-title {
    color: var(--error-title);
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.error-message {
    color: var(--error-text);
    margin-bottom: 1rem;
}

.retry-btn {
    background: #ef4444;
    color: white;
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.retry-btn:hover {
    background: #dc2626;
}

.dark-theme .retry-btn {
    background: #dc2626;
}

.dark-theme .retry-btn:hover {
    background: #b91c1c;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 0;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--empty-icon-color);
    transition: color 0.3s ease;
}

.empty-title {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    transition: color 0.3s ease;
}

.empty-message {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    transition: color 0.3s ease;
}

.refresh-btn {
    background: #3b82f6;
    color: white;
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.refresh-btn:hover {
    background: #2563eb;
}

.dark-theme .refresh-btn {
    background: #60a5fa;
}

.dark-theme .refresh-btn:hover {
    background: #3b82f6;
}

/* Animations */
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

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Staggered animations for cards */
.quizsets-grid > div:nth-child(1) { animation-delay: 0.1s; }
.quizsets-grid > div:nth-child(2) { animation-delay: 0.2s; }
.quizsets-grid > div:nth-child(3) { animation-delay: 0.3s; }
.quizsets-grid > div:nth-child(4) { animation-delay: 0.4s; }
.quizsets-grid > div:nth-child(5) { animation-delay: 0.5s; }
.quizsets-grid > div:nth-child(6) { animation-delay: 0.6s; }

/* Accessibility */
button:focus {
    outline: 2px solid var(--card-hover-border);
    outline-offset: 2px;
}

/* Gradient animation for category badges */
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.category-badge {
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
}
</style>