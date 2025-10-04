<template>
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <button 
                @click="$emit('back')"
                class="flex items-center text-blue-600 hover:text-blue-800"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </button>
            <h1 class="text-3xl font-bold text-gray-800">Previous Records</h1>
            <div class="w-20"></div> <!-- Spacer for alignment -->
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Results</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Participant Name</label>
                    <input 
                        v-model="filters.participant_name"
                        type="text" 
                        placeholder="Search by name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Set</label>
                    <select 
                        v-model="filters.quiz_set_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Sets</option>
                        <option 
                            v-for="quizSet in quizSets" 
                            :key="quizSet.id" 
                            :value="quizSet.id"
                        >
                            {{ quizSet.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input 
                        v-model="filters.date"
                        type="date" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <div class="flex items-end">
                    <button 
                        @click="applyFilters"
                        class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                    >
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Participant
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quiz Set
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Score
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Percentage
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Time Taken
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="result in results" :key="result.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ result.participant_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ result.quiz_set.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ result.score }}/{{ result.total_questions }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium" 
                                     :class="getPercentageColor(result.percentage)">
                                    {{ result.percentage }}%
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ formatTime(result.time_taken) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ formatDate(result.created_at) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button 
                                    @click="viewDetails(result)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    View Details
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="results.length === 0 && !loading" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                <p class="mt-1 text-sm text-gray-500">Try changing your filters or take a quiz first.</p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-sm text-gray-600">Loading results...</p>
            </div>
        </div>

        <!-- Result Details Modal -->
        <div v-if="selectedResult" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Quiz Result Details</h3>
                    <button 
                        @click="selectedResult = null"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Summary -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ selectedResult.score }}/{{ selectedResult.total_questions }}</div>
                        <div class="text-sm text-blue-600">Score</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600">{{ selectedResult.percentage }}%</div>
                        <div class="text-sm text-green-600">Percentage</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ formatTime(selectedResult.time_taken) }}</div>
                        <div class="text-sm text-purple-600">Time Taken</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ formatDate(selectedResult.created_at) }}</div>
                        <div class="text-sm text-yellow-600">Date</div>
                    </div>
                </div>

                <!-- Answers -->
                <div class="space-y-4">
                    <div 
                        v-for="(answer, questionIndex) in selectedResult.answers" 
                        :key="questionIndex"
                        class="border border-gray-200 rounded-lg p-4"
                        :class="{'bg-green-50 border-green-200': isAnswerCorrect(questionIndex), 'bg-red-50 border-red-200': !isAnswerCorrect(questionIndex)}"
                    >
                        <h4 class="font-semibold text-gray-800 mb-2">Question {{ parseInt(questionIndex) + 1 }}</h4>
                        <p class="text-gray-600 mb-2">{{ getQuestionText(questionIndex) }}</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div 
                                v-for="(option, optIndex) in getQuestionOptions(questionIndex)" 
                                :key="optIndex"
                                class="p-2 border border-gray-200 rounded text-sm"
                                :class="{
                                    'bg-green-100 border-green-500': option === getCorrectAnswer(questionIndex),
                                    'bg-red-100 border-red-500': option === answer && option !== getCorrectAnswer(questionIndex),
                                    'bg-blue-100 border-blue-500': option === answer && option === getCorrectAnswer(questionIndex)
                                }"
                            >
                                <span class="font-medium">{{ String.fromCharCode(65 + optIndex) }}.</span> {{ option }}
                                <span v-if="option === getCorrectAnswer(questionIndex)" class="ml-2 text-green-600 text-xs">✓ Correct</span>
                                <span v-if="option === answer && option !== getCorrectAnswer(questionIndex)" class="ml-2 text-red-600 text-xs">✗ Your Answer</span>
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
    name: 'Records',
    data() {
        return {
            results: [],
            quizSets: [],
            filters: {
                participant_name: '',
                quiz_set_id: '',
                date: ''
            },
            loading: false,
            selectedResult: null,
            quizDetails: {} // Store quiz questions for details view
        }
    },
    async mounted() {
        await this.fetchQuizSets();
        await this.fetchResults();
    },
    methods: {
        async fetchResults() {
            this.loading = true;
            try {
                const queryParams = new URLSearchParams();
                if (this.filters.participant_name) queryParams.append('participant_name', this.filters.participant_name);
                if (this.filters.quiz_set_id) queryParams.append('quiz_set_id', this.filters.quiz_set_id);
                if (this.filters.date) queryParams.append('date', this.filters.date);

                const url = queryParams.toString() 
                    ? `/api/quiz-results/filter?${queryParams}`
                    : '/api/quiz-results';

                const response = await fetch(url);
                this.results = await response.json();
            } catch (error) {
                console.error('Error fetching results:', error);
                alert('Failed to load results');
            } finally {
                this.loading = false;
            }
        },

        async fetchQuizSets() {
            try {
                const response = await fetch('/api/quiz-sets');
                this.quizSets = await response.json();
            } catch (error) {
                console.error('Error fetching quiz sets:', error);
            }
        },

        applyFilters() {
            this.fetchResults();
        },

        getPercentageColor(percentage) {
            if (percentage >= 80) return 'text-green-600';
            if (percentage >= 60) return 'text-yellow-600';
            return 'text-red-600';
        },

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },

        async viewDetails(result) {
            this.selectedResult = result;
            
            // Fetch quiz details for this result if not already cached
            if (!this.quizDetails[result.quiz_set_id]) {
                try {
                    const response = await fetch(`/api/quiz-sets/${result.quiz_set_id}/quizzes`);
                    this.quizDetails[result.quiz_set_id] = await response.json();
                } catch (error) {
                    console.error('Error fetching quiz details:', error);
                }
            }
        },

        isAnswerCorrect(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            if (!quizSetQuizzes) return false;
            
            const quiz = quizSetQuizzes[questionIndex];
            const userAnswer = this.selectedResult.answers[questionIndex];
            
            return quiz && userAnswer === quiz.correct_answer;
        },

        getQuestionText(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            return quizSetQuizzes && quizSetQuizzes[questionIndex] 
                ? quizSetQuizzes[questionIndex].question 
                : 'Question not available';
        },

        getQuestionOptions(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            return quizSetQuizzes && quizSetQuizzes[questionIndex] 
                ? quizSetQuizzes[questionIndex].options 
                : [];
        },

        getCorrectAnswer(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            return quizSetQuizzes && quizSetQuizzes[questionIndex] 
                ? quizSetQuizzes[questionIndex].correct_answer 
                : '';
        }
    }
}
</script>