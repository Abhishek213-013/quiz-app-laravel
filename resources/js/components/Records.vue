<template>
    <div class="flex-1">
        <div class="min-h-screen" :class="themeClass">
            <div class="container mx-auto px-4 py-8 max-w-7xl">

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold header-title">📊 My Previous Records</h1>
                </div>

                <!-- Error State -->
                <div v-if="error" class="border rounded-lg p-4 mb-6" :class="themeClass">
                    <div class="flex items-center">
                        <span class="mr-2">⚠️</span>
                        <span class="error-text">{{ error }}</span>
                        <button @click="retryLoading" 
                                class="ml-4 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                            Retry
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div v-if="!error" class="filters-card rounded-lg shadow-md p-6 mb-6" :class="themeClass">
                    <h2 class="text-xl font-semibold header-title mb-4">🔍 Filter My Results</h2>
                    
                    <div class="filters-grid">
                        <!-- Search by Name -->
                        <div class="filter-item">
                            <label class="block text-sm font-medium filter-label mb-1">👤 Search by name</label>
                            <input 
                                v-model="filters.participant_name"
                                type="text" 
                                placeholder="Enter your name"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="themeClass"
                                @input="applyFilters"
                            >
                        </div>
                        
                        <!-- Quiz Set -->
                        <div class="filter-item">
                            <label class="block text-sm font-medium filter-label mb-1">📚 Quiz Set</label>
                            <select 
                                v-model="filters.quiz_set_id"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="themeClass"
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
                            <label class="block text-sm font-medium filter-label mb-1">📅 Date</label>
                            <input 
                                v-model="filters.date"
                                type="date" 
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="themeClass"
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
                <div v-if="!error && filteredResults.length > 0" class="summary-card rounded-lg shadow-md p-6 mb-6" :class="themeClass">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ filteredResults.length }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">📈 My Attempts</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ calculateAverageScore() }}%
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">📊 My Average Score</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ getBestScore() }}%
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">🏆 My Best Score</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ getMostAttemptedQuizSet() }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">🔥 Most Attempted</div>
                        </div>
                    </div>
                </div>

                <!-- Results Table -->
                <div v-if="!error" class="table-card rounded-lg shadow-md overflow-hidden" :class="themeClass">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y" :class="themeClass">
                            <thead class="table-header" :class="themeClass">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        👤 Name Used
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        📚 Quiz Set
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        🎯 Score
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        📊 Percentage
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        ⏱️ Time Taken
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        📅 Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        🔧 Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="table-body divide-y" :class="themeClass">
                                <tr v-for="result in filteredResults" :key="result.id" class="table-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium">{{ result.participant_name || 'Unknown' }}</div>
                                        <div class="text-xs" :class="themeClass">
                                            🌐 {{ useBrowserFilter ? 'This browser' : 'All browsers' }}
                                            <span v-if="result.browser_id" class="ml-1 text-blue-500">(ID: {{ result.browser_id.slice(0, 8) }}...)</span>
                                            <span v-else class="ml-1 text-gray-400">(No browser ID)</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">{{ getResultQuizSetName(result) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
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
                                        <div class="text-sm">
                                            {{ formatTime(result.time_taken || 0) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            {{ formatDate(result.created_at) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button 
                                            @click="viewDetails(result)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3 flex items-center"
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
                        <h3 class="mt-2 text-sm font-medium">
                            {{ useBrowserFilter ? 'No attempts found from this browser' : 'No quiz attempts found' }}
                        </h3>
                        <p class="mt-1 text-sm" :class="themeClass">
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
                        <p class="mt-2 text-sm flex items-center justify-center" :class="themeClass">
                            <span class="mr-2">⏳</span>
                            Loading your attempts...
                        </p>
                    </div>
                </div>

                <!-- Result Details Modal -->
                <div v-if="selectedResult" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="closeModal">
                    <div class="modal-card rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto" :class="themeClass">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold header-title">📋 My Quiz Result Details</h3>
                            <button 
                                @click="closeModal"
                                class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-2xl"
                            >
                                ❌
                            </button>
                        </div>

                        <!-- Summary -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="summary-item p-4 rounded-lg text-center" :class="themeClass">
                                <div class="text-2xl font-bold text-blue-600">{{ selectedResult.score || 0 }}/{{ selectedResult.total_questions || 0 }}</div>
                                <div class="text-sm text-blue-600">🎯 My Score</div>
                            </div>
                            <div class="summary-item p-4 rounded-lg text-center" :class="themeClass">
                                <div class="text-2xl font-bold text-green-600">{{ formatPercentage(getSafePercentage(selectedResult)) }}</div>
                                <div class="text-sm text-green-600">📊 My Percentage</div>
                            </div>
                            <div class="summary-item p-4 rounded-lg text-center" :class="themeClass">
                                <div class="text-2xl font-bold text-purple-600">{{ formatTime(selectedResult.time_taken || 0) }}</div>
                                <div class="text-sm text-purple-600">⏱️ My Time</div>
                            </div>
                            <div class="summary-item p-4 rounded-lg text-center" :class="themeClass">
                                <div class="text-2xl font-bold text-yellow-600">{{ formatDate(selectedResult.created_at) }}</div>
                                <div class="text-sm text-yellow-600">📅 Date</div>
                            </div>
                        </div>

                        <!-- Performance Rating -->
                        <div class="performance-rating p-4 rounded-lg mb-6" :class="themeClass">
                            <h4 class="text-lg font-semibold header-title mb-2">📈 My Performance</h4>
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
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
                        <div class="question-stats border rounded-lg p-4 mb-6" :class="themeClass">
                            <h4 class="text-lg font-semibold header-title mb-3">📝 My Question Breakdown</h4>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="stat-item p-3 rounded-lg" :class="themeClass">
                                    <div class="text-2xl font-bold text-green-600">{{ getCorrectCount() }}</div>
                                    <div class="text-sm text-green-600">✅ Correct</div>
                                </div>
                                <div class="stat-item p-3 rounded-lg" :class="themeClass">
                                    <div class="text-2xl font-bold text-red-600">{{ getWrongCount() }}</div>
                                    <div class="text-sm text-red-600">❌ Wrong</div>
                                </div>
                                <div class="stat-item p-3 rounded-lg" :class="themeClass">
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
                                class="question-detail border rounded-lg p-4"
                                :class="getQuestionStatusClass(questionIndex) + ' ' + themeClass"
                            >
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="font-semibold header-title">❓ Question {{ parseInt(questionIndex) + 1 }}</h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getQuestionStatusBadgeClass(questionIndex)">
                                        {{ getQuestionStatus(questionIndex) }}
                                    </span>
                                </div>
                                <p class="mb-2" :class="themeClass">{{ getQuestionText(questionIndex) }}</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div 
                                        v-for="(option, optIndex) in getQuestionOptions(questionIndex)" 
                                        :key="optIndex"
                                        class="option-item p-2 border rounded text-sm"
                                        :class="getOptionClass(questionIndex, option, answer) + ' ' + themeClass"
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
                        <div v-else class="text-center py-8" :class="themeClass">
                            <div class="text-2xl mb-2">📭</div>
                            No answer details available for this attempt.
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
    props: {
        theme: {
            type: String,
            default: 'light'
        }
    },
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
            useBrowserFilter: false,
            showDebugInfo: true
        }
    },
    computed: {
        themeClass() {
            return `theme-${this.theme}`;
        },
        resultsWithBrowserId() {
            return this.results.filter(result => result.browser_id);
        },
        filteredResults() {
            console.log('🔍 Filtering results:', {
                useBrowserFilter: this.useBrowserFilter,
                browserId: this.browserId,
                totalResults: this.results.length,
                resultsWithBrowserId: this.resultsWithBrowserId.length
            });

            if (this.useBrowserFilter && this.resultsWithBrowserId.length === 0) {
                console.log('⚠️ Browser filter requested but no results have browser_id. Showing all results.');
                return this.results;
            }

            if (this.useBrowserFilter && this.browserId) {
                const filtered = this.results.filter(result => result.browser_id === this.browserId);
                console.log('🔒 Browser-filtered results:', filtered.length);
                return filtered;
            }

            console.log('🔓 Showing ALL results');
            return this.results;
        }
    },
    async mounted() {
        try {
            await this.initializeBrowserId();
            await this.fetchQuizSets();
            await this.fetchResults();
        } catch (error) {
            console.error('Error in mounted:', error);
            this.error = 'Failed to initialize component: ' + error.message;
        }
    },
    methods: {
        async refreshWithDebug() {
            console.log('🔄 Refreshing with debug info...');
            await this.fetchResults();
            
            if (this.resultsWithBrowserId.length === 0 && this.useBrowserFilter) {
                console.log('🔄 Auto-disabling browser filter (no browser_id data)');
                this.useBrowserFilter = false;
            }
        },

        toggleBrowserFilter() {
            if (this.resultsWithBrowserId.length === 0) {
                console.log('❌ Cannot enable browser filter: no results have browser_id');
                alert('No quiz results have browser tracking enabled. Please make sure your quiz submissions include browser_id.');
                return;
            }
            
            this.useBrowserFilter = !this.useBrowserFilter;
            console.log('🔄 Browser filter:', this.useBrowserFilter ? 'ON' : 'OFF');
        },

        async initializeBrowserId() {
            try {
                let browserId = localStorage.getItem('quiz_browser_id');
                
                if (!browserId) {
                    browserId = this.generateBrowserId();
                    localStorage.setItem('quiz_browser_id', browserId);
                    console.log('🆕 Generated new browser ID:', browserId);
                } else {
                    console.log('🔑 Using existing browser ID:', browserId);
                }
                
                this.browserId = browserId;
            } catch (error) {
                console.error('Error initializing browser ID:', error);
                this.browserId = 'temp_' + Date.now();
                console.log('🔄 Using fallback browser ID:', this.browserId);
            }
        },

        generateBrowserId() {
            const timestamp = Date.now().toString(36);
            const random = Math.random().toString(36).substr(2, 9);
            return `browser_${timestamp}_${random}`;
        },

        closeModal() {
            this.selectedResult = null;
        },

        getAllQuestions() {
            if (!this.selectedResult || !this.quizDetails[this.selectedResult.quiz_set_id]) {
                return {};
            }
            
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            const allQuestions = {};
            
            for (let i = 0; i < quizSetQuizzes.length; i++) {
                allQuestions[i] = this.selectedResult.answers[i] || null;
            }
            
            return allQuestions;
        },

        getTotalQuestions() {
            if (!this.selectedResult || !this.quizDetails[this.selectedResult.quiz_set_id]) {
                return 0;
            }
            return this.quizDetails[this.selectedResult.quiz_set_id].length;
        },

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

        getQuestionStatusClass(questionIndex) {
            const status = this.getQuestionStatus(questionIndex);
            
            switch (status) {
                case 'Correct':
                    return 'bg-green-50 border-green-200 dark:bg-green-900/30 dark:border-green-800';
                case 'Wrong':
                    return 'bg-red-50 border-red-200 dark:bg-red-900/30 dark:border-red-800';
                case 'Skipped':
                    return 'bg-blue-50 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800';
                default:
                    return 'bg-gray-50 border-gray-200 dark:bg-gray-800/30 dark:border-gray-700';
            }
        },

        getQuestionStatusBadgeClass(questionIndex) {
            const status = this.getQuestionStatus(questionIndex);
            
            switch (status) {
                case 'Correct':
                    return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
                case 'Wrong':
                    return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
                case 'Skipped':
                    return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100';
                default:
                    return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100';
            }
        },

        getOptionClass(questionIndex, option, userAnswer) {
            const correctAnswer = this.getCorrectAnswer(questionIndex);
            const isCorrect = option === correctAnswer;
            const isUserAnswer = option === userAnswer;
            const isSkipped = userAnswer === null;
            
            if (isCorrect && isUserAnswer) {
                return 'bg-green-100 border-green-500 dark:bg-green-900/50 dark:border-green-600';
            } else if (isCorrect && !isUserAnswer) {
                return 'bg-green-100 border-green-300 dark:bg-green-900/30 dark:border-green-500';
            } else if (!isCorrect && isUserAnswer) {
                return 'bg-red-100 border-red-500 dark:bg-red-900/50 dark:border-red-600';
            } else if (isSkipped && isCorrect) {
                return 'bg-blue-100 border-blue-300 dark:bg-blue-900/30 dark:border-blue-500';
            } else {
                return 'bg-gray-50 border-gray-200 dark:bg-gray-800/30 dark:border-gray-600';
            }
        },

        getSafePercentage(result) {
            if (!result) return 0;
            
            let percentage = result.percentage;
            
            if (percentage == null) {
                const score = Number(result.score) || 0;
                const total = Number(result.total_questions) || 1;
                percentage = (score / total) * 100;
            }
            
            percentage = Number(percentage);
            
            if (isNaN(percentage)) {
                return 0;
            }
            
            return Math.min(Math.max(percentage, 0), 100);
        },

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
                
                this.results = Array.isArray(data) ? data.map(result => ({
                    ...result,
                    percentage: this.getSafePercentage(result)
                })) : [];

                console.log('📊 Final results data:', {
                    total: this.results.length,
                    withBrowserId: this.results.filter(r => r.browser_id).length,
                    withoutBrowserId: this.results.filter(r => !r.browser_id).length,
                    browserId: this.browserId,
                    filteredCount: this.filteredResults.length
                });

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
/* Theme Variables */
:root {
    --records-bg: #f3f4f6;
    --header-title: #1e40af;
    --error-bg: #fef2f2;
    --error-border: #fecaca;
    --error-text: #991b1b;
    --filters-card-bg: #ffffff;
    --filters-card-border: #e5e7eb;
    --filter-label: #4b5563;
    --input-bg: #ffffff;
    --input-border: #d1d5db;
    --input-text: #1f2937;
    --summary-card-bg: #ffffff;
    --summary-card-border: #e5e7eb;
    --table-card-bg: #ffffff;
    --table-card-border: #e5e7eb;
    --table-header-bg: #f9fafb;
    --table-header-text: #6b7280;
    --table-body-bg: #ffffff;
    --table-border: #e5e7eb;
    --table-row-hover: #f9fafb;
    --table-text: #1f2937;
    --modal-card-bg: #ffffff;
    --modal-card-border: #e5e7eb;
    --summary-item-bg: #f8fafc;
    --summary-item-border: #e2e8f0;
    --performance-rating-bg: #f8fafc;
    --question-stats-bg: #ffffff;
    --question-stats-border: #e5e7eb;
    --stat-item-bg: #f8fafc;
    --stat-item-border: #e2e8f0;
}

.dark-theme {
    --records-bg: #111827;
    --header-title: #60a5fa;
    --error-bg: #7f1d1d;
    --error-border: #991b1b;
    --error-text: #fecaca;
    --filters-card-bg: #1f2937;
    --filters-card-border: #374151;
    --filter-label: #d1d5db;
    --input-bg: #374151;
    --input-border: #4b5563;
    --input-text: #f3f4f6;
    --summary-card-bg: #1f2937;
    --summary-card-border: #374151;
    --table-card-bg: #1f2937;
    --table-card-border: #374151;
    --table-header-bg: #374151;
    --table-header-text: #9ca3af;
    --table-body-bg: #1f2937;
    --table-border: #374151;
    --table-row-hover: #374151;
    --table-text: #f3f4f6;
    --modal-card-bg: #1f2937;
    --modal-card-border: #374151;
    --summary-item-bg: #374151;
    --summary-item-border: #4b5563;
    --performance-rating-bg: #374151;
    --question-stats-bg: #1f2937;
    --question-stats-border: #374151;
    --stat-item-bg: #374151;
    --stat-item-border: #4b5563;
}

/* Background */
.min-h-screen.theme-light {
    background-color: var(--records-bg);
}

.min-h-screen.theme-dark {
    background-color: var(--records-bg);
}

/* Header */
.header-title {
    color: var(--header-title);
}

/* Error State */
.theme-light {
    background-color: var(--error-bg);
    border-color: var(--error-border);
}

.theme-dark {
    background-color: var(--error-bg);
    border-color: var(--error-border);
}

.error-text {
    color: var(--error-text);
}

/* Filters Card */
.filters-card.theme-light {
    background-color: var(--filters-card-bg);
    border: 1px solid var(--filters-card-border);
}

.filters-card.theme-dark {
    background-color: var(--filters-card-bg);
    border: 1px solid var(--filters-card-border);
}

.filter-label {
    color: var(--filter-label);
}

/* Inputs and Selects */
input.theme-light,
select.theme-light {
    background-color: var(--input-bg);
    border-color: var(--input-border);
    color: var(--input-text);
}

input.theme-dark,
select.theme-dark {
    background-color: var(--input-bg);
    border-color: var(--input-border);
    color: var(--input-text);
}

/* Summary Card */
.summary-card.theme-light {
    background-color: var(--summary-card-bg);
    border: 1px solid var(--summary-card-border);
}

.summary-card.theme-dark {
    background-color: var(--summary-card-bg);
    border: 1px solid var(--summary-card-border);
}

/* Table Card */
.table-card.theme-light {
    background-color: var(--table-card-bg);
    border: 1px solid var(--table-card-border);
}

.table-card.theme-dark {
    background-color: var(--table-card-bg);
    border: 1px solid var(--table-card-border);
}

/* Table Header */
.table-header.theme-light {
    background-color: var(--table-header-bg);
}

.table-header.theme-dark {
    background-color: var(--table-header-bg);
}

.table-header.theme-light th {
    color: var(--table-header-text);
}

.table-header.theme-dark th {
    color: var(--table-header-text);
}

/* Table Body */
.table-body.theme-light {
    background-color: var(--table-body-bg);
}

.table-body.theme-dark {
    background-color: var(--table-body-bg);
}

.table-body.theme-light tr {
    border-color: var(--table-border);
}

.table-body.theme-dark tr {
    border-color: var(--table-border);
}

.table-body.theme-light tr:hover {
    background-color: var(--table-row-hover);
}

.table-body.theme-dark tr:hover {
    background-color: var(--table-row-hover);
}

.table-row div {
    color: var(--table-text);
}

/* Modal Card */
.modal-card.theme-light {
    background-color: var(--modal-card-bg);
    border: 1px solid var(--modal-card-border);
}

.modal-card.theme-dark {
    background-color: var(--modal-card-bg);
    border: 1px solid var(--modal-card-border);
}

/* Summary Items */
.summary-item.theme-light {
    background-color: var(--summary-item-bg);
    border: 1px solid var(--summary-item-border);
}

.summary-item.theme-dark {
    background-color: var(--summary-item-bg);
    border: 1px solid var(--summary-item-border);
}

/* Performance Rating */
.performance-rating.theme-light {
    background-color: var(--performance-rating-bg);
}

.performance-rating.theme-dark {
    background-color: var(--performance-rating-bg);
}

/* Question Stats */
.question-stats.theme-light {
    background-color: var(--question-stats-bg);
    border-color: var(--question-stats-border);
}

.question-stats.theme-dark {
    background-color: var(--question-stats-bg);
    border-color: var(--question-stats-border);
}

.stat-item.theme-light {
    background-color: var(--stat-item-bg);
    border: 1px solid var(--stat-item-border);
}

.stat-item.theme-dark {
    background-color: var(--stat-item-bg);
    border: 1px solid var(--stat-item-border);
}

/* Question Detail */
.question-detail.theme-light p,
.question-detail.theme-light span {
    color: #374151;
}

.question-detail.theme-dark p,
.question-detail.theme-dark span {
    color: #d1d5db;
}

/* Option Items */
.option-item.theme-light {
    background-color: #f8fafc;
    border-color: #e2e8f0;
    color: #1e293b;
}

.option-item.theme-dark {
    background-color: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
}

/* Loading text */
.theme-light {
    color: #4b5563;
}

.theme-dark {
    color: #9ca3af;
}

/* Filters Grid */
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

/* Animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>