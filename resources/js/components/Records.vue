<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Previous Records</h1>
                <p class="text-gray-600 mt-2">View and filter previous quiz results</p>
            </div>

            <!-- Error State -->
            <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800">{{ error }}</span>
                    <button @click="retryLoading" class="ml-4 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                        Retry
                    </button>
                </div>
            </div>

            <!-- Filters - Horizontal Layout -->
            <div v-if="!error" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Results</h2>
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <!-- Search by Name -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search by name</label>
                        <input 
                            v-model="filters.participant_name"
                            type="text" 
                            placeholder="Enter participant name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @input="applyFilters"
                        >
                    </div>
                    
                    <!-- Quiz Set -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Set</label>
                        <select 
                            v-model="filters.quiz_set_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="applyFilters"
                        >
                            <option value="">All Sets</option>
                            <option 
                                v-for="quizSet in quizSets" 
                                :key="quizSet.id" 
                                :value="quizSet.id"
                            >
                                {{ getQuizSetName(quizSet) }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Date -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input 
                            v-model="filters.date"
                            type="date" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="applyFilters"
                        >
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button 
                            @click="clearFilters"
                            class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors font-medium"
                        >
                            Clear
                        </button>
                        <button 
                            @click="applyFilters"
                            class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors font-medium"
                        >
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Summary -->
            <div v-if="!error && results.length > 0" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ results.length }}</div>
                        <div class="text-sm text-gray-600">Total Results</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ calculateAverageScore() }}%
                        </div>
                        <div class="text-sm text-gray-600">Average Score</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ getTopScorer() }}
                        </div>
                        <div class="text-sm text-gray-600">Top Scorer</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ getMostPopularQuizSet() }}
                        </div>
                        <div class="text-sm text-gray-600">Most Popular Set</div>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div v-if="!error" class="bg-white rounded-lg shadow-md overflow-hidden">
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
                                    <div class="text-sm font-medium text-gray-900">{{ result.participant_name || 'Unknown' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ getResultQuizSetName(result) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ (result.score || 0) }}/{{ (result.total_questions || 0) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium" 
                                         :class="getPercentageColor(getSafePercentage(result))">
                                        {{ formatPercentage(getSafePercentage(result)) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ formatTime(result.time_taken || 0) }}
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
                                        :disabled="!result.quiz_set_id"
                                    >
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="results.length === 0 && !loading && !error" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try changing your filters or take a quiz first.</p>
                </div>

                <!-- Loading State -->
                <div v-if="loading && !error" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-sm text-gray-600">Loading results...</p>
                </div>
            </div>

            <!-- Result Details Modal -->
            <div v-if="selectedResult" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="closeModal">
                <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Quiz Result Details</h3>
                        <button 
                            @click="closeModal"
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
                            <div class="text-2xl font-bold text-blue-600">{{ selectedResult.score || 0 }}/{{ selectedResult.total_questions || 0 }}</div>
                            <div class="text-sm text-blue-600">Score</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600">{{ formatPercentage(getSafePercentage(selectedResult)) }}</div>
                            <div class="text-sm text-green-600">Percentage</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ formatTime(selectedResult.time_taken || 0) }}</div>
                            <div class="text-sm text-purple-600">Time Taken</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ formatDate(selectedResult.created_at) }}</div>
                            <div class="text-sm text-yellow-600">Date</div>
                        </div>
                    </div>

                    <!-- Performance Rating -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Performance Rating</h4>
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div 
                                    class="h-2 rounded-full" 
                                    :class="getPerformanceBarColor(getSafePercentage(selectedResult))"
                                    :style="{ width: Math.min(getSafePercentage(selectedResult), 100) + '%' }"
                                ></div>
                            </div>
                            <span class="ml-4 text-sm font-medium" :class="getPerformanceTextColor(getSafePercentage(selectedResult))">
                                {{ getPerformanceRating(getSafePercentage(selectedResult)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Question Statistics -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Question Breakdown</h4>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ getCorrectCount() }}</div>
                                <div class="text-sm text-green-600">Correct</div>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ getWrongCount() }}</div>
                                <div class="text-sm text-red-600">Wrong</div>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ getSkippedCount() }}</div>
                                <div class="text-sm text-blue-600">Skipped</div>
                            </div>
                        </div>
                    </div>

                    <!-- Answers -->
                    <div v-if="selectedResult.answers && getTotalQuestions() > 0" class="space-y-4">
                        <div 
                            v-for="(answer, questionIndex) in getAllQuestions()" 
                            :key="questionIndex"
                            class="border border-gray-200 rounded-lg p-4"
                            :class="getQuestionStatusClass(questionIndex)"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="font-semibold text-gray-800">Question {{ parseInt(questionIndex) + 1 }}</h4>
                                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getQuestionStatusBadgeClass(questionIndex)">
                                    {{ getQuestionStatus(questionIndex) }}
                                </span>
                            </div>
                            <p class="text-gray-600 mb-2">{{ getQuestionText(questionIndex) }}</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div 
                                    v-for="(option, optIndex) in getQuestionOptions(questionIndex)" 
                                    :key="optIndex"
                                    class="p-2 border border-gray-200 rounded text-sm"
                                    :class="getOptionClass(questionIndex, option, answer)"
                                >
                                    <span class="font-medium">{{ String.fromCharCode(65 + optIndex) }}.</span> {{ option }}
                                    <span v-if="option === getCorrectAnswer(questionIndex)" class="ml-2 text-green-600 text-xs">✓ Correct</span>
                                    <span v-if="option === answer && option !== getCorrectAnswer(questionIndex)" class="ml-2 text-red-600 text-xs">✗ Your Answer</span>
                                    <span v-if="!answer && option === getCorrectAnswer(questionIndex)" class="ml-2 text-blue-600 text-xs">⏭️ Skipped</span>
                                </div>
                            </div>
                            <div v-if="!answer" class="mt-2 text-sm text-blue-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Question was skipped
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No answer details available for this result.
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
            error: null,
            selectedResult: null,
            quizDetails: {}
        }
    },
    async mounted() {
        try {
            await this.fetchQuizSets();
            await this.fetchResults();
        } catch (error) {
            console.error('Error in mounted:', error);
            this.error = 'Failed to initialize component: ' + error.message;
        }
    },
    methods: {
        // Close modal when clicking outside
        closeModal() {
            this.selectedResult = null;
        },

        // Get all questions (including skipped ones)
        getAllQuestions() {
            if (!this.selectedResult || !this.quizDetails[this.selectedResult.quiz_set_id]) {
                return {};
            }
            
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            const allQuestions = {};
            
            // Create an object with all questions, including those that were skipped
            for (let i = 0; i < quizSetQuizzes.length; i++) {
                allQuestions[i] = this.selectedResult.answers[i] || null; // null means skipped
            }
            
            return allQuestions;
        },

        // Get total number of questions
        getTotalQuestions() {
            if (!this.selectedResult || !this.quizDetails[this.selectedResult.quiz_set_id]) {
                return 0;
            }
            return this.quizDetails[this.selectedResult.quiz_set_id].length;
        },

        // Get correct answers count
        getCorrectCount() {
            const allQuestions = this.getAllQuestions();
            let correct = 0;
            
            Object.keys(allQuestions).forEach(index => {
                if (this.isAnswerCorrect(parseInt(index))) {
                    correct++;
                }
            });
            
            return correct;
        },

        // Get wrong answers count
        getWrongCount() {
            const allQuestions = this.getAllQuestions();
            let wrong = 0;
            
            Object.keys(allQuestions).forEach(index => {
                const answer = allQuestions[index];
                if (answer !== null && !this.isAnswerCorrect(parseInt(index))) {
                    wrong++;
                }
            });
            
            return wrong;
        },

        // Get skipped questions count
        getSkippedCount() {
            const allQuestions = this.getAllQuestions();
            let skipped = 0;
            
            Object.keys(allQuestions).forEach(index => {
                if (allQuestions[index] === null) {
                    skipped++;
                }
            });
            
            return skipped;
        },

        // Get question status (Correct, Wrong, Skipped)
        getQuestionStatus(questionIndex) {
            const answer = this.getAllQuestions()[questionIndex];
            
            if (answer === null) {
                return 'Skipped';
            } else if (this.isAnswerCorrect(questionIndex)) {
                return 'Correct';
            } else {
                return 'Wrong';
            }
        },

        // Get question status class for styling
        getQuestionStatusClass(questionIndex) {
            const status = this.getQuestionStatus(questionIndex);
            
            switch (status) {
                case 'Correct':
                    return 'bg-green-50 border-green-200';
                case 'Wrong':
                    return 'bg-red-50 border-red-200';
                case 'Skipped':
                    return 'bg-blue-50 border-blue-200';
                default:
                    return 'bg-gray-50 border-gray-200';
            }
        },

        // Get question status badge class
        getQuestionStatusBadgeClass(questionIndex) {
            const status = this.getQuestionStatus(questionIndex);
            
            switch (status) {
                case 'Correct':
                    return 'bg-green-100 text-green-800';
                case 'Wrong':
                    return 'bg-red-100 text-red-800';
                case 'Skipped':
                    return 'bg-blue-100 text-blue-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        },

        // Get option class for styling
        getOptionClass(questionIndex, option, userAnswer) {
            const correctAnswer = this.getCorrectAnswer(questionIndex);
            const isCorrect = option === correctAnswer;
            const isUserAnswer = option === userAnswer;
            const isSkipped = userAnswer === null;
            
            if (isCorrect && isUserAnswer) {
                return 'bg-green-100 border-green-500'; // Correct answer selected
            } else if (isCorrect && !isUserAnswer) {
                return 'bg-green-100 border-green-300'; // Correct answer not selected
            } else if (!isCorrect && isUserAnswer) {
                return 'bg-red-100 border-red-500'; // Wrong answer selected
            } else if (isSkipped && isCorrect) {
                return 'bg-blue-100 border-blue-300'; // Correct answer for skipped question
            } else {
                return 'bg-gray-50 border-gray-200'; // Other options
            }
        },

        // Safe percentage getter
        getSafePercentage(result) {
            if (!result) return 0;
            
            let percentage = result.percentage;
            
            // If percentage is null/undefined, calculate it from score and total_questions
            if (percentage == null) {
                const score = Number(result.score) || 0;
                const total = Number(result.total_questions) || 1; // Avoid division by zero
                percentage = (score / total) * 100;
            }
            
            // Ensure it's a number
            percentage = Number(percentage);
            
            // Handle NaN cases
            if (isNaN(percentage)) {
                return 0;
            }
            
            return Math.min(Math.max(percentage, 0), 100); // Clamp between 0-100
        },

        // Safe percentage formatter
        formatPercentage(percentage) {
            const perc = Number(percentage) || 0;
            return perc.toFixed(1) + '%';
        },

        async fetchResults() {
            this.loading = true;
            this.error = null;
            
            try {
                const queryParams = new URLSearchParams();
                if (this.filters.participant_name) queryParams.append('participant_name', this.filters.participant_name);
                if (this.filters.quiz_set_id) queryParams.append('quiz_set_id', this.filters.quiz_set_id);
                if (this.filters.date) queryParams.append('date', this.filters.date);

                const url = queryParams.toString() 
                    ? `/api/quiz-results/filter?${queryParams}`
                    : '/api/quiz-results';

                console.log('Fetching results from:', url);

                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Fetched results:', data);
                
                // Ensure results is always an array and has safe percentage values
                this.results = Array.isArray(data) ? data.map(result => ({
                    ...result,
                    // Ensure percentage is always a number
                    percentage: this.getSafePercentage(result)
                })) : [];
            } catch (error) {
                console.error('Error fetching results:', error);
                this.error = `Failed to load results: ${error.message}`;
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchQuizSets() {
            try {
                const response = await fetch('/api/quiz-sets');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.quizSets = Array.isArray(data) ? data : [];
                console.log('Fetched quiz sets:', this.quizSets);
            } catch (error) {
                console.error('Error fetching quiz sets:', error);
                this.error = `Failed to load quiz sets: ${error.message}`;
                this.quizSets = [];
            }
        },

        retryLoading() {
            this.error = null;
            this.fetchQuizSets();
            this.fetchResults();
        },

        applyFilters() {
            this.fetchResults();
        },

        clearFilters() {
            this.filters = {
                participant_name: '',
                quiz_set_id: '',
                date: ''
            };
            this.fetchResults();
        },

        getQuizSetName(quizSet) {
            return quizSet?.name || 'Unknown Set';
        },

        getResultQuizSetName(result) {
            return result?.quiz_set?.name || 'Unknown Set';
        },

        getPercentageColor(percentage) {
            const perc = Number(percentage) || 0;
            if (perc >= 80) return 'text-green-600';
            if (perc >= 60) return 'text-yellow-600';
            return 'text-red-600';
        },

        getPerformanceBarColor(percentage) {
            const perc = Number(percentage) || 0;
            if (perc >= 80) return 'bg-green-500';
            if (perc >= 60) return 'bg-yellow-500';
            return 'bg-red-500';
        },

        getPerformanceTextColor(percentage) {
            const perc = Number(percentage) || 0;
            if (perc >= 80) return 'text-green-600';
            if (perc >= 60) return 'text-yellow-600';
            return 'text-red-600';
        },

        getPerformanceRating(percentage) {
            const perc = Number(percentage) || 0;
            if (perc >= 90) return 'Excellent';
            if (perc >= 80) return 'Very Good';
            if (perc >= 70) return 'Good';
            if (perc >= 60) return 'Average';
            return 'Needs Improvement';
        },

        calculateAverageScore() {
            if (this.results.length === 0) return '0.0';
            const total = this.results.reduce((sum, result) => sum + this.getSafePercentage(result), 0);
            return (total / this.results.length).toFixed(1);
        },

        getTopScorer() {
            if (this.results.length === 0) return 'N/A';
            const topScore = Math.max(...this.results.map(result => this.getSafePercentage(result)));
            const topScorer = this.results.find(result => this.getSafePercentage(result) === topScore);
            return topScorer?.participant_name || 'Unknown';
        },

        getMostPopularQuizSet() {
            if (this.results.length === 0) return 'N/A';
            const quizSetCounts = {};
            this.results.forEach(result => {
                const setName = this.getResultQuizSetName(result);
                quizSetCounts[setName] = (quizSetCounts[setName] || 0) + 1;
            });
            
            const mostPopular = Object.keys(quizSetCounts).reduce((a, b) => 
                quizSetCounts[a] > quizSetCounts[b] ? a : b
            );
            return mostPopular;
        },

        formatTime(seconds) {
            const secs = Number(seconds) || 0;
            const mins = Math.floor(secs / 60);
            const remainingSecs = secs % 60;
            return `${mins}:${remainingSecs.toString().padStart(2, '0')}`;
        },

        formatDate(dateString) {
            if (!dateString) return 'Unknown date';
            try {
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch (error) {
                return 'Invalid date';
            }
        },

        async viewDetails(result) {
            this.selectedResult = result;
            
            if (!result.quiz_set_id) {
                this.error = 'Cannot load details: Quiz set ID is missing';
                return;
            }

            if (!this.quizDetails[result.quiz_set_id]) {
                try {
                    const response = await fetch(`/api/quiz-sets/${result.quiz_set_id}/quizzes`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    this.quizDetails[result.quiz_set_id] = await response.json();
                } catch (error) {
                    console.error('Error fetching quiz details:', error);
                    this.error = `Failed to load quiz details: ${error.message}`;
                }
            }
        },

        isAnswerCorrect(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            if (!quizSetQuizzes || !Array.isArray(quizSetQuizzes)) return false;
            
            const quiz = quizSetQuizzes[questionIndex];
            const userAnswer = this.getAllQuestions()[questionIndex];
            
            return quiz && userAnswer === quiz.correct_answer;
        },

        getQuestionText(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            if (!quizSetQuizzes || !Array.isArray(quizSetQuizzes)) return 'Question not available';
            
            const quiz = quizSetQuizzes[questionIndex];
            return quiz?.question || 'Question not available';
        },

        getQuestionOptions(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            if (!quizSetQuizzes || !Array.isArray(quizSetQuizzes)) return [];
            
            const quiz = quizSetQuizzes[questionIndex];
            return quiz?.options || [];
        },

        getCorrectAnswer(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            if (!quizSetQuizzes || !Array.isArray(quizSetQuizzes)) return '';
            
            const quiz = quizSetQuizzes[questionIndex];
            return quiz?.correct_answer || '';
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

/* Loading animation */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>