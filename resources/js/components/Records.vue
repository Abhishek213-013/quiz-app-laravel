<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">📊 My Previous Records</h1>
            </div>

            <!-- Error State -->
            <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <span class="mr-2">⚠️</span>
                    <span class="text-red-800">{{ error }}</span>
                    <button @click="retryLoading" class="ml-4 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                        Retry
                    </button>
                </div>
            </div>

            <!-- Filters - Using CSS Grid for better responsiveness -->
            <div v-if="!error" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">🔍 Filter My Results</h2>
                
                <div class="filters-grid">
                    <!-- Search by Name -->
                    <div class="filter-item">
                        <label class="block text-sm font-medium text-gray-700 mb-1">👤 Search by name</label>
                        <input 
                            v-model="filters.participant_name"
                            type="text" 
                            placeholder="Enter your name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @input="applyFilters"
                        >
                    </div>
                    
                    <!-- Quiz Set -->
                    <div class="filter-item">
                        <label class="block text-sm font-medium text-gray-700 mb-1">📚 Quiz Set</label>
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
                    <div class="filter-item">
                        <label class="block text-sm font-medium text-gray-700 mb-1">📅 Date</label>
                        <input 
                            v-model="filters.date"
                            type="date" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="applyFilters"
                        >
                    </div>
                    
                    <!-- Buttons -->
                    <div class="filter-buttons">
                        <button 
                            @click="clearFilters"
                            class="w-full md:w-auto px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors font-medium flex items-center justify-center"
                        >
                            <span class="mr-2">🗑️</span>
                            Clear
                        </button>
                        <button 
                            @click="applyFilters"
                            class="w-full md:w-auto px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors font-medium flex items-center justify-center"
                        >
                            <span class="mr-2">✅</span>
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Summary -->
            <div v-if="!error && filteredResults.length > 0" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ filteredResults.length }}</div>
                        <div class="text-sm text-gray-600">📈 My Attempts</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ calculateAverageScore() }}%
                        </div>
                        <div class="text-sm text-gray-600">📊 My Average Score</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ getBestScore() }}%
                        </div>
                        <div class="text-sm text-gray-600">🏆 My Best Score</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ getMostAttemptedQuizSet() }}
                        </div>
                        <div class="text-sm text-gray-600">🔥 Most Attempted</div>
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
                                    👤 Name Used
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    📚 Quiz Set
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    🎯 Score
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    📊 Percentage
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ⏱️ Time Taken
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    📅 Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    🔧 Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="result in filteredResults" :key="result.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ result.participant_name || 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500">
                                        🌐 {{ useBrowserFilter ? 'This browser' : 'All browsers' }}
                                        <span v-if="result.browser_id" class="ml-1 text-blue-500">(ID: {{ result.browser_id.slice(0, 8) }}...)</span>
                                        <span v-else class="ml-1 text-gray-400">(No browser ID)</span>
                                    </div>
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
                                        class="text-blue-600 hover:text-blue-900 mr-3 flex items-center"
                                        :disabled="!result.quiz_set_id"
                                    >
                                        <span class="mr-1">👁️</span>
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredResults.length === 0 && !loading && !error" class="text-center py-12">
                    <div class="text-4xl mb-4">📭</div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        {{ useBrowserFilter ? 'No attempts found from this browser' : 'No quiz attempts found' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ useBrowserFilter ? 'Take a quiz first to see your attempts here.' : 'Take a quiz first to see attempts here.' }}
                    </p>
                    <div class="mt-4 flex justify-center gap-2">
                        <button 
                            @click="retryLoading"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors flex items-center"
                        >
                            <span class="mr-2">🔄</span>
                            Refresh
                        </button>
                        <button 
                            v-if="useBrowserFilter && results.length > 0"
                            @click="toggleBrowserFilter"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors flex items-center"
                        >
                            <span class="mr-2">👥</span>
                            Show All Results
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading && !error" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-sm text-gray-600 flex items-center justify-center">
                        <span class="mr-2">⏳</span>
                        Loading your attempts...
                    </p>
                </div>
            </div>

            <!-- Result Details Modal -->
            <div v-if="selectedResult" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="closeModal">
                <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">📋 My Quiz Result Details</h3>
                        <button 
                            @click="closeModal"
                            class="text-gray-400 hover:text-gray-600 text-2xl"
                        >
                            ❌
                        </button>
                    </div>

                    <!-- Summary -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ selectedResult.score || 0 }}/{{ selectedResult.total_questions || 0 }}</div>
                            <div class="text-sm text-blue-600">🎯 My Score</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600">{{ formatPercentage(getSafePercentage(selectedResult)) }}</div>
                            <div class="text-sm text-green-600">📊 My Percentage</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ formatTime(selectedResult.time_taken || 0) }}</div>
                            <div class="text-sm text-purple-600">⏱️ My Time</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ formatDate(selectedResult.created_at) }}</div>
                            <div class="text-sm text-yellow-600">📅 Date</div>
                        </div>
                    </div>

                    <!-- Performance Rating -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">📈 My Performance</h4>
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
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">📝 My Question Breakdown</h4>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ getCorrectCount() }}</div>
                                <div class="text-sm text-green-600">✅ Correct</div>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ getWrongCount() }}</div>
                                <div class="text-sm text-red-600">❌ Wrong</div>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ getSkippedCount() }}</div>
                                <div class="text-sm text-blue-600">⏭️ Skipped</div>
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
                                <h4 class="font-semibold text-gray-800">❓ Question {{ parseInt(questionIndex) + 1 }}</h4>
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
                                    <span v-if="option === getCorrectAnswer(questionIndex)" class="ml-2 text-green-600 text-xs">✅ Correct</span>
                                    <span v-if="option === answer && option !== getCorrectAnswer(questionIndex)" class="ml-2 text-red-600 text-xs">❌ My Answer</span>
                                    <span v-if="!answer && option === getCorrectAnswer(questionIndex)" class="ml-2 text-blue-600 text-xs">⏭️ Skipped</span>
                                </div>
                            </div>
                            <div v-if="!answer" class="mt-2 text-sm text-blue-600 flex items-center">
                                <span class="mr-1">⏭️</span>
                                I skipped this question
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <div class="text-2xl mb-2">📭</div>
                        No answer details available for this attempt.
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
            quizDetails: {},
            browserId: null,
            useBrowserFilter: false, // Start by showing ALL results
            showDebugInfo: true
        }
    },
    computed: {
        // Get results with browser_id for debugging
        resultsWithBrowserId() {
            return this.results.filter(result => result.browser_id);
        },
        
        // Filter results - automatically handles missing browser_id
        filteredResults() {
            console.log('🔍 Filtering results:', {
                useBrowserFilter: this.useBrowserFilter,
                browserId: this.browserId,
                totalResults: this.results.length,
                resultsWithBrowserId: this.resultsWithBrowserId.length
            });

            // If we try to use browser filter but no results have browser_id, show all with warning
            if (this.useBrowserFilter && this.resultsWithBrowserId.length === 0) {
                console.log('⚠️ Browser filter requested but no results have browser_id. Showing all results.');
                return this.results;
            }

            // Normal browser filtering
            if (this.useBrowserFilter && this.browserId) {
                const filtered = this.results.filter(result => result.browser_id === this.browserId);
                console.log('🔒 Browser-filtered results:', filtered.length);
                return filtered;
            }

            // Default: show all results
            console.log('🔓 Showing ALL results');
            return this.results;
        }
    },
    async mounted() {
        try {
            // Generate or retrieve browser ID
            await this.initializeBrowserId();
            await this.fetchQuizSets();
            await this.fetchResults();
        } catch (error) {
            console.error('Error in mounted:', error);
            this.error = 'Failed to initialize component: ' + error.message;
        }
    },
    methods: {
        // Refresh with detailed debugging
        async refreshWithDebug() {
            console.log('🔄 Refreshing with debug info...');
            await this.fetchResults();
            
            // Auto-disable browser filter if no browser_id results
            if (this.resultsWithBrowserId.length === 0 && this.useBrowserFilter) {
                console.log('🔄 Auto-disabling browser filter (no browser_id data)');
                this.useBrowserFilter = false;
            }
        },

        // Toggle browser filter with smart behavior
        toggleBrowserFilter() {
            if (this.resultsWithBrowserId.length === 0) {
                console.log('❌ Cannot enable browser filter: no results have browser_id');
                alert('No quiz results have browser tracking enabled. Please make sure your quiz submissions include browser_id.');
                return;
            }
            
            this.useBrowserFilter = !this.useBrowserFilter;
            console.log('🔄 Browser filter:', this.useBrowserFilter ? 'ON' : 'OFF');
        },

        // Initialize browser ID
        async initializeBrowserId() {
            try {
                // Try to get existing browser ID from localStorage
                let browserId = localStorage.getItem('quiz_browser_id');
                
                if (!browserId) {
                    // Generate a new browser ID
                    browserId = this.generateBrowserId();
                    localStorage.setItem('quiz_browser_id', browserId);
                    console.log('🆕 Generated new browser ID:', browserId);
                } else {
                    console.log('🔑 Using existing browser ID:', browserId);
                }
                
                this.browserId = browserId;
            } catch (error) {
                console.error('Error initializing browser ID:', error);
                // Fallback: generate a temporary ID for this session
                this.browserId = 'temp_' + Date.now();
                console.log('🔄 Using fallback browser ID:', this.browserId);
            }
        },

        // Generate a unique browser ID
        generateBrowserId() {
            const timestamp = Date.now().toString(36);
            const random = Math.random().toString(36).substr(2, 9);
            return `browser_${timestamp}_${random}`;
        },

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

                console.log('📡 Fetching results from:', url);

                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('📥 Fetched all results:', data);
                
                // Ensure results is always an array and has safe percentage values
                this.results = Array.isArray(data) ? data.map(result => ({
                    ...result,
                    // Ensure percentage is always a number
                    percentage: this.getSafePercentage(result)
                })) : [];

                console.log('📊 Final results data:', {
                    total: this.results.length,
                    withBrowserId: this.results.filter(r => r.browser_id).length,
                    withoutBrowserId: this.results.filter(r => !r.browser_id).length,
                    browserId: this.browserId,
                    filteredCount: this.filteredResults.length
                });

                // Auto-enable browser filter if we have results with browser_id
                if (this.resultsWithBrowserId.length > 0 && !this.useBrowserFilter) {
                    console.log('🔄 Auto-enabling browser filter since we have browser_id results');
                    this.useBrowserFilter = true;
                }
                
            } catch (error) {
                console.error('❌ Error fetching results:', error);
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
                console.log('📚 Fetched quiz sets:', this.quizSets);
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
            if (perc >= 90) return 'Excellent 🎉';
            if (perc >= 80) return 'Very Good 👍';
            if (perc >= 70) return 'Good 👌';
            if (perc >= 60) return 'Average 📊';
            return 'Needs Improvement 📈';
        },

        calculateAverageScore() {
            if (this.filteredResults.length === 0) return '0.0';
            const total = this.filteredResults.reduce((sum, result) => sum + this.getSafePercentage(result), 0);
            return (total / this.filteredResults.length).toFixed(1);
        },

        getBestScore() {
            if (this.filteredResults.length === 0) return '0.0';
            const best = Math.max(...this.filteredResults.map(result => this.getSafePercentage(result)));
            return best.toFixed(1);
        },

        getMostAttemptedQuizSet() {
            if (this.filteredResults.length === 0) return 'N/A';
            const quizSetCounts = {};
            this.filteredResults.forEach(result => {
                const setName = this.getResultQuizSetName(result);
                quizSetCounts[setName] = (quizSetCounts[setName] || 0) + 1;
            });
            
            const mostAttempted = Object.keys(quizSetCounts).reduce((a, b) => 
                quizSetCounts[a] > quizSetCounts[b] ? a : b
            );
            return mostAttempted;
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
.filters-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.filter-item {
    width: 100%;
}

.filter-buttons {
    display: flex;
    gap: 0.5rem;
    flex-direction: column;
}

@media (min-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr 1fr 1fr auto;
        align-items: end;
        gap: 1rem;
    }
    
    .filter-buttons {
        flex-direction: row;
        align-items: end;
    }
}

/* Your existing styles */
.transform {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}

.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

span[role="img"] {
    font-style: normal;
}
</style>