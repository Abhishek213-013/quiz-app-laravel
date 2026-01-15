<template>
    <div class="flex-1 overflow-hidden relative">
        <!-- Animated Background Elements -->
        <div class="animated-bg-elements">
            <div class="floating-particle" v-for="(particle, index) in particles" :key="index"
                 :style="particle.style"></div>
            <div class="circuit-board-bg"></div>
        </div>

        <!-- Records Easter Egg Trigger -->
        <div class="records-easter-egg-trigger" @click="toggleAnalyticsMode"
             @mouseenter="showAnalyticsTooltip = true"
             @mouseleave="showAnalyticsTooltip = false"
             :class="{ 'analytics-mode-active': analyticsMode }">
            <span class="text-2xl">📈</span>
            <div v-if="showAnalyticsTooltip && !analyticsMode" class="analytics-tooltip">
                Click for ADVANCED ANALYTICS
            </div>
        </div>

        <div class="container mx-auto px-4 py-8 max-w-7xl relative z-10">
            <!-- Analytics Mode Progress -->
            <div v-if="analyticsProgress > 0" class="analytics-hint">
                Analytics Mode: {{ analyticsSequence.slice(0, analyticsProgress).join(' ') }}
            </div>

            <!-- Animated Header -->
            <div class="text-center mb-12 animate-slide-up">
                <h1 class="records-title inline-block">
                    <span class="text-gradient animate-gradient-shift text-4xl font-bold">📊 My Quiz Records</span>
                    <span class="sparkle">✨</span>
                </h1>
                <p class="records-subtitle mt-2 text-lg opacity-75">
                    Track, analyze, and improve your quiz performance
                </p>
            </div>

            <!-- Error State with Animation -->
            <transition name="slide-down">
                <div v-if="error" class="error-state-container mb-6 animate-shake" :class="themeClass">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="error-icon mr-3 animate-pulse-slow">⚠️</span>
                            <span class="error-text">{{ error }}</span>
                        </div>
                        <button @click="retryLoading" 
                                class="retry-btn animate-bounce-slow">
                            <span class="mr-2">🔄</span>
                            Retry
                        </button>
                    </div>
                    <div class="error-data-stream"></div>
                </div>
            </transition>

            <!-- Filters Card with Enhanced Effects -->
            <div v-if="!error" class="filters-card rounded-lg shadow-md p-6 mb-6 animate-slide-up interactive-card" 
                 :class="themeClass" :style="{ animationDelay: '0.1s' }">
                <h2 class="filters-title mb-4 animate-slide-right">
                    <span class="filter-icon">🔍</span>
                    Filter My Results
                    <span class="filter-sparkle"></span>
                </h2>
                
                <div class="filters-grid">
                    <!-- Search by Name -->
                    <div class="filter-item">
                        <label class="filter-label mb-1">
                            <span class="label-icon">👤</span>
                            Search by name
                        </label>
                        <div class="input-wrapper" :class="{ 'data-stream': analyticsMode }">
                            <input 
                                v-model="filters.participant_name"
                                type="text" 
                                placeholder="Enter your name"
                                class="filter-input animate-fade-in"
                                :class="themeClass"
                                @input="applyFilters"
                                @focus="playFocusSound"
                            >
                            <div class="input-underline"></div>
                        </div>
                    </div>
                    
                    <!-- Quiz Set -->
                    <div class="filter-item">
                        <label class="filter-label mb-1">
                            <span class="label-icon">📚</span>
                            Quiz Set
                        </label>
                        <div class="select-wrapper" :class="{ 'holographic': analyticsMode }">
                            <select 
                                v-model="filters.quiz_set_id"
                                class="filter-select animate-fade-in"
                                :class="themeClass"
                                @change="applyFilters"
                                @focus="playFocusSound"
                            >
                                <option value="">All Sets</option>
                                <option 
                                    v-for="quizSet in quizSets" 
                                    :key="quizSet.id" 
                                    :value="quizSet.id"
                                    :class="{ 'animate-pulse-slow': quizSet.id === 'featured' }"
                                >
                                    {{ getQuizSetName(quizSet) }}
                                </option>
                            </select>
                            <div class="select-glow"></div>
                        </div>
                    </div>
                    
                    <!-- Date -->
                    <div class="filter-item">
                        <label class="filter-label mb-1">
                            <span class="label-icon">📅</span>
                            Date
                        </label>
                        <div class="date-wrapper" :class="{ 'cyber-glow-blue': analyticsMode }">
                            <input 
                                v-model="filters.date"
                                type="date" 
                                class="date-input animate-fade-in"
                                :class="themeClass"
                                @change="applyFilters"
                                @focus="playFocusSound"
                            >
                            <div class="date-particles"></div>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="filter-buttons animate-slide-up" :style="{ animationDelay: '0.3s' }">
                        <button 
                            @click="clearFilters"
                            class="filter-btn clear-btn interactive-card"
                            @mouseenter="playHoverSound('clear')"
                        >
                            <span class="btn-icon animate-tada">🗑️</span>
                            <span class="btn-text">Clear</span>
                            <div class="btn-particles"></div>
                        </button>
                        <button 
                            @click="applyFilters"
                            class="filter-btn apply-btn interactive-card"
                            @mouseenter="playHoverSound('apply')"
                        >
                            <span class="btn-icon animate-bounce-slow">✅</span>
                            <span class="btn-text">Apply</span>
                            <div class="btn-sparkle"></div>
                        </button>
                    </div>
                </div>

                <!-- Browser Filter Toggle -->
                <div class="browser-filter-toggle mt-4 animate-fade-in" :style="{ animationDelay: '0.4s' }">
                    <label class="toggle-label interactive-card">
                        <input 
                            type="checkbox" 
                            v-model="useBrowserFilter"
                            @change="toggleBrowserFilter"
                            class="toggle-checkbox"
                        >
                        <span class="toggle-slider"></span>
                        <span class="toggle-text">
                            <span class="toggle-icon">{{ useBrowserFilter ? '🔒' : '🌐' }}</span>
                            {{ useBrowserFilter ? 'Only my browser' : 'All browsers' }}
                            <span class="toggle-count">
                                ({{ useBrowserFilter ? resultsWithBrowserId.length : results.length }})
                            </span>
                        </span>
                    </label>
                    <div class="toggle-neural-node"></div>
                </div>

                <!-- Warning when showing all browsers -->
                <transition name="fade">
                    <div v-if="!useBrowserFilter && resultsWithBrowserId.length > 0" 
                         class="all-browsers-warning mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 rounded-r">
                        <div class="flex items-start">
                            <span class="warning-icon mr-3 text-xl">⚠️</span>
                            <div>
                                <h4 class="warning-title font-medium text-yellow-800 dark:text-yellow-300">
                                    Viewing ALL attempts from ALL browsers
                                </h4>
                                <p class="warning-text text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                                    You are currently seeing everyone's quiz results. To view only your attempts, 
                                    enable the browser filter above.
                                </p>
                                <button @click="useBrowserFilter = true" 
                                        class="mt-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-colors flex items-center">
                                    <span class="mr-2">🔒</span>
                                    Show only my attempts
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Results Summary with Counting Animation -->
            <transition name="slide-up">
                <div v-if="!error && filteredResults.length > 0" 
                     class="summary-card rounded-lg shadow-md p-6 mb-6 animate-slide-up interactive-card"
                     :class="themeClass" :style="{ animationDelay: '0.2s' }">
                    
                    <!-- Summary Status Indicator -->
                    <div class="summary-status mb-4 flex items-center justify-center">
                        <div class="status-badge inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                             :class="useBrowserFilter 
                                    ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-700'
                                    : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-700'">
                            <span class="mr-2">
                                {{ useBrowserFilter ? '🔒' : '👥' }}
                            </span>
                            {{ useBrowserFilter ? 'Your attempts only' : 'All attempts' }}
                            <span class="ml-2 text-xs opacity-75">
                                ({{ useBrowserFilter ? resultsWithBrowserId.length : results.length }} total)
                            </span>
                        </div>
                    </div>

                    <h3 class="summary-title mb-6 animate-slide-right">
                        <span class="summary-icon">📈</span>
                        Performance Summary
                        <span class="summary-sparkle"></span>
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="summary-item animate-count-up" :style="{ animationDelay: '0.1s' }">
                            <div class="summary-number text-blue-600 cyber-glow-blue">
                                {{ animatedAttempts }}
                            </div>
                            <div class="summary-label">📈 Total Attempts</div>
                            <div class="summary-glow"></div>
                        </div>
                        <div class="summary-item animate-count-up" :style="{ animationDelay: '0.2s' }">
                            <div class="summary-number text-green-600 cyber-glow-green">
                                {{ animatedAverageScore }}%
                            </div>
                            <div class="summary-label">📊 Average Score</div>
                            <div class="summary-glow"></div>
                        </div>
                        <div class="summary-item animate-count-up" :style="{ animationDelay: '0.3s' }">
                            <div class="summary-number text-purple-600 cyber-glow-purple">
                                {{ animatedBestScore }}%
                            </div>
                            <div class="summary-label">🏆 Best Score</div>
                            <div class="summary-glow"></div>
                        </div>
                        <div class="summary-item animate-count-up" :style="{ animationDelay: '0.4s' }">
                            <div class="summary-number text-yellow-600 cyber-glow-yellow">
                                {{ animatedMostAttempted }}
                            </div>
                            <div class="summary-label">🔥 Most Attempted</div>
                            <div class="summary-glow"></div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Results Table -->
            <transition name="fade">
                <div v-if="!error" class="table-card rounded-lg shadow-md overflow-hidden animate-slide-up" 
                     :class="themeClass" :style="{ animationDelay: '0.3s' }">
                    <div class="table-header-container crt-scanlines" :class="{ 'cyber-scan': analyticsMode }">
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-fixed">
                                <thead class="table-header" :class="themeClass">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-40">
                                            👤 Name Used
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-48">
                                            📚 Quiz Set
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-32">
                                            🎯 Score
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-40">
                                            📊 Percentage
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-32">
                                            ⏱️ Time Taken
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-40">
                                            📅 Date
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-32">
                                            🔧 Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="table-body divide-y divide-gray-200 dark:divide-gray-700" :class="themeClass">
                                    <tr v-for="(result, index) in sortedResults" :key="result.id" 
                                        class="table-row hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 animate-slide-right"
                                        :style="{ animationDelay: `${index * 0.1}s` }">
                                        
                                        <!-- Name Column -->
                                        <td class="px-4 py-3 align-top">
                                            <div class="flex flex-col">
                                                <div class="participant-name font-medium text-sm text-gray-900 dark:text-gray-100">
                                                    {{ result.participant_name || 'Anonymous' }}
                                                </div>
                                                <div class="browser-info text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <span class="browser-icon">
                                                        {{ useBrowserFilter ? '🔒' : (result.browser_id === browserId ? '🔒' : '🌐') }}
                                                    </span>
                                                    {{ getBrowserLabel(result) }}
                                                    <span v-if="result.browser_id && !useBrowserFilter" 
                                                          class="browser-id inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-700 ml-1">
                                                        ID: {{ result.browser_id.slice(0, 8) }}...
                                                    </span>
                                                    <span v-if="result.browser_id === browserId && !useBrowserFilter" 
                                                          class="your-attempt-badge inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 ml-1">
                                                        ✓ Your attempt
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Quiz Set Column -->
                                        <td class="px-4 py-3 align-top">
                                            <div class="quiz-set-name text-sm text-gray-900 dark:text-gray-100">
                                                {{ getResultQuizSetName(result) }}
                                            </div>
                                        </td>
                                        
                                        <!-- Score Column -->
                                        <td class="px-4 py-3 align-top">
                                            <div class="score-container">
                                                <div class="score-value text-sm text-gray-900 dark:text-gray-100">
                                                    {{ (result.score || 0) }}/{{ (result.total_questions || 0) }}
                                                </div>
                                                <div class="score-bar mt-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                    <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full" 
                                                         :style="{ width: getSafePercentage(result) + '%' }">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Percentage Column -->
                                        <td class="px-4 py-3 align-top">
                                            <div class="percentage-container">
                                                <div class="percentage-value text-sm font-semibold" 
                                                     :class="getPercentageColor(getSafePercentage(result))">
                                                    {{ formatPercentage(getSafePercentage(result)) }}
                                                </div>
                                                <div class="performance-rating text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ getPerformanceRating(getSafePercentage(result)) }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Time Column -->
                                        <td class="px-4 py-3 align-top">
                                            <div class="time-container">
                                                <div class="time-value text-sm text-gray-900 dark:text-gray-100">
                                                    {{ formatTime(result.time_taken || 0) }}
                                                </div>
                                                <div class="time-indicator mt-1 flex items-center" v-if="analyticsMode">
                                                    <div class="time-dot w-2 h-2 rounded-full mr-1" 
                                                         :class="getTimeEfficiency(result)"></div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ getTimeEfficiencyLabel(result) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Date Column -->
                                        <td class="px-4 py-3 align-top">
                                            <div class="date-container">
                                                <div class="date-value text-sm text-gray-900 dark:text-gray-100">
                                                    {{ formatDate(result.created_at) }}
                                                </div>
                                                <div class="date-relative text-xs text-gray-500 dark:text-gray-400 mt-1" 
                                                     v-if="analyticsMode">
                                                    {{ getRelativeTime(result.created_at) }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Actions Column -->
                                        <td class="px-4 py-3 align-top">
                                            <button 
                                                @click.stop="viewDetails(result)"
                                                class="action-btn px-3 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-800/50 rounded-lg text-sm font-medium transition-all duration-200 flex items-center"
                                                :disabled="!result.quiz_set_id"
                                                @mouseenter="playHoverSound('view')"
                                            >
                                                <span class="mr-1">👁️</span>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <transition name="fade">
                        <div v-if="filteredResults.length === 0 && !loading && !error" 
                             class="empty-state text-center py-12 animate-fade-in">
                            <div class="empty-icon text-4xl mb-4 animate-float">
                                {{ getEmptyStateIcon() }}
                            </div>
                            <h3 class="empty-title mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ getEmptyStateTitle() }}
                            </h3>
                            <p class="empty-subtitle mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ getEmptyStateSubtitle() }}
                            </p>
                            <div class="empty-actions mt-6 flex justify-center gap-3">
                                <button 
                                    @click="retryLoading"
                                    class="empty-btn px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center"
                                    @mouseenter="playHoverSound('refresh')"
                                >
                                    <span class="mr-2">🔄</span>
                                    Refresh
                                </button>
                                <button 
                                    v-if="useBrowserFilter && results.length > resultsWithBrowserId.length"
                                    @click="toggleBrowserFilter"
                                    class="empty-btn px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors flex items-center"
                                    @mouseenter="playHoverSound('expand')"
                                >
                                    <span class="mr-2">👥</span>
                                    Show All Attempts
                                </button>
                            </div>
                        </div>
                    </transition>

                    <!-- Loading State -->
                    <transition name="fade">
                        <div v-if="loading && !error" class="loading-state text-center py-12 animate-fade-in">
                            <div class="loading-spinner-container mx-auto w-16 h-16 relative">
                                <div class="loading-spinner w-16 h-16 border-4 border-gray-300 dark:border-gray-600 border-t-blue-500 rounded-full animate-spin"></div>
                            </div>
                            <p class="loading-text mt-4 text-gray-600 dark:text-gray-400 flex items-center justify-center">
                                <span class="loading-dots">Loading your attempts</span>
                            </p>
                        </div>
                    </transition>

                    <!-- Pagination -->
                    <div v-if="filteredResults.length > itemsPerPage" class="pagination-container px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="pagination-info text-sm text-gray-600 dark:text-gray-400">
                                Showing {{ startIndex + 1 }}-{{ endIndex }} of {{ filteredResults.length }} results
                            </div>
                            <div class="pagination-controls flex items-center gap-2">
                                <button 
                                    @click="prevPage"
                                    :disabled="currentPage === 1"
                                    class="pagination-btn px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    ← Previous
                                </button>
                                <div class="pagination-numbers flex items-center gap-1">
                                    <button 
                                        v-for="page in visiblePages" 
                                        :key="page"
                                        @click="goToPage(page)"
                                        class="page-number w-8 h-8 flex items-center justify-center text-sm rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        :class="{ 'bg-blue-500 text-white hover:bg-blue-600': page === currentPage }"
                                    >
                                        {{ page }}
                                    </button>
                                </div>
                                <button 
                                    @click="nextPage"
                                    :disabled="currentPage === totalPages"
                                    class="pagination-btn px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    Next →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Analytics Terminal -->
            <transition name="terminal">
                <div v-if="analyticsMode && filteredResults.length > 0" class="analytics-terminal">
                    <div class="terminal-header">
                        <div class="terminal-dots">
                            <span class="dot red"></span>
                            <span class="dot yellow"></span>
                            <span class="dot green"></span>
                        </div>
                        <span class="terminal-title">analytics@records:~</span>
                        <button @click="toggleAnalyticsMode" class="terminal-close hover:text-white">×</button>
                    </div>
                    <div class="terminal-body">
                        <div class="terminal-line">
                            <span class="prompt">$</span> Records Analysis initialized
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Total Attempts: <span class="text-blue-400">{{ filteredResults.length }}</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Average Score: <span class="text-green-400">{{ calculateAverageScore() }}%</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Best Performance: <span class="text-purple-400">{{ getBestScore() }}%</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Most Active: <span class="text-yellow-400">{{ getMostAttemptedQuizSet() }}</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">$</span> Analysis complete. Type <span class="text-cyan-400">'export'</span> for data
                        </div>
                    </div>
                </div>
            </transition>
        </div>

        <!-- Enhanced Results Details Modal -->
        <transition name="modal">
            <div v-if="selectedResult" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="closeModal">
                <div class="modal-content bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto" :class="themeClass" ref="modalContent">
                    <div class="modal-header p-6 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="modal-title text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    <span class="modal-icon mr-2">📋</span>
                                    Complete Quiz Analysis
                                </h3>
                                <p class="modal-subtitle text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Participant: {{ selectedResult.participant_name || 'Anonymous' }} | 
                                    Quiz Set: {{ getResultQuizSetName(selectedResult) }} | 
                                    Date: {{ formatDate(selectedResult.created_at) }}
                                </p>
                            </div>
                            <button 
                                @click="closeModal"
                                class="modal-close text-2xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors bg-gray-100 dark:bg-gray-700 w-10 h-10 rounded-full flex items-center justify-center"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <div class="modal-body p-6">
                        <!-- Performance Overview -->
                        <div class="modal-summary-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                            <div class="modal-summary-item p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg text-center border border-blue-200 dark:border-blue-700">
                                <div class="modal-summary-value text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                                    {{ selectedResult.score || 0 }}/{{ selectedResult.total_questions || 0 }}
                                </div>
                                <div class="modal-summary-label text-sm text-blue-600 dark:text-blue-400 font-medium flex items-center justify-center">
                                    <span class="mr-2">🎯</span>Score
                                </div>
                                <div class="text-xs text-blue-500 dark:text-blue-300 mt-2">
                                    {{ calculateCorrectPercentage() }} correct
                                </div>
                            </div>
                            
                            <div class="modal-summary-item p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg text-center border border-green-200 dark:border-green-700">
                                <div class="modal-summary-value text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                                    {{ formatPercentage(getSafePercentage(selectedResult)) }}
                                </div>
                                <div class="modal-summary-label text-sm text-green-600 dark:text-green-400 font-medium flex items-center justify-center">
                                    <span class="mr-2">📊</span>Percentage
                                </div>
                                <div class="text-xs text-green-500 dark:text-green-300 mt-2">
                                    {{ getPerformanceRating(getSafePercentage(selectedResult)) }}
                                </div>
                            </div>
                            
                            <div class="modal-summary-item p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg text-center border border-purple-200 dark:border-purple-700">
                                <div class="modal-summary-value text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                                    {{ formatTime(selectedResult.time_taken || 0) }}
                                </div>
                                <div class="modal-summary-label text-sm text-purple-600 dark:text-purple-400 font-medium flex items-center justify-center">
                                    <span class="mr-2">⏱️</span>Time Taken
                                </div>
                                <div class="text-xs text-purple-500 dark:text-purple-300 mt-2">
                                    {{ calculateTimePerQuestion() }} per question
                                </div>
                            </div>
                            
                            <div class="modal-summary-item p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-lg text-center border border-yellow-200 dark:border-yellow-700">
                                <div class="modal-summary-value text-3xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">
                                    {{ getSkippedCount() }}
                                </div>
                                <div class="modal-summary-label text-sm text-yellow-600 dark:text-yellow-400 font-medium flex items-center justify-center">
                                    <span class="mr-2">⏭️</span>Skipped Questions
                                </div>
                                <div class="text-xs text-yellow-500 dark:text-yellow-300 mt-2">
                                    {{ calculateSkippedPercentage() }} skipped
                                </div>
                            </div>
                        </div>

                        <!-- Performance Breakdown -->
                        <div class="modal-performance-grid grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="modal-performance-item p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <h4 class="modal-performance-title text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <span class="mr-2">✅</span>Correct Answers
                                </h4>
                                <div class="space-y-2">
                                    <div class="performance-stat flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Correct:</span>
                                        <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                            {{ getCorrectCount() }}
                                        </span>
                                    </div>
                                    <div class="performance-bar h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" 
                                             :style="{ width: calculateCorrectPercentage() }"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-performance-item p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <h4 class="modal-performance-title text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <span class="mr-2">❌</span>Wrong Answers
                                </h4>
                                <div class="space-y-2">
                                    <div class="performance-stat flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Wrong:</span>
                                        <span class="text-lg font-bold text-red-600 dark:text-red-400">
                                            {{ getWrongCount() }}
                                        </span>
                                    </div>
                                    <div class="performance-bar h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500 rounded-full" 
                                             :style="{ width: calculateWrongPercentage() }"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-performance-item p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <h4 class="modal-performance-title text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <span class="mr-2">📊</span>Performance Score
                                </h4>
                                <div class="space-y-2">
                                    <div class="performance-stat flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Rating:</span>
                                        <span class="text-lg font-bold" :class="getPercentageColor(getSafePercentage(selectedResult))">
                                            {{ getPerformanceRating(getSafePercentage(selectedResult)) }}
                                        </span>
                                    </div>
                                    <div class="performance-bar h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-1000" 
                                             :class="getPerformanceBarColor(getSafePercentage(selectedResult))"
                                             :style="{ width: Math.min(getSafePercentage(selectedResult), 100) + '%' }">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Section with Fixed Background -->
                        <div class="questions-section mb-8">
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="questions-title text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                    <span class="mr-2">❓</span>
                                    All Questions & Answers
                                    <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                        ({{ getTotalQuestions() }} total questions)
                                    </span>
                                </h4>
                                <div class="flex items-center space-x-2">
                                    <div class="legend flex items-center space-x-4 text-xs">
                                        <div class="legend-item flex items-center">
                                            <div class="w-3 h-3 bg-green-500 rounded mr-1"></div>
                                            <span class="text-gray-600 dark:text-gray-400">Correct</span>
                                        </div>
                                        <div class="legend-item flex items-center">
                                            <div class="w-3 h-3 bg-red-500 rounded mr-1"></div>
                                            <span class="text-gray-600 dark:text-gray-400">Wrong</span>
                                        </div>
                                        <div class="legend-item flex items-center">
                                            <div class="w-3 h-3 bg-blue-500 rounded mr-1"></div>
                                            <span class="text-gray-600 dark:text-gray-400">Skipped</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Questions Container with Background -->
                            <div class="questions-container bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <!-- Questions List -->
                                <div v-if="selectedResult.answers && getTotalQuestions() > 0" class="questions-grid grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div 
                                        v-for="(answer, questionIndex) in getAllQuestions()" 
                                        :key="questionIndex"
                                        class="question-card p-4 border-2 rounded-xl transition-all duration-300 hover:shadow-lg"
                                        :class="getQuestionStatusClass(questionIndex)"
                                    >
                                        <!-- Question Header -->
                                        <div class="question-header flex items-start justify-between mb-4">
                                            <div class="flex items-center">
                                                <div class="question-number w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full mr-3">
                                                    <span class="font-bold text-gray-900 dark:text-gray-100">
                                                        {{ parseInt(questionIndex) + 1 }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="question-title font-semibold text-gray-900 dark:text-gray-100">
                                                        Question {{ parseInt(questionIndex) + 1 }}
                                                    </h5>
                                                    <p class="question-text text-gray-700 dark:text-gray-300 text-sm mt-1">
                                                        {{ getQuestionText(questionIndex) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="question-status-badge px-3 py-1 text-xs font-medium rounded-full shadow-sm"
                                                  :class="getQuestionStatusBadgeClass(questionIndex)">
                                                {{ getQuestionStatus(questionIndex) }}
                                            </span>
                                        </div>

                                        <!-- Options -->
                                        <div class="question-options space-y-2 mb-4">
                                            <div 
                                                v-for="(option, optIndex) in getQuestionOptions(questionIndex)" 
                                                :key="optIndex"
                                                class="question-option p-3 border rounded-lg transition-all duration-200"
                                                :class="getOptionClass(questionIndex, option, answer)"
                                            >
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <span class="option-letter font-bold mr-3 w-6 h-6 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded">
                                                            {{ String.fromCharCode(65 + optIndex) }}
                                                        </span>
                                                        <span class="option-text">
                                                            {{ option }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Option Markers -->
                                                    <div class="option-markers flex space-x-2">
                                                        <span v-if="option === getCorrectAnswer(questionIndex)" 
                                                              class="option-marker correct px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 text-xs rounded flex items-center">
                                                            <span class="mr-1">✅</span> Correct
                                                        </span>
                                                        <span v-if="option === answer && option !== getCorrectAnswer(questionIndex)" 
                                                              class="option-marker wrong px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100 text-xs rounded flex items-center">
                                                            <span class="mr-1">❌</span> Your Answer
                                                        </span>
                                                        <span v-if="option === answer && option === getCorrectAnswer(questionIndex)" 
                                                              class="option-marker correct-user px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 text-xs rounded flex items-center">
                                                            <span class="mr-1">🎯</span> Correct Answer
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Show correct answer for skipped questions -->
                                                <div v-if="!answer && option === getCorrectAnswer(questionIndex)" 
                                                     class="skipped-info mt-2 pt-2 border-t border-blue-200 dark:border-blue-700">
                                                    <div class="text-blue-600 dark:text-blue-400 text-sm flex items-center">
                                                        <span class="skipped-icon mr-2">💡</span>
                                                        <span class="font-medium">Correct Answer:</span>
                                                        <span class="ml-2">{{ option }}</span>
                                                    </div>
                                                    <p class="text-xs text-blue-500 dark:text-blue-300 mt-1">
                                                        You skipped this question. The correct answer is highlighted.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Answer Explanation (if available) -->
                                        <div v-if="getQuestionExplanation(questionIndex)" 
                                             class="explanation mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                                            <div class="flex items-start">
                                                <span class="explanation-icon mr-2">💡</span>
                                                <div>
                                                    <div class="explanation-title font-medium text-blue-800 dark:text-blue-300 mb-1">
                                                        Explanation
                                                    </div>
                                                    <p class="explanation-text text-blue-700 dark:text-blue-400 text-sm">
                                                        {{ getQuestionExplanation(questionIndex) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- User's Answer Status -->
                                        <div v-if="answer !== null" class="user-answer-status mt-4 p-3 rounded-lg"
                                             :class="answer === getCorrectAnswer(questionIndex) 
                                                    ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700'
                                                    : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700'">
                                            <div class="flex items-center">
                                                <span class="status-icon mr-2">
                                                    {{ answer === getCorrectAnswer(questionIndex) ? '✅' : '❌' }}
                                                </span>
                                                <div>
                                                    <span class="status-text font-medium" 
                                                          :class="answer === getCorrectAnswer(questionIndex) 
                                                                ? 'text-green-700 dark:text-green-300'
                                                                : 'text-red-700 dark:text-red-300'">
                                                        {{ answer === getCorrectAnswer(questionIndex) 
                                                            ? 'You answered correctly!' 
                                                            : 'Your answer was incorrect.' }}
                                                    </span>
                                                    <div class="status-detail text-sm mt-1"
                                                         :class="answer === getCorrectAnswer(questionIndex) 
                                                                ? 'text-green-600 dark:text-green-400'
                                                                : 'text-red-600 dark:text-red-400'">
                                                        You selected: <span class="font-medium">{{ answer }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="user-answer-status mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                                            <div class="flex items-center">
                                                <span class="status-icon mr-2">⏭️</span>
                                                <div>
                                                    <span class="status-text font-medium text-blue-700 dark:text-blue-300">
                                                        You skipped this question
                                                    </span>
                                                    <div class="status-detail text-sm text-blue-600 dark:text-blue-400 mt-1">
                                                        The correct answer was: <span class="font-medium">{{ getCorrectAnswer(questionIndex) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="modal-no-details text-center py-12">
                                    <div class="no-details-icon text-4xl mb-4 animate-float">📭</div>
                                    <h4 class="no-details-title text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                        No Question Details Available
                                    </h4>
                                    <p class="no-details-text text-gray-600 dark:text-gray-400">
                                        The detailed question information could not be loaded.
                                    </p>
                                    <button @click="closeModal" 
                                            class="mt-6 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Summary -->
                        <div class="statistics-summary p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900/30 dark:to-gray-800/30 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h4 class="statistics-title text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <span class="mr-2">📈</span>
                                Performance Statistics
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="stat-item p-3 bg-white dark:bg-gray-800 rounded-lg text-center">
                                    <div class="stat-value text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ getCorrectCount() }}
                                    </div>
                                    <div class="stat-label text-sm text-gray-600 dark:text-gray-400">Correct Answers</div>
                                    <div class="stat-percentage text-xs text-green-500 dark:text-green-300">
                                        {{ calculateCorrectPercentage() }}
                                    </div>
                                </div>
                                <div class="stat-item p-3 bg-white dark:bg-gray-800 rounded-lg text-center">
                                    <div class="stat-value text-2xl font-bold text-red-600 dark:text-red-400">
                                        {{ getWrongCount() }}
                                    </div>
                                    <div class="stat-label text-sm text-gray-600 dark:text-gray-400">Wrong Answers</div>
                                    <div class="stat-percentage text-xs text-red-500 dark:text-red-300">
                                        {{ calculateWrongPercentage() }}
                                    </div>
                                </div>
                                <div class="stat-item p-3 bg-white dark:bg-gray-800 rounded-lg text-center">
                                    <div class="stat-value text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ getSkippedCount() }}
                                    </div>
                                    <div class="stat-label text-sm text-gray-600 dark:text-gray-400">Skipped Questions</div>
                                    <div class="stat-percentage text-xs text-blue-500 dark:text-blue-300">
                                        {{ calculateSkippedPercentage() }}
                                    </div>
                                </div>
                                <div class="stat-item p-3 bg-white dark:bg-gray-800 rounded-lg text-center">
                                    <div class="stat-value text-2xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ formatTime(selectedResult.time_taken || 0) }}
                                    </div>
                                    <div class="stat-label text-sm text-gray-600 dark:text-gray-400">Total Time</div>
                                    <div class="stat-percentage text-xs text-purple-500 dark:text-purple-300">
                                        {{ calculateTimePerQuestion() }} per question
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer p-6 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky bottom-0">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="footer-text text-sm text-gray-600 dark:text-gray-400">
                                Quiz completed on {{ formatDate(selectedResult.created_at) }}
                            </div>
                            <div class="footer-actions flex gap-3">
                                <button @click="closeModal" 
                                        class="modal-close-btn px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
                                    Close
                                </button>
                                
                                <!-- Export Dropdown -->
                                <div class="relative group">
                                    <button 
                                        class="modal-export-btn px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 font-medium flex items-center shadow-md hover:shadow-lg">
                                        <span class="mr-2">📥</span>
                                        Export Results
                                        <span class="ml-2">▼</span>
                                    </button>
                                    
                                    <div class="absolute bottom-full left-0 mb-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                        <button @click="exportResultAsPDF" 
                                                class="w-full text-left px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span class="mr-3 text-red-500">📄</span>
                                                <div>
                                                    <div class="font-medium">Export as PDF</div>
                                                    <div class="text-xs text-gray-500">Download printable report</div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400">PDF</span>
                                        </button>
                                        <button @click="exportResultAsJSON" 
                                                class="w-full text-left px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center justify-between border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center">
                                                <span class="mr-3 text-blue-500">📊</span>
                                                <div>
                                                    <div class="font-medium">Export as JSON</div>
                                                    <div class="text-xs text-gray-500">Download raw data</div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400">JSON</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <button v-if="hasPreviousAttempt()" @click="navigateToPreviousAttempt"
                                        class="nav-btn px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 font-medium flex items-center">
                                    <span class="mr-2">←</span>
                                    Previous
                                </button>
                                <button v-if="hasNextAttempt()" @click="navigateToNextAttempt"
                                        class="nav-btn px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-lg hover:from-purple-600 hover:to-pink-700 transition-all duration-300 font-medium flex items-center">
                                    Next
                                    <span class="ml-2">→</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Particle Container for Effects -->
        <div class="particle-container" ref="particleContainer"></div>

        <!-- Matrix Rain Effect (Analytics Mode) -->
        <div class="matrix-rain" :class="{ active: analyticsMode }"></div>
    </div>
</template>

<script>
// Install required packages:
// npm install jspdf html2canvas
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

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
            useBrowserFilter: true, // Changed to true by default
            
            // Animation states
            particles: [],
            analyticsMode: false,
            showAnalyticsTooltip: false,
            analyticsProgress: 0,
            analyticsSequence: ['A', 'N', 'A', 'L', 'Y', 'T', 'I', 'C', 'S'],
            
            // Animated values
            animatedAttempts: 0,
            animatedAverageScore: 0,
            animatedBestScore: 0,
            animatedMostAttempted: '',
            
            // Table sorting
            sortKey: 'date',
            sortOrder: 'desc',
            
            // Pagination
            currentPage: 1,
            itemsPerPage: 10,
            
            // Performance tracking
            performanceProgress: [
                { label: 'Accuracy', value: '0%', percentage: 0 },
                { label: 'Speed', value: '0s/q', percentage: 0 },
                { label: 'Consistency', value: '0%', percentage: 0 }
            ]
        }
    },
    computed: {
        themeClass() {
            return this.theme === 'dark' ? 'dark-theme' : 'light-theme';
        },
        resultsWithBrowserId() {
            return this.results.filter(result => result.browser_id);
        },
        filteredResults() {
            let filtered = this.results;
            
            // Always filter by browser ID by default
            if (this.useBrowserFilter && this.browserId) {
                filtered = filtered.filter(result => result.browser_id === this.browserId);
            }
            
            // Apply text filter
            if (this.filters.participant_name) {
                const searchTerm = this.filters.participant_name.toLowerCase();
                filtered = filtered.filter(result => 
                    result.participant_name?.toLowerCase().includes(searchTerm)
                );
            }
            
            // Apply quiz set filter
            if (this.filters.quiz_set_id) {
                filtered = filtered.filter(result => 
                    result.quiz_set_id == this.filters.quiz_set_id
                );
            }
            
            // Apply date filter
            if (this.filters.date) {
                filtered = filtered.filter(result => {
                    const resultDate = new Date(result.created_at).toISOString().split('T')[0];
                    return resultDate === this.filters.date;
                });
            }
            
            return filtered;
        },
        sortedResults() {
            const results = [...this.filteredResults];
            
            return results.sort((a, b) => {
                let aVal, bVal;
                
                switch (this.sortKey) {
                    case 'name':
                        aVal = a.participant_name || '';
                        bVal = b.participant_name || '';
                        break;
                    case 'score':
                        aVal = this.getSafePercentage(a);
                        bVal = this.getSafePercentage(b);
                        break;
                    case 'percentage':
                        aVal = this.getSafePercentage(a);
                        bVal = this.getSafePercentage(b);
                        break;
                    case 'time':
                        aVal = a.time_taken || 0;
                        bVal = b.time_taken || 0;
                        break;
                    case 'date':
                    default:
                        aVal = new Date(a.created_at).getTime();
                        bVal = new Date(b.created_at).getTime();
                }
                
                if (this.sortOrder === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            }).slice(this.startIndex, this.endIndex);
        },
        totalPages() {
            return Math.ceil(this.filteredResults.length / this.itemsPerPage);
        },
        startIndex() {
            return (this.currentPage - 1) * this.itemsPerPage;
        },
        endIndex() {
            return Math.min(this.currentPage * this.itemsPerPage, this.filteredResults.length);
        },
        visiblePages() {
            const pages = [];
            const maxVisible = 5;
            let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
            let end = Math.min(this.totalPages, start + maxVisible - 1);
            
            if (end - start + 1 < maxVisible) {
                start = Math.max(1, end - maxVisible + 1);
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        }
    },
    async mounted() {
        try {
            await this.initializeBrowserId();
            await this.fetchQuizSets();
            await this.fetchResults();
            
            // Auto-enable browser filter if we have results from this browser
            if (this.resultsWithBrowserId.length > 0) {
                this.useBrowserFilter = true;
            }
            
            this.initParticles();
            this.initAnalyticsCode();
        } catch (error) {
            console.error('Error in mounted:', error);
            this.error = 'Failed to initialize component: ' + error.message;
        }
    },
    beforeDestroy() {
        if (this.analyticsMode) {
            document.body.classList.remove('analytics-mode-active');
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
            // If user tries to turn OFF browser filter, confirm first
            if (this.useBrowserFilter) {
                const confirmTurnOff = confirm(
                    'You are about to view ALL attempts from ALL browsers. ' +
                    'Are you sure you want to see other people\'s results?'
                );
                
                if (!confirmTurnOff) {
                    return;
                }
                
                this.useBrowserFilter = false;
                this.showNotification('Showing ALL attempts from ALL browsers', 'info');
            } else {
                // Trying to turn ON browser filter
                if (this.resultsWithBrowserId.length === 0) {
                    console.log('No browser-specific results found');
                    alert('No quiz attempts found from this browser. Take a quiz first to see your attempts here.');
                    return;
                }
                
                this.useBrowserFilter = true;
                this.showNotification('Now showing only your browser\'s attempts', 'success');
            }
            
            console.log('🔄 Browser filter:', this.useBrowserFilter ? 'ON' : 'OFF');
            
            if (this.useBrowserFilter) {
                this.createFilterEffect();
            }
        },

        getBrowserLabel(result) {
            if (this.useBrowserFilter) {
                return 'Your browser';
            } else {
                return result.browser_id === this.browserId ? 'Your browser' : 'Other browser';
            }
        },

        async initializeBrowserId() {
            try {
                let browserId = localStorage.getItem('quiz_browser_id');
                
                if (!browserId) {
                    browserId = this.generateBrowserId();
                    localStorage.setItem('quiz_browser_id', browserId);
                }
                
                this.browserId = browserId;
            } catch (error) {
                console.error('Error initializing browser ID:', error);
                this.browserId = 'temp_' + Date.now();
            }
        },

        generateBrowserId() {
            const timestamp = Date.now().toString(36);
            const random = Math.random().toString(36).substr(2, 9);
            return `browser_${timestamp}_${random}`;
        },

        getEmptyStateIcon() {
            if (this.useBrowserFilter) {
                return '🔒';
            } else {
                return '👥';
            }
        },

        getEmptyStateTitle() {
            if (this.useBrowserFilter) {
                if (this.resultsWithBrowserId.length === 0) {
                    return 'No attempts from this browser';
                } else {
                    return 'No matching attempts found';
                }
            } else {
                return 'No quiz attempts found';
            }
        },

        getEmptyStateSubtitle() {
            if (this.useBrowserFilter) {
                if (this.resultsWithBrowserId.length === 0) {
                    return 'Take a quiz first to see your attempts here.';
                } else {
                    return 'Try adjusting your filters to see more results.';
                }
            } else {
                return 'No results match your current filters.';
            }
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
                allQuestions[i] = this.selectedResult.answers?.[i] || null;
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

        calculateCorrectPercentage() {
            const total = this.getTotalQuestions();
            if (total === 0) return '0%';
            const percentage = (this.getCorrectCount() / total) * 100;
            return percentage.toFixed(1) + '%';
        },

        calculateWrongPercentage() {
            const total = this.getTotalQuestions();
            if (total === 0) return '0%';
            const percentage = (this.getWrongCount() / total) * 100;
            return percentage.toFixed(1) + '%';
        },

        calculateSkippedPercentage() {
            const total = this.getTotalQuestions();
            if (total === 0) return '0%';
            const percentage = (this.getSkippedCount() / total) * 100;
            return percentage.toFixed(1) + '%';
        },

        calculateTimePerQuestion() {
            const totalQuestions = this.getTotalQuestions();
            const totalTime = this.selectedResult.time_taken || 0;
            if (totalQuestions === 0) return '0s';
            const timePerQuestion = totalTime / totalQuestions;
            return timePerQuestion.toFixed(1) + 's';
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
                    return 'question-card correct';
                case 'Wrong':
                    return 'question-card wrong';
                case 'Skipped':
                    return 'question-card skipped';
                default:
                    return 'question-card';
            }
        },

        getQuestionStatusBadgeClass(questionIndex) {
            const status = this.getQuestionStatus(questionIndex);
            
            switch (status) {
                case 'Correct':
                    return 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 border border-green-200 dark:border-green-700';
                case 'Wrong':
                    return 'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 border border-red-200 dark:border-red-700';
                case 'Skipped':
                    return 'bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 border border-blue-200 dark:border-blue-700';
                default:
                    return 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700';
            }
        },

        getOptionClass(questionIndex, option, userAnswer) {
            const correctAnswer = this.getCorrectAnswer(questionIndex);
            const isCorrect = option === correctAnswer;
            const isUserAnswer = option === userAnswer;
            
            let classes = 'question-option';
            
            if (isCorrect && isUserAnswer) {
                classes += ' correct';
            } else if (isCorrect && !isUserAnswer) {
                classes += ' correct-unselected';
            } else if (!isCorrect && isUserAnswer) {
                classes += ' wrong';
            } else if (!userAnswer && isCorrect) {
                classes += ' correct-skipped';
            } else {
                classes += ' normal';
            }
            
            return classes;
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

                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                this.results = Array.isArray(data) ? data.map(result => ({
                    ...result,
                    percentage: this.getSafePercentage(result)
                })) : [];

                // Animate the summary values
                this.animateSummaryValues();
                
            } catch (error) {
                console.error('❌ Error fetching results:', error);
                this.error = `Failed to load results: ${error.message}`;
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        animateSummaryValues() {
            // Animate attempts count
            this.animateValue('animatedAttempts', this.filteredResults.length, 1000);
            
            // Animate average score
            const avg = this.calculateAverageScore();
            this.animateValue('animatedAverageScore', avg, 1200);
            
            // Animate best score
            const best = this.getBestScore();
            this.animateValue('animatedBestScore', best, 1400);
            
            // Set most attempted
            this.animatedMostAttempted = this.getMostAttemptedQuizSet();
        },

        animateValue(property, target, duration) {
            const start = this[property];
            const increment = (target - start) / 60;
            let current = start;
            let step = 0;
            
            const timer = setInterval(() => {
                step++;
                current += increment;
                
                if (typeof target === 'number') {
                    this[property] = Math.round(current * 10) / 10;
                } else {
                    this[property] = target;
                }
                
                if (step >= 60 || Math.abs(current - target) < 0.1) {
                    this[property] = target;
                    clearInterval(timer);
                }
            }, duration / 60);
        },

        async fetchQuizSets() {
            try {
                const response = await fetch('/api/quiz-sets');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.quizSets = Array.isArray(data) ? data : [];
            } catch (error) {
                console.error('Error fetching quiz sets:', error);
                this.error = `Failed to load quiz sets: ${error.message}`;
                this.quizSets = [];
            }
        },

        retryLoading() {
            this.error = null;
            this.createRetryEffect();
            this.fetchQuizSets();
            this.fetchResults();
        },

        applyFilters() {
            this.createApplyEffect();
            this.currentPage = 1;
            this.fetchResults();
        },

        clearFilters() {
            this.filters = {
                participant_name: '',
                quiz_set_id: '',
                date: ''
            };
            this.createClearEffect();
            this.currentPage = 1;
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
            if (perc >= 80) return 'text-green-600 dark:text-green-400';
            if (perc >= 60) return 'text-yellow-600 dark:text-yellow-400';
            return 'text-red-600 dark:text-red-400';
        },

        getPerformanceBarColor(percentage) {
            const perc = Number(percentage) || 0;
            if (perc >= 80) return 'bg-gradient-to-r from-green-500 to-emerald-500';
            if (perc >= 60) return 'bg-gradient-to-r from-yellow-500 to-amber-500';
            return 'bg-gradient-to-r from-red-500 to-pink-500';
        },

        getPerformanceTextColor(percentage) {
            const perc = Number(percentage) || 0;
            if (perc >= 80) return 'text-green-600 dark:text-green-400';
            if (perc >= 60) return 'text-yellow-600 dark:text-yellow-400';
            return 'text-red-600 dark:text-red-400';
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
            if (this.filteredResults.length === 0) return 0;
            const total = this.filteredResults.reduce((sum, result) => sum + this.getSafePercentage(result), 0);
            return parseFloat((total / this.filteredResults.length).toFixed(1));
        },

        getBestScore() {
            if (this.filteredResults.length === 0) return 0;
            const best = Math.max(...this.filteredResults.map(result => this.getSafePercentage(result)));
            return parseFloat(best.toFixed(1));
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

        getTimeEfficiency(result) {
            const timePerQuestion = (result.time_taken || 0) / (result.total_questions || 1);
            if (timePerQuestion < 30) return 'bg-green-500';
            if (timePerQuestion < 60) return 'bg-yellow-500';
            return 'bg-red-500';
        },

        getTimeEfficiencyLabel(result) {
            const timePerQuestion = (result.time_taken || 0) / (result.total_questions || 1);
            if (timePerQuestion < 30) return 'Fast';
            if (timePerQuestion < 60) return 'Medium';
            return 'Slow';
        },

        formatDate(dateString) {
            if (!dateString) return 'Unknown date';
            try {
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return 'Invalid date';
            }
        },

        getRelativeTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            return `${Math.floor(diffDays / 7)}w ago`;
        },

        async viewDetails(result) {
            this.selectedResult = result;
            this.createViewEffect();
            
            if (!result.quiz_set_id) {
                this.error = 'Cannot load details: Quiz set ID is missing';
                return;
            }

            await this.loadQuizDetails();
        },

        async loadQuizDetails() {
            if (!this.selectedResult.quiz_set_id) return;
            
            if (!this.quizDetails[this.selectedResult.quiz_set_id]) {
                try {
                    const response = await fetch(`/api/quiz-sets/${this.selectedResult.quiz_set_id}/quizzes`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    this.quizDetails[this.selectedResult.quiz_set_id] = await response.json();
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
        },

        getQuestionExplanation(questionIndex) {
            const quizSetQuizzes = this.quizDetails[this.selectedResult.quiz_set_id];
            if (!quizSetQuizzes || !Array.isArray(quizSetQuizzes)) return '';
            
            const quiz = quizSetQuizzes[questionIndex];
            return quiz?.explanation || '';
        },

        hasPreviousAttempt() {
            const currentIndex = this.filteredResults.findIndex(r => r.id === this.selectedResult.id);
            return currentIndex > 0;
        },

        hasNextAttempt() {
            const currentIndex = this.filteredResults.findIndex(r => r.id === this.selectedResult.id);
            return currentIndex < this.filteredResults.length - 1;
        },

        navigateToPreviousAttempt() {
            const currentIndex = this.filteredResults.findIndex(r => r.id === this.selectedResult.id);
            if (currentIndex > 0) {
                this.selectedResult = this.filteredResults[currentIndex - 1];
                this.loadQuizDetails();
            }
        },

        navigateToNextAttempt() {
            const currentIndex = this.filteredResults.findIndex(r => r.id === this.selectedResult.id);
            if (currentIndex < this.filteredResults.length - 1) {
                this.selectedResult = this.filteredResults[currentIndex + 1];
                this.loadQuizDetails();
            }
        },

        // Pagination methods
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.createPageEffect();
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.createPageEffect();
            }
        },

        goToPage(page) {
            this.currentPage = page;
            this.createPageEffect();
        },

        // Animation methods
        initParticles() {
            for (let i = 0; i < 20; i++) {
                this.particles.push({
                    style: {
                        left: `${Math.random() * 100}%`,
                        top: `${Math.random() * 100}%`,
                        animationDelay: `${Math.random() * 5}s`,
                        width: `${Math.random() * 6 + 2}px`,
                        height: `${Math.random() * 6 + 2}px`,
                        background: `hsl(${Math.random() * 360}, 70%, 60%)`,
                        opacity: Math.random() * 0.3 + 0.1
                    }
                });
            }
        },

        initAnalyticsCode() {
            const analyticsMap = {
                'KeyA': 'A',
                'KeyN': 'N',
                'KeyL': 'L',
                'KeyY': 'Y',
                'KeyT': 'T',
                'KeyI': 'I',
                'KeyC': 'C',
                'KeyS': 'S'
            };
            
            let sequence = [];
            
            document.addEventListener('keydown', (e) => {
                const key = analyticsMap[e.code];
                if (key) {
                    sequence.push(key);
                    
                    if (sequence.length > this.analyticsSequence.length) {
                        sequence.shift();
                    }
                    
                    this.analyticsProgress = 0;
                    for (let i = 0; i < sequence.length; i++) {
                        if (sequence[i] === this.analyticsSequence[i]) {
                            this.analyticsProgress = i + 1;
                        } else {
                            sequence = [];
                            this.analyticsProgress = 0;
                            break;
                        }
                    }
                    
                    if (this.analyticsProgress === this.analyticsSequence.length) {
                        this.toggleAnalyticsMode();
                        sequence = [];
                        this.analyticsProgress = 0;
                    }
                } else {
                    sequence = [];
                    this.analyticsProgress = 0;
                }
            });
        },

        toggleAnalyticsMode() {
            this.analyticsMode = !this.analyticsMode;
            if (this.analyticsMode) {
                document.body.classList.add('analytics-mode-active');
                this.createAnalyticsEffect();
                this.showNotification("Advanced Analytics Activated!", "success");
            } else {
                document.body.classList.remove('analytics-mode-active');
            }
        },

        playHoverSound(type) {
            // In a real app, you would play actual sounds
            console.log(`Playing ${type} sound`);
        },

        playFocusSound() {
            console.log('Playing focus sound');
        },

        createAnalyticsEffect() {
            this.createParticleBurst(10, '#00ffff');
        },

        createFilterEffect() {
            this.createParticleBurst(5, '#3b82f6');
        },

        createApplyEffect() {
            this.createParticleBurst(8, '#10b981');
        },

        createClearEffect() {
            this.createParticleBurst(6, '#ef4444');
        },

        createRetryEffect() {
            this.createParticleBurst(12, '#f59e0b');
        },

        createViewEffect() {
            this.createParticleBurst(15, '#8b5cf6');
        },

        createPageEffect() {
            this.createParticleBurst(3, '#6366f1');
        },

        createParticleBurst(count, color) {
            const container = this.$refs.particleContainer;
            if (!container) return;
            
            for (let i = 0; i < count; i++) {
                const particle = document.createElement('div');
                particle.className = 'burst-particle';
                particle.style.cssText = `
                    --tx: ${(Math.random() - 0.5) * 80}px;
                    --ty: ${(Math.random() - 0.5) * 80}px;
                    background: ${color};
                    left: ${Math.random() * 100}%;
                    top: ${Math.random() * 100}%;
                `;
                container.appendChild(particle);
                
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.parentNode.removeChild(particle);
                    }
                }, 800);
            }
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification-slide notification-${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#3b82f6'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        },

        async exportResultAsPDF() {
            if (!this.selectedResult) return;
            
            try {
                this.showNotification('Generating PDF...', 'info');
                
                // Create a temporary container for PDF generation
                const pdfContainer = document.createElement('div');
                pdfContainer.style.cssText = `
                    position: absolute;
                    left: -9999px;
                    top: -9999px;
                    width: 800px;
                    padding: 40px;
                    background: white;
                    color: black;
                    font-family: Arial, sans-serif;
                `;
                
                // Generate PDF content
                const participantName = this.selectedResult.participant_name || 'Anonymous';
                const quizSetName = this.getResultQuizSetName(this.selectedResult);
                const date = this.formatDate(this.selectedResult.created_at);
                const score = `${this.selectedResult.score || 0}/${this.selectedResult.total_questions || 0}`;
                const percentage = this.formatPercentage(this.getSafePercentage(this.selectedResult));
                const timeTaken = this.formatTime(this.selectedResult.time_taken || 0);
                const correctCount = this.getCorrectCount();
                const wrongCount = this.getWrongCount();
                const skippedCount = this.getSkippedCount();
                const totalQuestions = this.getTotalQuestions();
                
                let questionsHTML = '';
                const allQuestions = this.getAllQuestions();
                
                Object.keys(allQuestions).forEach((index, i) => {
                    const questionNum = parseInt(index) + 1;
                    const questionText = this.getQuestionText(index);
                    const userAnswer = allQuestions[index] || 'Skipped';
                    const correctAnswer = this.getCorrectAnswer(index);
                    const status = this.getQuestionStatus(index);
                    const explanation = this.getQuestionExplanation(index);
                    const options = this.getQuestionOptions(index);
                    
                    questionsHTML += `
                        <div style="margin-bottom: 30px; page-break-inside: avoid;">
                            <div style="background: ${status === 'Correct' ? '#d1fae5' : status === 'Wrong' ? '#fee2e2' : '#dbeafe'}; 
                                        padding: 15px; border-radius: 8px; border-left: 5px solid ${status === 'Correct' ? '#10b981' : status === 'Wrong' ? '#ef4444' : '#3b82f6'};">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <h4 style="margin: 0; font-size: 18px; color: #1f2937;">Question ${questionNum}</h4>
                                    <span style="background: ${status === 'Correct' ? '#10b981' : status === 'Wrong' ? '#ef4444' : '#3b82f6'}; 
                                                color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                                        ${status}
                                    </span>
                                </div>
                                <p style="margin: 0 0 15px 0; font-size: 14px; color: #4b5563;">${questionText}</p>
                                
                                <div style="margin-bottom: 15px;">
                                    ${options.map((option, optIndex) => {
                                        const isCorrect = option === correctAnswer;
                                        const isUserAnswer = option === userAnswer;
                                        let optionStyle = 'padding: 8px 12px; margin: 4px 0; border-radius: 6px; ';
                                        
                                        if (isCorrect && isUserAnswer) {
                                            optionStyle += 'background: #d1fae5; border: 2px solid #10b981;';
                                        } else if (isCorrect) {
                                            optionStyle += 'background: #f0fdf4; border: 1px solid #bbf7d0;';
                                        } else if (isUserAnswer && !isCorrect) {
                                            optionStyle += 'background: #fee2e2; border: 2px solid #ef4444;';
                                        } else {
                                            optionStyle += 'background: #f9fafb; border: 1px solid #e5e7eb;';
                                        }
                                        
                                        return `
                                            <div style="${optionStyle}">
                                                <strong>${String.fromCharCode(65 + optIndex)}.</strong> ${option}
                                                ${isCorrect ? '<span style="color: #10b981; margin-left: 10px;">✓ Correct</span>' : ''}
                                                ${isUserAnswer && !isCorrect ? '<span style="color: #ef4444; margin-left: 10px;">✗ Your Answer</span>' : ''}
                                            </div>
                                        `;
                                    }).join('')}
                                </div>
                                
                                <div style="background: #f8fafc; padding: 10px; border-radius: 6px; font-size: 12px;">
                                    <strong>Your Answer:</strong> ${userAnswer}<br>
                                    <strong>Correct Answer:</strong> ${correctAnswer}
                                    ${explanation ? `<br><strong>Explanation:</strong> ${explanation}` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                pdfContainer.innerHTML = `
                    <div style="max-width: 800px; margin: 0 auto;">
                        <!-- Header -->
                        <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #3b82f6; padding-bottom: 20px;">
                            <h1 style="color: #1e40af; font-size: 32px; margin: 0 0 10px 0;">Quiz Results Report</h1>
                            <div style="display: flex; justify-content: center; gap: 30px; font-size: 14px; color: #6b7280;">
                                <div><strong>Participant:</strong> ${participantName}</div>
                                <div><strong>Quiz Set:</strong> ${quizSetName}</div>
                                <div><strong>Date:</strong> ${date}</div>
                            </div>
                        </div>
                        
                        <!-- Performance Summary -->
                        <div style="margin-bottom: 40px;">
                            <h2 style="color: #1e40af; font-size: 24px; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                Performance Summary
                            </h2>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                                <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 2px solid #e5e7eb;">
                                    <div style="font-size: 28px; color: #1e40af; font-weight: bold; margin-bottom: 5px;">${score}</div>
                                    <div style="font-size: 14px; color: #6b7280;">Score</div>
                                </div>
                                <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 2px solid #e5e7eb;">
                                    <div style="font-size: 28px; color: #10b981; font-weight: bold; margin-bottom: 5px;">${percentage}</div>
                                    <div style="font-size: 14px; color: #6b7280;">Percentage</div>
                                </div>
                                <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 2px solid #e5e7eb;">
                                    <div style="font-size: 28px; color: #8b5cf6; font-weight: bold; margin-bottom: 5px;">${timeTaken}</div>
                                    <div style="font-size: 14px; color: #6b7280;">Time Taken</div>
                                </div>
                                <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 2px solid #e5e7eb;">
                                    <div style="font-size: 28px; color: #f59e0b; font-weight: bold; margin-bottom: 5px;">${this.getPerformanceRating(this.getSafePercentage(this.selectedResult))}</div>
                                    <div style="font-size: 14px; color: #6b7280;">Performance Rating</div>
                                </div>
                            </div>
                            
                            <!-- Detailed Statistics -->
                            <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; border: 1px solid #bae6fd;">
                                <h3 style="color: #0369a1; font-size: 18px; margin-top: 0;">Detailed Statistics</h3>
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; text-align: center;">
                                    <div>
                                        <div style="font-size: 24px; color: #10b981; font-weight: bold;">${correctCount}</div>
                                        <div style="font-size: 12px; color: #6b7280;">Correct Answers</div>
                                        <div style="font-size: 10px; color: #10b981;">${((correctCount/totalQuestions)*100).toFixed(1)}%</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 24px; color: #ef4444; font-weight: bold;">${wrongCount}</div>
                                        <div style="font-size: 12px; color: #6b7280;">Wrong Answers</div>
                                        <div style="font-size: 10px; color: #ef4444;">${((wrongCount/totalQuestions)*100).toFixed(1)}%</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 24px; color: #3b82f6; font-weight: bold;">${skippedCount}</div>
                                        <div style="font-size: 12px; color: #6b7280;">Skipped Questions</div>
                                        <div style="font-size: 10px; color: #3b82f6;">${((skippedCount/totalQuestions)*100).toFixed(1)}%</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 24px; color: #8b5cf6; font-weight: bold;">${totalQuestions}</div>
                                        <div style="font-size: 12px; color: #6b7280;">Total Questions</div>
                                        <div style="font-size: 10px; color: #8b5cf6;">100%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Questions Analysis -->
                        <div>
                            <h2 style="color: #1e40af; font-size: 24px; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                                Questions Analysis (${totalQuestions} Questions)
                            </h2>
                            ${questionsHTML}
                        </div>
                        
                        <!-- Footer -->
                        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 12px; color: #9ca3af;">
                            <p>Report generated on ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
                            <p>Quiz Application - Performance Analysis Report</p>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(pdfContainer);
                
                // Generate PDF
                const canvas = await html2canvas(pdfContainer, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                });
                
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });
                
                const imgWidth = 210; // A4 width in mm
                const pageHeight = 297; // A4 height in mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                let heightLeft = imgHeight;
                let position = 0;
                
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
                
                // Add additional pages if content is too long
                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                
                // Clean up
                document.body.removeChild(pdfContainer);
                
                // Save PDF with proper filename
                const fileName = `${participantName.replace(/[^a-z0-9]/gi, '_')}_${quizSetName.replace(/[^a-z0-9]/gi, '_')}.pdf`;
                pdf.save(fileName);
                
                this.showNotification('PDF exported successfully!', 'success');
                
            } catch (error) {
                console.error('PDF export error:', error);
                this.showNotification('Failed to generate PDF. Please try again.', 'error');
            }
        },

        exportResultAsJSON() {
            if (!this.selectedResult) return;
            
            const data = {
                result: this.selectedResult,
                performance: {
                    score: this.getSafePercentage(this.selectedResult),
                    time: this.formatTime(this.selectedResult.time_taken || 0),
                    date: this.formatDate(this.selectedResult.created_at)
                },
                questions: Object.keys(this.getAllQuestions()).map(index => ({
                    question: this.getQuestionText(index),
                    options: this.getQuestionOptions(index),
                    correctAnswer: this.getCorrectAnswer(index),
                    userAnswer: this.getAllQuestions()[index],
                    status: this.getQuestionStatus(index),
                    explanation: this.getQuestionExplanation(index)
                }))
            };
            
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `quiz-result-${this.selectedResult.id}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            this.showNotification('Result exported as JSON!', 'success');
        },

        highlightRow(index) {
            // Add visual feedback for row hover
            const rows = document.querySelectorAll('.table-row');
            if (rows[index]) {
                rows[index].classList.add('bg-blue-50', 'dark:bg-blue-900/20');
                setTimeout(() => {
                    rows[index].classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                }, 500);
            }
        }
    }
}
</script>

<style scoped>
/* Enhanced theme variables with typography */
:root {
    /* Light Theme Colors */
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
    
    /* Question specific colors for light mode */
    --question-text: #1f2937;
    --option-text: #374151;
    --answer-text: #4b5563;
    --bg-card: #ffffff;
    --bg-card-alt: #f9fafb;
    
    /* Light Theme Text Colors */
    --text-primary: #111827;
    --text-secondary: #4b5563;
    --text-tertiary: #6b7280;
    --text-accent: #1e40af;
    --text-error: #dc2626;
    --text-success: #059669;
    --text-warning: #d97706;
    --text-info: #2563eb;
    
    /* Light Theme Fonts */
    --font-heading: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --font-mono: 'JetBrains Mono', 'Cascadia Code', 'Fira Code', monospace;
    
    /* Animation Colors */
    --glow-blue: rgba(59, 130, 246, 0.5);
    --glow-green: rgba(34, 197, 94, 0.5);
    --glow-purple: rgba(168, 85, 247, 0.5);
    --glow-red: rgba(239, 68, 68, 0.5);
    --glow-yellow: rgba(245, 158, 11, 0.5);
    --cyber-cyan: #00ffff;
    --cyber-pink: #ff00ff;
    --cyber-green: #00ff00;
    --cyber-blue: #0066ff;
    --cyber-purple: #9900ff;
}

.dark-theme {
    /* Dark Theme Colors */
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
    
    /* Question specific colors for dark mode - FIXED FOR VISIBILITY */
    --question-text: #f3f4f6; /* Light gray for question text */
    --option-text: #e5e7eb; /* Lighter gray for options */
    --answer-text: #d1d5db; /* Even lighter for answers */
    --bg-card: #1f2937; /* Dark background for cards */
    --bg-card-alt: #374151; /* Slightly lighter for alternatives */
    
    /* Dark Theme Text Colors */
    --text-primary: #f3f4f6;
    --text-secondary: #d1d5db;
    --text-tertiary: #9ca3af;
    --text-accent: #60a5fa;
    --text-error: #fca5a5;
    --text-success: #6ee7b7;
    --text-warning: #fcd34d;
    --text-info: #93c5fd;
    
    /* Dark Theme Fonts - more contrast */
    --font-heading: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    --font-mono: 'JetBrains Mono', 'Cascadia Code', 'Fira Code', monospace;
}

/* Base Typography System */
.flex-1 {
    font-family: var(--font-body);
    font-size: 16px;
    line-height: 1.5;
    color: var(--text-primary);
    min-height: 100vh;
    position: relative;
    background: var(--records-bg);
    transition: background 0.3s ease;
}

/* Animated Background */
.animated-bg-elements {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
}

.floating-particle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.3;
    filter: blur(1px);
    animation: float 15s infinite ease-in-out;
}

.circuit-board-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(to right, var(--glow-blue) 1px, transparent 1px),
        linear-gradient(to bottom, var(--glow-blue) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: 0.05;
}

/* Easter Egg Trigger */
.records-easter-egg-trigger {
    position: fixed;
    top: 80px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
    transition: all 0.3s ease;
    animation: pulse-glow 2s infinite;
}

.records-easter-egg-trigger:hover {
    transform: scale(1.1) rotate(15deg);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
}

.records-easter-egg-trigger.analytics-mode-active {
    background: linear-gradient(135deg, var(--cyber-cyan) 0%, #008888 100%);
    animation: spin 2s linear infinite, pulse-glow 1s infinite;
}

.analytics-tooltip {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 10px;
    background: #1f2937;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.analytics-tooltip::before {
    content: '';
    position: absolute;
    bottom: 100%;
    right: 15px;
    border-width: 0 6px 6px 6px;
    border-style: solid;
    border-color: transparent transparent #1f2937 transparent;
}

/* Analytics Hint */
.analytics-hint {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: var(--cyber-cyan);
    padding: 8px 16px;
    border-radius: 20px;
    font-family: monospace;
    font-size: 12px;
    z-index: 1000;
    opacity: 0.7;
    backdrop-filter: blur(10px);
}

/* Header Styling */
.text-gradient {
    background: linear-gradient(45deg, var(--glow-blue), var(--glow-green), var(--glow-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
}

.sparkle {
    position: absolute;
    top: -10px;
    right: -30px;
    animation: sparkle 2s infinite;
    font-size: 1.5rem;
}

/* Error State */
.error-state-container {
    border-radius: 0.75rem;
    padding: 1rem;
    position: relative;
    overflow: hidden;
    border: 2px solid var(--error-border);
    background: var(--error-bg);
}

.error-icon {
    font-size: 1.5rem;
    animation: bounce-slow 2s infinite;
}

.retry-btn {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, var(--glow-red), #dc2626);
    color: white;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.retry-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.error-data-stream {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--cyber-red), transparent);
    animation: cyber-scan 2s linear infinite;
}

/* Filters Card */
.filters-card {
    background: var(--filters-card-bg);
    border: 2px solid var(--filters-card-border);
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
}

.filters-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: rgba(59, 130, 246, 0.2);
}

.filter-icon {
    animation: bounce-slow 2s infinite;
}

.filter-sparkle {
    position: absolute;
    top: -5px;
    right: -20px;
    width: 10px;
    height: 10px;
    background: var(--cyber-cyan);
    border-radius: 50%;
    animation: sparkle 3s infinite;
}

/* Filters Grid */
.filters-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 768px) {
    .filters-grid {
        grid-template-columns: repeat(3, 1fr) auto;
        align-items: end;
    }
}

.filter-item {
    width: 100%;
}

.label-icon {
    animation: pulse-slow 2s infinite;
}

/* Input Wrappers */
.input-wrapper, .select-wrapper, .date-wrapper {
    position: relative;
}

.filter-input, .filter-select, .date-input {
    width: 100%;
    padding: 0.75rem 1rem;
    background: var(--input-bg);
    border: 2px solid var(--input-border);
    border-radius: 0.5rem;
    color: var(--input-text);
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.filter-input:focus, .filter-select:focus, .date-input:focus {
    outline: none;
    border-color: var(--glow-blue);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-underline {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--cyber-cyan), var(--cyber-blue));
    transition: width 0.3s ease;
}

.filter-input:focus + .input-underline {
    width: 100%;
}

.select-glow {
    position: absolute;
    inset: -2px;
    border-radius: 0.5rem;
    background: linear-gradient(45deg, transparent, var(--glow-purple), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.filter-select:focus + .select-glow {
    opacity: 0.3;
}

/* Filter Buttons */
.filter-buttons {
    display: flex;
    gap: 0.75rem;
    flex-direction: column;
}

@media (min-width: 768px) {
    .filter-buttons {
        flex-direction: row;
        align-items: end;
    }
}

.filter-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
}

.clear-btn {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
}

.clear-btn:hover {
    background: linear-gradient(135deg, #4b5563, #374151);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.apply-btn {
    background: linear-gradient(135deg, var(--glow-green), #059669);
    color: white;
}

.apply-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-icon {
    font-size: 1.25rem;
}

/* Browser Filter Toggle */
.browser-filter-toggle {
    position: relative;
}

.toggle-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.toggle-label:hover {
    background: var(--table-row-hover);
}

.toggle-checkbox {
    display: none;
}

.toggle-slider {
    width: 44px;
    height: 24px;
    background: var(--input-border);
    border-radius: 12px;
    position: relative;
    transition: all 0.3s ease;
}

.toggle-slider::before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: all 0.3s ease;
}

.toggle-checkbox:checked + .toggle-slider {
    background: var(--glow-blue);
}

.toggle-checkbox:checked + .toggle-slider::before {
    transform: translateX(20px);
}

.toggle-icon {
    animation: pulse-slow 2s infinite;
}

.toggle-neural-node {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background: var(--cyber-blue);
    border-radius: 50%;
    animation: node-pulse 3s infinite;
}

/* All Browsers Warning */
.all-browsers-warning {
    animation: slide-right 0.3s ease-out;
}

.warning-icon {
    animation: pulse-slow 2s infinite;
}

.warning-title {
    font-family: var(--font-heading);
    font-size: 1rem;
    font-weight: 600;
}

.warning-text {
    font-family: var(--font-body);
    font-size: 0.875rem;
}

/* Summary Card */
.summary-card {
    background: var(--summary-card-bg);
    border: 2px solid var(--summary-card-border);
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
}

.summary-card:hover {
    border-color: rgba(59, 130, 246, 0.2);
}

.summary-status {
    animation: fade-in 0.5s ease-out;
}

.status-badge {
    font-family: var(--font-body);
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    animation: badge-pulse 3s ease-in-out infinite;
}

@keyframes badge-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.9;
    }
}

.summary-icon {
    animation: bounce-slow 2s infinite;
}

.summary-sparkle {
    position: absolute;
    top: -5px;
    right: -20px;
    width: 10px;
    height: 10px;
    background: var(--cyber-green);
    border-radius: 50%;
    animation: sparkle 3s infinite;
}

/* Summary Items */
.summary-item {
    text-align: center;
    padding: 1rem;
    background: var(--summary-item-bg);
    border: 1px solid var(--summary-item-border);
    border-radius: 0.75rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.summary-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.summary-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1), transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.summary-item:hover .summary-glow {
    opacity: 1;
}

/* Table Card */
.table-card {
    background: var(--table-card-bg);
    border: 2px solid var(--table-card-border);
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
}

.table-header-container {
    position: relative;
}

/* Table Styling */
.table-fixed {
    table-layout: fixed;
}

.table-header {
    background: var(--table-header-bg);
}

.table-header th {
    padding: 0.75rem 1rem;
    border-bottom: 2px solid var(--table-border);
}

.table-body {
    background: var(--table-body-bg);
}

.table-body td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--table-border);
}

/* Align top for all cells */
.align-top {
    vertical-align: top;
}

/* Table Row Hover Effect */
.table-row:hover {
    background: var(--table-row-hover);
}

/* Browser info styling */
.browser-info {
    font-family: var(--font-body);
    font-size: 0.75rem;
    color: var(--text-tertiary);
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.browser-icon {
    margin-right: 0.25rem;
}

.browser-id {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    color: var(--text-tertiary);
}

.your-attempt-badge {
    font-family: var(--font-body);
    font-size: 0.7rem;
    font-weight: 500;
    letter-spacing: 0.025em;
    animation: badge-pulse 3s ease-in-out infinite;
}

/* Empty State */
.empty-state {
    position: relative;
}

.empty-icon {
    animation: float 6s ease-in-out infinite;
}

.empty-actions {
    display: flex;
    gap: 1rem;
}

.empty-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Loading State */
.loading-state {
    position: relative;
}

.loading-spinner-container {
    position: relative;
    display: inline-block;
    width: 64px;
    height: 64px;
}

.loading-spinner {
    width: 64px;
    height: 64px;
    border: 4px solid var(--input-border);
    border-top-color: var(--glow-blue);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-dots::after {
    content: '...';
    animation: dots 1.5s steps(3, end) infinite;
}

/* Pagination */
.pagination-container {
    background: var(--table-header-bg);
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-btn {
    padding: 0.5rem 1rem;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    color: var(--input-text);
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.pagination-btn:hover:not(.disabled) {
    background: var(--glow-blue);
    color: white;
    border-color: var(--glow-blue);
}

.pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-numbers {
    display: flex;
    gap: 0.25rem;
}

.page-number {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    color: var(--input-text);
    border-radius: 0.25rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.page-number:hover {
    background: var(--table-row-hover);
}

.page-number.active {
    background: var(--glow-blue);
    color: white;
    border-color: var(--glow-blue);
}

/* Analytics Terminal */
.analytics-terminal {
    position: fixed;
    bottom: 30px;
    left: 30px;
    width: 400px;
    max-width: 90%;
    border-radius: 10px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    z-index: 1001;
    overflow: hidden;
    border: 1px solid var(--cyber-green);
}

.terminal-header {
    background: #2d2d2d;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #444;
}

.terminal-dots {
    display: flex;
    gap: 8px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.dot.red { background: #ff5f56; }
.dot.yellow { background: #ffbd2e; }
.dot.green { background: #27ca3f; }

.terminal-close {
    color: #888;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    line-height: 1;
    transition: color 0.2s;
}

.terminal-body {
    padding: 20px;
    max-height: 300px;
    overflow-y: auto;
}

.terminal-line {
    margin-bottom: 10px;
    line-height: 1.5;
}

/* Modal Overlay */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1002;
    padding: 1rem;
}

.modal-content {
    background: var(--modal-card-bg);
    border: 2px solid var(--modal-card-border);
    border-radius: 1rem;
    width: 100%;
    max-width: 6xl;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    animation: modal-slide 0.3s ease-out;
}

@keyframes modal-slide {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Modal Header Sticky */
.modal-header.sticky {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.modal-icon {
    animation: bounce-slow 2s infinite;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--table-header-text);
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-close:hover {
    color: var(--glow-red);
    transform: rotate(90deg);
}

/* Modal Summary Grid */
.modal-summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (min-width: 768px) {
    .modal-summary-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.modal-summary-item {
    text-align: center;
    padding: 1rem;
    border-radius: 0.75rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.modal-summary-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.modal-summary-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1), transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal-summary-item:hover .modal-summary-glow {
    opacity: 1;
}

/* Modal Performance */
.modal-performance-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 768px) {
    .modal-performance-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.modal-performance-item {
    padding: 1rem;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.modal-performance-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.performance-bar {
    position: relative;
    overflow: hidden;
}

.performance-bar div {
    transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Questions Section */
.questions-section {
    margin-bottom: 2rem;
}

.questions-title {
    margin-bottom: 1.5rem;
}

/* Questions Container */
.questions-container {
    background: var(--filters-card-bg);
    border: 2px solid var(--filters-card-border);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    border-radius: 1rem;
}

.questions-container:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.questions-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .questions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Question Cards */
.question-card {
    background: var(--bg-card) !important;
    border-color: var(--table-border) !important;
    transition: all 0.3s ease;
    min-height: 200px;
}

.question-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.question-card.correct {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), var(--bg-card)) !important;
    border-color: rgba(34, 197, 94, 0.7) !important;
}

.question-card.wrong {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), var(--bg-card)) !important;
    border-color: rgba(239, 68, 68, 0.7) !important;
}

.question-card.skipped {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), var(--bg-card)) !important;
    border-color: rgba(59, 130, 246, 0.7) !important;
}

.question-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.question-number {
    flex-shrink: 0;
}

.question-title {
    font-size: 1rem;
    font-weight: 600;
}

.question-text {
    font-size: 0.875rem;
    line-height: 1.5;
    margin-top: 0.25rem;
    color: var(--question-text) !important;
}

.question-status-badge {
    flex-shrink: 0;
    animation: fadeIn 0.5s ease-out;
}

/* Options with hover effects */
.question-options {
    margin-bottom: 1rem;
}

.question-option {
    background: var(--bg-card-alt) !important;
    border-color: var(--table-border) !important;
    color: var(--option-text) !important;
    cursor: default;
    transition: all 0.2s ease;
}

.question-option:hover {
    transform: translateX(4px);
}

.question-option.correct {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), var(--bg-card-alt)) !important;
    border-color: #10b981 !important;
    color: #d1fae5 !important;
}

.question-option.correct-unselected {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), var(--bg-card-alt)) !important;
    border-color: rgba(34, 197, 94, 0.5) !important;
    color: #a7f3d0 !important;
}

.question-option.wrong {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), var(--bg-card-alt)) !important;
    border-color: #ef4444 !important;
    color: #fee2e2 !important;
}

.question-option.correct-skipped {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), var(--bg-card-alt)) !important;
    border-color: rgba(59, 130, 246, 0.5) !important;
    color: #bfdbfe !important;
}

.question-option.normal {
    background: var(--bg-card-alt) !important;
    border-color: var(--table-border) !important;
    color: var(--option-text) !important;
}

.option-letter {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.5rem;
    height: 1.5rem;
    font-size: 0.75rem;
    border-radius: 0.25rem;
}

.option-text {
    color: var(--option-text) !important;
    font-family: var(--font-body);
}

.option-markers {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Skipped Questions Info */
.skipped-info {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
}

/* Explanation Box */
.explanation {
    margin-top: 1rem;
}

.explanation-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.explanation-text {
    font-size: 0.75rem;
    line-height: 1.4;
    color: var(--answer-text) !important;
}

/* User Answer Status */
.user-answer-status {
    margin-top: 1rem;
}

.status-icon {
    flex-shrink: 0;
    font-size: 1.25rem;
}

.status-text {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--answer-text) !important;
}

.status-detail {
    font-size: 0.75rem;
    color: var(--answer-text) !important;
}

/* Statistics Summary */
.statistics-summary {
    margin-bottom: 2rem;
}

.statistics-title {
    margin-bottom: 1.5rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

@media (min-width: 768px) {
    .stat-value {
        font-size: 2rem;
    }
}

.stat-label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.stat-percentage {
    font-size: 0.75rem;
}

/* No Details State */
.modal-no-details {
    text-align: center;
    padding: 3rem 1rem;
}

.no-details-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    animation: float 4s ease-in-out infinite;
}

.no-details-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.no-details-text {
    font-size: 0.875rem;
}

/* Modal Footer */
.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--table-border);
    position: sticky;
    bottom: 0;
    background: var(--modal-card-bg);
}

.footer-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.modal-close-btn, .modal-export-btn, .nav-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.modal-close-btn {
    background: var(--input-bg);
    color: var(--input-text);
    border: 1px solid var(--input-border);
}

.modal-close-btn:hover {
    background: var(--table-row-hover);
}

.modal-export-btn, .nav-btn {
    color: white;
    position: relative;
    overflow: hidden;
}

.modal-export-btn::after, .nav-btn::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    transform: rotate(30deg);
    transition: all 0.6s;
    opacity: 0;
}

.modal-export-btn:hover::after, .nav-btn:hover::after {
    opacity: 1;
    transform: rotate(30deg) translate(50%, 50%);
}

.modal-export-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.nav-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

/* Particle Container */
.particle-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.burst-particle {
    position: absolute;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    animation: particle-burst 0.8s ease-out forwards;
    pointer-events: none;
}

/* Matrix Rain */
.matrix-rain {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.5s;
    font-family: var(--font-mono);
    color: var(--cyber-green);
    font-size: 14px;
    overflow: hidden;
}

.matrix-rain.active {
    opacity: 0.1;
}

.matrix-rain::before {
    content: '01010101 10101010 00110011 11001100 01010101 10101010 00110011 11001100';
    position: absolute;
    width: 100%;
    animation: matrix-fall 20s linear infinite;
    white-space: pre;
    line-height: 1.5;
}

/* Animation Classes */
.animate-slide-up {
    animation: slide-up 0.6s ease-out forwards;
}

.animate-slide-right {
    animation: slide-right 0.5s ease-out forwards;
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out forwards;
}

.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 2s ease-in-out infinite;
}

.animate-ping-slow {
    animation: ping-slow 2s ease-out infinite;
}

.animate-tada {
    animation: tada 1s ease-out;
}

.animate-gradient-shift {
    background-size: 200% 200%;
    animation: gradient-shift 3s ease infinite;
}

.animate-count-up {
    animation: count-up 1s ease-out forwards;
}

@keyframes count-up {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-spin-slow {
    animation: spin 3s linear infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

/* Cyber Glow Effects */
.cyber-glow-blue {
    text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
}

.cyber-glow-green {
    text-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
}

.cyber-glow-purple {
    text-shadow: 0 0 10px rgba(168, 85, 247, 0.5);
}

.cyber-glow-red {
    text-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
}

.cyber-glow-yellow {
    text-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
}

/* Interactive Card */
.interactive-card {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.interactive-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.7s ease;
}

.interactive-card:hover::before {
    left: 100%;
}

/* CRT Scanlines */
.crt-scanlines {
    position: relative;
}

.crt-scanlines::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        transparent 50%,
        rgba(0, 0, 0, 0.1) 50%
    );
    background-size: 100% 4px;
    pointer-events: none;
    z-index: 1;
}

/* Cyber Scan */
.cyber-scan {
    position: relative;
    overflow: hidden;
}

.cyber-scan::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, 
        transparent, 
        var(--cyber-green),
        transparent
    );
    animation: cyber-scan 2s linear infinite;
}

/* Data Stream */
.data-stream {
    position: relative;
    overflow: hidden;
}

.data-stream::after {
    content: '';
    position: absolute;
    top: -100%;
    left: 0;
    width: 2px;
    height: 100%;
    background: linear-gradient(to bottom, transparent, var(--cyber-cyan), transparent);
    animation: data-stream 3s linear infinite;
}

/* Holographic */
.holographic {
    background: linear-gradient(
        45deg,
        rgba(255, 255, 255, 0.1) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, 0.1) 50%,
        rgba(255, 255, 255, 0.1) 75%,
        transparent 75%,
        transparent
    );
    background-size: 50px 50px;
    animation: hologram-move 20s linear infinite;
}

/* Neural Node */
.neural-node {
    position: absolute;
    width: 6px;
    height: 6px;
    background: var(--cyber-blue);
    border-radius: 50%;
    animation: node-pulse 2s infinite;
    z-index: 0;
}

/* Transition Effects */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-down-enter,
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-up-enter,
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(20px);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}

.terminal-enter-active,
.terminal-leave-active {
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.terminal-enter,
.terminal-leave-to {
    opacity: 0;
    transform: translateX(-30px) scale(0.9);
}

.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter,
.modal-leave-to {
    opacity: 0;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .analytics-terminal {
        width: calc(100% - 40px);
        left: 20px;
        bottom: 20px;
    }
    
    .records-easter-egg-trigger {
        width: 40px;
        height: 40px;
        top: 70px;
        right: 10px;
    }
    
    .analytics-hint {
        font-size: 10px;
        padding: 6px 12px;
        bottom: 10px;
    }
    
    .questions-grid {
        grid-template-columns: 1fr;
    }
    
    .statistics-summary .grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .footer-actions {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .footer-actions button {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 640px) {
    .modal-summary-grid {
        grid-template-columns: 1fr;
    }
    
    .statistics-summary .grid {
        grid-template-columns: 1fr;
    }
    
    .modal-title {
        font-size: 1.25rem;
    }
    
    .questions-title {
        font-size: 1rem;
    }
    
    .question-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .question-status-badge {
        margin-top: 0.5rem;
    }
    
    .question-option {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .option-markers {
        margin-top: 0.5rem;
        width: 100%;
    }
}

/* Smooth scroll for modal */
.modal-content {
    scroll-behavior: smooth;
}

/* Custom scrollbar for modal */
.modal-content::-webkit-scrollbar {
    width: 8px;
}

.modal-content::-webkit-scrollbar-track {
    background: transparent;
}

.modal-content::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 4px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.5);
}

.dark-theme .modal-content::-webkit-scrollbar-thumb {
    background: rgba(75, 85, 99, 0.5);
}

.dark-theme .modal-content::-webkit-scrollbar-thumb:hover {
    background: rgba(75, 85, 99, 0.7);
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .question-card,
    .question-option,
    .modal-summary-item,
    .stat-item,
    .modal-export-btn,
    .nav-btn {
        transition: none;
    }
    
    .performance-bar div {
        transition: none;
    }
}

/* Animation keyframes */
@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
    }
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 20px var(--glow-blue);
    }
    50% {
        box-shadow: 0 0 30px var(--glow-purple);
    }
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes sparkle {
    0%, 100% {
        opacity: 0.3;
        transform: scale(0.8);
    }
    50% {
        opacity: 1;
        transform: scale(1.2);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-right {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes pulse-slow {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

@keyframes ping-slow {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

@keyframes tada {
    0% {
        transform: scale(1);
    }
    10%, 20% {
        transform: scale(0.9) rotate(-3deg);
    }
    30%, 50%, 70%, 90% {
        transform: scale(1.1) rotate(3deg);
    }
    40%, 60%, 80% {
        transform: scale(1.1) rotate(-3deg);
    }
    100% {
        transform: scale(1) rotate(0);
    }
}

@keyframes hologram-move {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 50px 50px;
    }
}

@keyframes dots {
    0%, 20% {
        content: '.';
    }
    40% {
        content: '..';
    }
    60% {
        content: '...';
    }
    80%, 100% {
        content: '';
    }
}

@keyframes data-stream {
    0% {
        top: -100%;
    }
    100% {
        top: 100%;
    }
}

@keyframes particle-burst {
    0% {
        transform: translate(0, 0) scale(1);
        opacity: 1;
    }
    100% {
        transform: translate(var(--tx), var(--ty)) scale(0);
        opacity: 0;
    }
}

@keyframes node-pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 var(--glow-blue);
    }
    50% {
        transform: scale(1.2);
        box-shadow: 0 0 0 10px transparent;
    }
}

@keyframes matrix-fall {
    0% {
        transform: translateY(-100%);
    }
    100% {
        transform: translateY(100vh);
    }
}

@keyframes cyber-scan {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

@keyframes gradient-shift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

/* Ensure proper stacking */
.container {
    position: relative;
    z-index: 10;
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--input-bg);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--input-border);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--glow-blue);
}

/* Print Styles */
@media print {
    .records-easter-egg-trigger,
    .analytics-hint,
    .analytics-terminal,
    .matrix-rain,
    .animated-bg-elements,
    .particle-container {
        display: none !important;
    }
    
    * {
        animation: none !important;
        transition: none !important;
    }
}

/* Headings */
h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-heading);
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.2;
}

.records-title {
    font-family: var(--font-heading);
    font-weight: 700;
    color: var(--header-title);
    position: relative;
    display: inline-block;
}

.records-subtitle {
    font-family: var(--font-body);
    font-size: 1.125rem;
    color: var(--text-secondary);
    line-height: 1.6;
}

.filters-title {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.summary-title {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
}

.modal-title {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Text Elements */
.filter-label {
    font-family: var(--font-body);
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.summary-label {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-secondary);
    position: relative;
    z-index: 1;
}

.summary-number {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-accent);
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 1;
}

@media (min-width: 640px) {
    .summary-number {
        font-size: 1.875rem;
    }
}

/* Table Typography */
.table-header th {
    font-family: var(--font-body);
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table-body td {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-primary);
}

.participant-name {
    font-family: var(--font-body);
    font-weight: 500;
    color: var(--text-primary);
}

.quiz-set-name {
    font-family: var(--font-body);
    color: var(--text-primary);
}

.score-value {
    font-family: var(--font-body);
    color: var(--text-primary);
}

.percentage-value {
    font-family: var(--font-heading);
    font-weight: 600;
}

.time-value {
    font-family: var(--font-body);
    color: var(--text-primary);
}

.date-value {
    font-family: var(--font-body);
    color: var(--text-primary);
}

.browser-info {
    font-family: var(--font-body);
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.performance-rating {
    font-family: var(--font-body);
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.time-indicator span {
    font-family: var(--font-body);
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.date-relative {
    font-family: var(--font-body);
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

/* Button Text */
.filter-btn {
    font-family: var(--font-body);
    font-weight: 500;
    letter-spacing: 0.025em;
}

.btn-text {
    font-family: var(--font-body);
}

.toggle-text {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle-count {
    font-weight: 500;
    color: var(--text-accent);
}

/* Error States */
.error-text {
    font-family: var(--font-body);
    font-weight: 500;
    color: var(--text-error);
}

.retry-btn {
    font-family: var(--font-body);
    font-weight: 500;
}

/* Empty States */
.empty-title {
    font-family: var(--font-heading);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.empty-subtitle {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.empty-btn {
    font-family: var(--font-body);
    font-weight: 500;
}

/* Loading States */
.loading-text {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-secondary);
}

/* Pagination */
.pagination-info {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.pagination-btn {
    font-family: var(--font-body);
    font-size: 0.875rem;
    font-weight: 500;
}

.page-number {
    font-family: var(--font-body);
    font-size: 0.875rem;
    font-weight: 500;
}

/* Modal Typography */
.modal-summary-value {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    font-weight: 700;
}

.modal-summary-label {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.modal-performance-title {
    font-family: var(--font-heading);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.performance-rating-text {
    font-family: var(--font-body);
    font-size: 0.875rem;
    font-weight: 500;
}

.modal-stats-title {
    font-family: var(--font-heading);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.stat-value {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    font-weight: 700;
}

.stat-label {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.question-title {
    font-family: var(--font-heading);
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.question-text {
    font-family: var(--font-body);
    color: var(--question-text);
    line-height: 1.5;
}

.option-text {
    font-family: var(--font-body);
    color: var(--option-text);
}

.option-marker {
    font-family: var(--font-body);
    font-size: 0.75rem;
}

.question-skipped {
    font-family: var(--font-body);
    font-size: 0.875rem;
    color: var(--text-info);
}

.modal-no-details {
    font-family: var(--font-body);
    font-size: 1rem;
    color: var(--text-secondary);
}

.modal-close-btn, .modal-export-btn {
    font-family: var(--font-body);
    font-weight: 500;
}

/* Analytics Terminal */
.analytics-terminal {
    font-family: var(--font-mono);
    background: linear-gradient(135deg, rgba(26, 26, 26, 0.95), rgba(31, 41, 55, 0.95));
    backdrop-filter: blur(10px);
}

.terminal-title {
    font-family: var(--font-mono);
    font-size: 0.875rem;
    color: #888;
}

.terminal-body {
    font-family: var(--font-mono);
    font-size: 0.875rem;
    line-height: 1.5;
}

.prompt {
    font-family: var(--font-mono);
    font-weight: bold;
    color: var(--cyber-green);
}

/* Easter Egg Tooltip */
.analytics-tooltip {
    font-family: var(--font-body);
    font-size: 0.75rem;
    color: white;
}

/* Analytics Hint */
.analytics-hint {
    font-family: var(--font-mono);
    font-size: 0.75rem;
    color: var(--cyber-cyan);
}

/* Performance Rating Colors */
.text-green-600.dark-theme {
    color: #6ee7b7;
}

.text-yellow-600.dark-theme {
    color: #fcd34d;
}

.text-red-600.dark-theme {
    color: #fca5a5;
}

.text-blue-600.dark-theme {
    color: #93c5fd;
}

.text-purple-600.dark-theme {
    color: #c4b5fd;
}

/* Light theme specific overrides */
.light-theme {
    /* Enhance contrast for light theme */
    .records-title {
        color: #1e3a8a; /* Darker blue for better contrast */
    }
    
    .filter-label {
        color: #374151; /* Darker gray */
    }
    
    .table-header th {
        color: #4b5563; /* Darker gray */
    }
    
    .summary-label {
        color: #4b5563;
    }
    
    .browser-info,
    .performance-rating,
    .time-indicator span,
    .date-relative {
        color: #6b7280;
    }
    
    .toggle-text {
        color: #4b5563;
    }
    
    /* Questions section specific */
    .questions-container {
        background: #ffffff !important;
        border: 2px solid #e5e7eb !important;
    }
    
    .question-card {
        background: #ffffff !important;
    }
}

/* Dark theme specific overrides */
.dark-theme {
    /* Enhance legibility for dark theme */
    .records-title {
        color: #93c5fd; /* Lighter blue for better visibility */
    }
    
    .filter-label {
        color: #d1d5db;
    }
    
    .table-header th {
        color: #d1d5db; /* Lighter gray */
    }
    
    .summary-label {
        color: #d1d5db;
    }
    
    .browser-info,
    .performance-rating,
    .time-indicator span,
    .date-relative {
        color: #9ca3af;
    }
    
    .toggle-text {
        color: #d1d5db;
    }
    
    /* Ensure sufficient contrast in dark mode */
    .participant-name,
    .quiz-set-name,
    .score-value,
    .time-value,
    .date-value {
        color: #f3f4f6; /* Pure white for primary text */
    }
    
    .summary-number {
        text-shadow: 0 2px 10px rgba(147, 197, 253, 0.3);
    }
    
    /* Question text should be clearly visible */
    .question-text {
        color: #f3f4f6 !important;
    }
    
    .option-text {
        color: #e5e7eb !important;
    }
    
    .status-text,
    .status-detail,
    .explanation-text {
        color: #d1d5db !important;
    }
    
    /* Questions section specific */
    .questions-container {
        background: rgba(31, 41, 55, 0.8) !important;
        border: 2px solid #374151 !important;
        backdrop-filter: blur(10px);
    }
    
    .question-card {
        background: rgba(55, 65, 81, 0.5) !important;
    }
}

/* Font loading fallbacks */
@supports (font-variation-settings: normal) {
    :root {
        --font-heading: 'Inter var', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        --font-body: 'Inter var', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
}

/* Export dropdown styles */
.relative.group:hover .absolute {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.absolute {
    transform: translateY(10px);
}

.group:hover .absolute {
    transform: translateY(0);
}
</style>