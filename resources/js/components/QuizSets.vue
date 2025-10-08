<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
        <!-- Simplified Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Choose Quiz Set</h1>
            <p class="text-gray-600 mt-2">Select a category to start your quiz</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Quiz Set Cards -->
            <div 
                v-for="quizSet in quizSets" 
                :key="quizSet.id"
                class="bg-white rounded-lg shadow-md p-6 cursor-pointer transform transition-transform hover:scale-105 hover:shadow-lg"
                @click="selectQuizSet(quizSet.id)"
            >
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                        {{ quizSet.category }}
                    </span>
                    <span class="text-sm text-gray-500">{{ quizSet.question_count }} questions</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ quizSet.name }}</h3>
                <p class="text-gray-600 mb-4">{{ quizSet.description }}</p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>Time: {{ quizSet.time_limit }}s</span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ Math.floor(quizSet.time_limit / 60) }}min
                    </span>
                </div>
            </div>
        </div>

        <!-- Loading and Error states -->
        <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Loading quiz sets...</p>
        </div>

        <div v-if="error" class="text-center py-8 text-red-600">
            {{ error }}
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
    async mounted() {
        await this.fetchQuizSets();
    },
    methods: {
        async fetchQuizSets() {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await fetch('/api/quiz-sets');
                if (!response.ok) {
                    throw new Error('Failed to fetch quiz sets');
                }
                this.quizSets = await response.json();
            } catch (error) {
                console.error('Error fetching quiz sets:', error);
                this.error = 'Failed to load quiz sets. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        selectQuizSet(quizSetId) {
            // Emit event to parent component to handle navigation
            this.$emit('select-set', quizSetId);
        }
    }
}
</script>