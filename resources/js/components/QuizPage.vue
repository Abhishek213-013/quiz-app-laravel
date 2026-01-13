<template>
    <div class="flex-1 overflow-hidden relative">
        <!-- Animated Background Elements -->
        <div class="animated-bg-elements">
            <div class="floating-particle" v-for="(particle, index) in particles" :key="index"
                 :style="particle.style"></div>
            <div class="circuit-board-bg"></div>
        </div>

        <!-- Easter Egg Trigger -->
        <div class="easter-egg-trigger" @click="toggleNerdMode"
             @mouseenter="showEasterTooltip = true"
             @mouseleave="showEasterTooltip = false"
             :class="{ 'nerd-mode-active': nerdMode }">
            <span class="text-2xl">🧠</span>
            <div v-if="showEasterTooltip && !nerdMode" class="easter-tooltip">
                Click for NERD MODE
            </div>
        </div>

        <!-- Konami Code Progress -->
        <div v-if="konamiProgress > 0" class="konami-hint">
            Konami Code: {{ konamiSequence.slice(0, konamiProgress).join(' ') }}
        </div>

        <div class="min-h-screen" :class="themeClass">
            <!-- Enhanced Sticky Header -->
            <div class="sticky top-0 z-50 header cyber-scan" :class="themeClass">
                <div class="container mx-auto px-4 py-4 max-w-4xl">
                    <div class="flex items-center justify-between">
                        <!-- Question Type Indicator -->
                        <div class="text-left">
                            <div class="text-sm font-medium header-subtitle ">Question Type</div>
                            <div class="text-lg font-bold header-title gradient-text">{{ getCurrentQuestionTypeLabel() }}</div>
                        </div>
                        
                        <!-- Timer with Enhanced Effects -->
                        <div class="text-center">
                            <div class="text-lg font-semibold header-subtitle ">Time Remaining</div>
                            <div class="text-2xl font-bold cyber-glow" :class="timerColor">{{ formatTime(timeLeft) }}</div>
                            <div class="countdown-pulse" :class="{'countdown-warning': timeLeft <= 10}"></div>
                        </div>

                        <!-- Progress with Animation -->
                        <div class="text-right">
                            <div class="text-lg font-semibold header-subtitle ">Progress</div>
                            <div class="text-xl font-bold progress-count animate-count-up">
                                {{ currentQuestionIndex + 1 }} / {{ quizzes.length }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-3 relative">
                        <div class="progress-bar h-2 rounded-full overflow-hidden">
                            <div class="progress-shimmer h-full" 
                                 :style="{width: `${((currentQuestionIndex + 1) / quizzes.length) * 100}%`}">
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-center text-gray-500">
                            {{ Math.round(((currentQuestionIndex + 1) / quizzes.length) * 100) }}% Complete
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container mx-auto px-4 py-8 max-w-4xl relative z-10">
                <!-- Quiz Content -->
                <div v-if="!quizCompleted && quizzes.length > 0" class="quiz-card interactive-card" :class="themeClass">
                    <!-- Question Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 question-number animate-pulse-slow">
                                Q{{ currentQuestionIndex + 1 }}
                            </span>
                            <!-- <span class="px-3 py-1 question-type glitch" :data-text="getCurrentQuestionTypeLabel()">
                                {{ getCurrentQuestionTypeLabel() }}
                            </span> -->
                            <span class="px-3 py-1 question-points">
                                {{ currentQuestion.points }} point{{ currentQuestion.points !== 1 ? 's' : '' }}
                            </span>
                            <!-- Skip Status Badge with Animation -->
                            <transition name="slide-down">
                                <span v-if="answers[currentQuestionIndex] === 'skipped'" 
                                      class="px-3 py-1 skip-badge animate-shake">
                                    ⏭️ Skipped
                                </span>
                            </transition>
                        </div>
                        
                        <!-- Points Indicator -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Points:</span>
                            <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full text-sm font-bold">
                                {{ currentQuestion.points }} 🎯
                            </span>
                        </div>
                    </div>

                    <!-- Question with Enhanced Styling -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white animate-bounce-slow">
                                ?
                            </div>
                            <span class="text-sm font-medium text-gray-500">Question {{ currentQuestionIndex + 1 }}</span>
                        </div>
                        <h2 class="text-2xl font-semibold question-text mb-4 glitch" :data-text="currentQuestion.question">
                            {{ currentQuestion.question }}
                        </h2>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-ping-slow"></span>
                            <span>Answer to proceed →</span>
                        </div>
                    </div>

                    <!-- Multiple Choice Options with Enhanced Effects -->
                    <div v-if="currentQuestion.question_type === 'multiple_choice'" class="space-y-3">
                        <div 
                            v-for="(option, index) in currentQuestion.options" 
                            :key="index"
                            class="p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 interactive-card selection-glow"
                            :class="{
                                'correct-answer': selectedAnswer === option && option === currentQuestion.correct_answer,
                                'incorrect-answer': selectedAnswer === option && option !== currentQuestion.correct_answer && quizCompleted,
                                'bg-gradient-to-r from-blue-50 to-blue-100 border-blue-500': selectedAnswer === option && !quizCompleted,
                                'border-gray-200 hover:border-blue-300': selectedAnswer !== option,
                                'lab-bubbles': selectedAnswer === option
                            }"
                            @click="selectAnswer(option)"
                        >
                            <div class="flex items-center">
                                <div class="w-10 h-10 flex items-center justify-center rounded-full border-2 mr-4 transition-all duration-300"
                                    :class="{
                                        'bg-gradient-to-r from-blue-500 to-purple-500 border-transparent text-white shadow-lg': selectedAnswer === option,
                                        'border-gray-300 text-gray-600': selectedAnswer !== option
                                    }">
                                    <span class="font-bold">{{ String.fromCharCode(65 + index) }}</span>
                                </div>
                                <span class="text-lg flex-1">{{ option }}</span>
                                <div v-if="selectedAnswer === option" class="ml-2">
                                    <span class="text-2xl" v-if="option === currentQuestion.correct_answer && quizCompleted">✅</span>
                                    <span class="text-2xl" v-else-if="quizCompleted">❌</span>
                                    <span class="text-2xl" v-else>👉</span>
                                </div>
                            </div>
                            <div v-if="selectedAnswer === option" class="mt-2 ml-14">
                                <div class="text-sm text-blue-600 animate-fade-in">
                                    Selected ✓
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Brief Answer Input with Enhanced Effects -->
                    <div v-else-if="currentQuestion.question_type === 'brief_answer'" class="space-y-4">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 interactive-card">
                            <label class="block text-sm font-medium text-blue-800 mb-2 flex items-center gap-2">
                                <span class="text-xl">✏️</span>
                                <span>Type your answer below:</span>
                            </label>
                            <div class="relative">
                                <textarea
                                    v-model="briefAnswer"
                                    rows="4"
                                    placeholder="Enter your detailed answer here... (Auto-saves as you type)"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-all duration-300"
                                    :disabled="answers[currentQuestionIndex] === 'skipped'"
                                    @input="selectAnswer(briefAnswer)"
                                    @focus="activateFocusMode"
                                    @blur="deactivateFocusMode"
                                    :class="{'focus-mode-active': isTextAreaFocused}"
                                ></textarea>
                                <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                    {{ briefAnswer.length }}/500
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span class="text-xl">💡</span>
                            <span>Your answer is automatically saved as you type.</span>
                            <span class="ml-auto px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">
                                Brief Answer
                            </span>
                        </div>
                    </div>

                    <!-- True/False Options with Enhanced Effects -->
                    <div v-else-if="currentQuestion.question_type === 'true_false'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div 
                            class="p-6 border-2 rounded-lg cursor-pointer text-center transition-all duration-200 interactive-card selection-glow"
                            :class="{
                                'correct-answer': selectedAnswer === 'True' && currentQuestion.correct_answer === 'True',
                                'incorrect-answer': selectedAnswer === 'True' && currentQuestion.correct_answer !== 'True' && quizCompleted,
                                'bg-gradient-to-r from-green-50 to-emerald-100 border-green-500': selectedAnswer === 'True' && !quizCompleted,
                                'border-gray-200 hover:border-green-300': selectedAnswer !== 'True',
                                'lab-bubbles': selectedAnswer === 'True'
                            }"
                            @click="selectAnswer('True')"
                        >
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 flex items-center justify-center rounded-full border-2 mb-3 transition-all duration-300"
                                    :class="{
                                        'bg-gradient-to-r from-green-500 to-emerald-600 border-transparent text-white shadow-lg': selectedAnswer === 'True',
                                        'border-gray-300': selectedAnswer !== 'True'
                                    }">
                                    <span class="text-2xl">✓</span>
                                </div>
                                <span class="text-xl font-bold text-gray-800">True</span>
                                <span class="text-sm text-gray-500 mt-1">This statement is correct</span>
                                <div v-if="selectedAnswer === 'True'" class="mt-3">
                                    <span class="px-3 py-1 bg-green-500 text-white rounded-full text-sm animate-fade-in">
                                        Selected
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div 
                            class="p-6 border-2 rounded-lg cursor-pointer text-center transition-all duration-200 interactive-card selection-glow"
                            :class="{
                                'correct-answer': selectedAnswer === 'False' && currentQuestion.correct_answer === 'False',
                                'incorrect-answer': selectedAnswer === 'False' && currentQuestion.correct_answer !== 'False' && quizCompleted,
                                'bg-gradient-to-r from-red-50 to-pink-100 border-red-500': selectedAnswer === 'False' && !quizCompleted,
                                'border-gray-200 hover:border-red-300': selectedAnswer !== 'False',
                                'lab-bubbles': selectedAnswer === 'False'
                            }"
                            @click="selectAnswer('False')"
                        >
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 flex items-center justify-center rounded-full border-2 mb-3 transition-all duration-300"
                                    :class="{
                                        'bg-gradient-to-r from-red-500 to-pink-600 border-transparent text-white shadow-lg': selectedAnswer === 'False',
                                        'border-gray-300': selectedAnswer !== 'False'
                                    }">
                                    <span class="text-2xl">✗</span>
                                </div>
                                <span class="text-xl font-bold text-gray-800">False</span>
                                <span class="text-sm text-gray-500 mt-1">This statement is incorrect</span>
                                <div v-if="selectedAnswer === 'False'" class="mt-3">
                                    <span class="px-3 py-1 bg-red-500 text-white rounded-full text-sm animate-fade-in">
                                        Selected
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Skip Question Button with Enhanced Effects -->
                    <transition name="slide-down">
                        <div v-if="answers[currentQuestionIndex] !== 'skipped'" class="mt-8 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg interactive-card">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-yellow-600 mr-3 text-2xl animate-pulse-slow">⏭️</span>
                                    <div>
                                        <span class="text-sm font-medium text-yellow-700">
                                            Not sure about this question?
                                        </span>
                                        <p class="text-xs text-yellow-600 mt-1">
                                            Skip it and come back later. You can always unskip.
                                        </p>
                                    </div>
                                </div>
                                <button 
                                    @click="skipQuestion"
                                    class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 font-medium transition-all duration-300 transform hover:scale-105 shadow-lg"
                                >
                                    <span class="flex items-center gap-2">
                                        <span>⏭️</span>
                                        <span>Skip Question</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </transition>

                    <!-- Navigation with Enhanced Effects -->
                    <div class="flex justify-between mt-10 pt-6 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <button 
                                @click="previousQuestion"
                                :disabled="currentQuestionIndex === 0"
                                class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center gap-2"
                            >
                                <span>←</span>
                                <span>Previous</span>
                            </button>
                            
                            <!-- Unskip Button with Animation -->
                            <transition name="slide-right">
                                <button 
                                    v-if="answers[currentQuestionIndex] === 'skipped'"
                                    @click="unskipQuestion"
                                    class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 font-medium transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center gap-2 animate-tada"
                                >
                                    <span>↩️</span>
                                    <span>Unskip & Answer</span>
                                </button>
                            </transition>
                        </div>
                        
                        <button 
                            @click="nextQuestion"
                            :disabled="!isAnswerSelected && answers[currentQuestionIndex] !== 'skipped'"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center gap-2"
                        >
                            <span>{{ currentQuestionIndex === quizzes.length - 1 ? 'Finish Quiz' : 'Next Question' }}</span>
                            <span>→</span>
                        </button>
                    </div>
                </div>

                <!-- Results with Enhanced Effects - Updated with more space -->
                <div v-if="quizCompleted" class="results-card rounded-lg shadow-2xl p-8 text-center interactive-card animate-slide-up">
                    <!-- Celebration Animation - Enhanced with more space -->
                    <div class="score-celebration mb-12">
                        <!-- Main Trophy with animation -->
                        <div class="trophy-container mb-8 relative">
                            <span class="text-7xl mb-6 animate-tada inline-block celebration-glow">🏆</span>
                            <div class="neural-network"></div>
                            <!-- Floating particles around trophy -->
                            <div class="celebration-particles">
                                <div class="celebration-particle" v-for="n in 12" :key="n" 
                                     :style="{
                                         left: `${Math.random() * 100}%`,
                                         top: `${Math.random() * 100}%`,
                                         animationDelay: `${n * 0.1}s`
                                     }">
                                    <span class="text-xl">{{ ['🎉', '✨', '🌟', '🎊', '🥳', '🎈', '💫', '⭐', '🔥', '🎯', '💎', '🚀'][n % 12] }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Main celebration text with more space -->
                        <div class="celebration-text mb-8">
                            <h2 class="text-4xl font-bold text-gray-800 mb-6 gradient-text cyber-glow animate-pulse-slow">
                                Quiz Completed! 🎉
                            </h2>
                            <div class="space-y-4">
                                <p class="text-2xl text-gray-700 font-medium">Great job completing the quiz!</p>
                                <p class="text-xl text-gray-600">Your knowledge has been tested and proven!</p>
                                <p class="text-lg text-gray-500 mt-2">🌟 Outstanding performance! 🌟</p>
                            </div>
                        </div>
                        
                        <!-- Achievement badges with more spacing -->
                        <div class="achievement-badges flex flex-wrap justify-center gap-4 mb-8">
                            <div class="badge-item px-5 py-3 bg-gradient-to-r from-blue-100 to-blue-200 rounded-xl flex items-center gap-3 shadow-lg">
                                <span class="text-2xl">✅</span>
                                <div class="text-left">
                                    <div class="text-sm text-blue-600">Answered</div>
                                    <div class="text-xl font-bold text-blue-800">{{ answeredCount }}</div>
                                </div>
                            </div>
                            <div class="badge-item px-5 py-3 bg-gradient-to-r from-green-100 to-green-200 rounded-xl flex items-center gap-3 shadow-lg">
                                <span class="text-2xl">🌟</span>
                                <div class="text-left">
                                    <div class="text-sm text-green-600">Correct</div>
                                    <div class="text-xl font-bold text-green-800">{{ correctCount }}</div>
                                </div>
                            </div>
                            <div class="badge-item px-5 py-3 bg-gradient-to-r from-purple-100 to-purple-200 rounded-xl flex items-center gap-3 shadow-lg">
                                <span class="text-2xl">⚡</span>
                                <div class="text-left">
                                    <div class="text-sm text-purple-600">Speed</div>
                                    <div class="text-xl font-bold text-purple-800">{{ Math.round(timeTaken / quizzes.length) }}s/Q</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Score Summary with Enhanced Effects -->
                    <div class="score-summary-container mb-12">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-2xl blur-xl opacity-60"></div>
                            <div class="relative bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-2xl p-10 border-4 border-white shadow-2xl">
                                <div class="text-8xl font-bold text-white mb-4 cyber-glow animate-tada">
                                    {{ score }}/{{ totalPossiblePoints }}
                                </div>
                                <p class="text-3xl text-white mb-2 celebration-glow">
                                    {{ ((score / totalPossiblePoints) * 100).toFixed(1) }}% Score
                                </p>
                                <div class="mt-8">
                                    <div class="w-full bg-white/20 h-4 rounded-full overflow-hidden">
                                        <div class="progress-bar h-full" 
                                             :style="{width: `${(score / totalPossiblePoints) * 100}%`}">
                                            <div class="progress-shimmer h-full"></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between mt-3 text-base text-white/90">
                                        <span>0%</span>
                                        <span class="text-xl font-bold">{{ Math.round((score / totalPossiblePoints) * 100) }}%</span>
                                        <span>100%</span>
                                    </div>
                                </div>
                                <p class="text-lg text-white/90 mt-6">
                                    Total Points: {{ score }} out of {{ totalPossiblePoints }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Results with Enhanced Grid -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-8 mb-10 border border-gray-200/50 shadow-2xl">
                        <h3 class="text-2xl font-bold text-gray-800 mb-8 text-center gradient-text">Quiz Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <!-- Total Points -->
                            <div class="text-center p-6 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-100 shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="text-4xl font-bold text-purple-600 mb-2">{{ score }}/{{ totalPossiblePoints }}</div>
                                <div class="text-lg font-medium text-gray-700 mb-1">Total Points</div>
                                <div class="text-sm text-gray-500">🎯 Accuracy</div>
                            </div>
                            
                            <!-- Answered -->
                            <div class="text-center p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-100 shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="text-4xl font-bold text-blue-600 mb-2">{{ answeredCount }}</div>
                                <div class="text-lg font-medium text-gray-700 mb-1">Answered</div>
                                <div class="text-sm text-gray-500">✅ Completed</div>
                            </div>
                            
                            <!-- Skipped -->
                            <div class="text-center p-6 rounded-2xl bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-100 shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="text-4xl font-bold text-yellow-600 mb-2">{{ skippedCount }}</div>
                                <div class="text-lg font-medium text-gray-700 mb-1">Skipped</div>
                                <div class="text-sm text-gray-500">⏭️ For Later</div>
                            </div>
                            
                            <!-- Correct -->
                            <div class="text-center p-6 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-100 shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="text-4xl font-bold text-green-600 mb-2">{{ correctCount }}</div>
                                <div class="text-lg font-medium text-gray-700 mb-1">Correct</div>
                                <div class="text-sm text-gray-500">🌟 Perfect</div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Taken with Enhanced Display -->
                    <div class="bg-gradient-to-r from-gray-50 to-slate-100 rounded-2xl p-8 mb-10 border border-gray-200 shadow-xl">
                        <h4 class="text-xl font-bold text-gray-800 mb-8 text-center">Performance Metrics</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-center gap-4 p-5 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-blue-100">
                                    <span class="text-3xl">⏱️</span>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm text-gray-500">Time Taken</div>
                                    <div class="text-2xl font-bold text-gray-800">{{ formatTime(timeTaken) }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-5 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-green-100">
                                    <span class="text-3xl">📝</span>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm text-gray-500">Questions</div>
                                    <div class="text-2xl font-bold text-gray-800">{{ quizzes.length }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-5 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-purple-100">
                                    <span class="text-3xl">⚡</span>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm text-gray-500">Average Time</div>
                                    <div class="text-2xl font-bold text-gray-800">{{ Math.round(timeTaken / quizzes.length) }}s/Q</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Participant Name Form with Enhanced Design -->
                    <div class="max-w-lg mx-auto mb-12">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 border-2 border-blue-200 shadow-lg">
                            <label class="block text-xl font-bold text-gray-800 mb-6 text-center">
                                <span class="text-3xl mr-3">👤</span>
                                Enter your name to save your results:
                            </label>
                            <div class="relative mb-4">
                                <input 
                                    v-model="participantName"
                                    type="text" 
                                    placeholder="Your Awesome Name"
                                    class="w-full px-8 py-5 border-3 border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-500 text-xl transition-all duration-300 shadow-sm"
                                />
                                <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                    <span class="text-2xl text-gray-400">✨</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 text-center mt-4">
                                Your results will be saved to the leaderboard for future reference
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons with Enhanced Effects -->
                    <div class="flex flex-col sm:flex-row justify-center gap-6">
                        <button 
                            @click="goToDashboard"
                            class="px-10 py-5 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-2xl hover:from-gray-700 hover:to-gray-800 font-medium transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center justify-center gap-3 text-lg w-full sm:w-auto"
                        >
                            <span class="text-2xl">🏠</span>
                            <span class="text-lg font-semibold">Back to Dashboard</span>
                        </button>
                        <button 
                            @click="submitResults"
                            :disabled="!participantName.trim()"
                            class="px-10 py-5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl hover:from-green-600 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center justify-center gap-3 text-lg w-full sm:w-auto"
                        >
                            <span class="text-2xl">📤</span>
                            <span class="text-lg font-semibold">Submit Results</span>
                            <span v-if="!participantName.trim()" class="text-xs bg-white/20 px-3 py-1 rounded-lg ml-2">
                                Enter name first
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Loading State with Enhanced Animation -->
                <div v-if="loading" class="text-center py-20">
                    <div class="inline-block relative">
                        <div class="w-24 h-24 border-4 border-blue-500 border-t-transparent rounded-full animate-spin-slow"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-3xl">🧠</span>
                        </div>
                    </div>
                    <p class="mt-8 text-gray-600 text-xl">Loading quiz...</p>
                    <p class="text-base text-gray-400 mt-3 animate-pulse-slow">Preparing your challenge</p>
                </div>

                <!-- Error State with Enhanced Design -->
                <div v-if="!loading && quizzes.length === 0 && !quizCompleted" class="text-center py-20">
                    <div class="inline-block p-8 bg-gradient-to-r from-red-50 to-pink-50 rounded-3xl shadow-2xl">
                        <span class="text-6xl mb-6 animate-shake">⚠️</span>
                        <h3 class="text-3xl font-bold text-gray-800 mb-4">No Questions Available</h3>
                        <p class="text-gray-600 mb-6">This quiz set doesn't have any questions yet.</p>
                        <button 
                            @click="fetchQuizzes"
                            class="mt-4 px-8 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-lg"
                        >
                            Retry Loading
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Particle Container for Effects -->
        <div class="particle-container" ref="particleContainer"></div>

        <!-- Hidden Easter Egg Messages -->
        <transition name="slide-down">
            <div v-if="showHiddenMessage" class="hidden-message animate-tada">
                <div class="message-content">
                    <span class="text-3xl mr-3">🎯</span>
                    <div>
                        <h3 class="font-bold">Achievement Unlocked!</h3>
                        <p class="text-sm opacity-75 mt-1">{{ randomFact }}</p>
                    </div>
                    <button @click="showHiddenMessage = false" class="ml-auto text-2xl hover:text-red-500 transition-colors">×</button>
                </div>
            </div>
        </transition>

        <!-- Nerd Mode Terminal -->
        <transition name="terminal">
            <div v-if="nerdMode" class="nerd-terminal">
                <div class="terminal-header">
                    <div class="terminal-dots">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                    </div>
                    <span class="terminal-title">quiz_master@nerd_mode:~</span>
                    <button @click="toggleNerdMode" class="terminal-close hover:text-white">×</button>
                </div>
                <div class="terminal-body">
                    <div class="terminal-line">
                        <span class="prompt">$</span> Welcome to <span class="text-cyan-400">Quiz Nerd Mode</span> <span class="blink">_</span>
                    </div>
                    <div class="terminal-line">
                        <span class="prompt">$</span> Quiz loaded at {{ new Date().toLocaleTimeString() }}
                    </div>
                    <div class="terminal-line">
                        <span class="prompt">></span> Questions: <span class="text-green-400">{{ quizzes.length }}</span>
                    </div>
                    <div class="terminal-line">
                        <span class="prompt">></span> Points Available: <span class="text-blue-400">{{ totalPossiblePoints }}</span>
                    </div>
                    <div class="terminal-line">
                        <span class="prompt">></span> Time Remaining: <span class="text-yellow-400">{{ timeLeft }}s</span>
                    </div>
                    <div class="terminal-line">
                        <span class="prompt">></span> Current Score: <span class="text-purple-400">{{ score }}/{{ totalPossiblePoints }}</span>
                    </div>
                    <div class="terminal-line">
                        <span class="prompt">$</span> Type <span class="text-yellow-400">'help'</span> for advanced commands
                    </div>
                    <div class="terminal-line">
                        <span class="prompt">#</span> <span class="text-pink-400">quiz_master_ai</span> is analyzing your performance...
                    </div>
                </div>
            </div>
        </transition>

        <!-- Matrix Rain Effect (Easter Egg) -->
        <div class="matrix-rain" :class="{ active: nerdMode }"></div>
    </div>
</template>

<script>
export default {
    name: 'QuizPage',
    props: {
        quizSetId: {
            type: [String, Number],
            required: true
        },
        theme: {
            type: String,
            default: 'light'
        }
    },
    data() {
        return {
            quizzes: [],
            currentQuestionIndex: 0,
            selectedAnswer: null,
            briefAnswer: '',
            answers: {},
            timeLeft: 60,
            timer: null,
            quizCompleted: false,
            score: 0,
            totalPossiblePoints: 0,
            participantName: '',
            loading: false,
            startTime: null,
            timeTaken: 0,
            // New state for enhanced features
            nerdMode: false,
            showEasterTooltip: false,
            showHiddenMessage: false,
            konamiProgress: 0,
            konamiSequence: ['↑', '↑', '↓', '↓', '←', '→', '←', '→', 'B', 'A'],
            randomFact: '',
            particles: [],
            isTextAreaFocused: false,
            funFacts: [
                "The human brain can store about 2.5 petabytes of information",
                "Honey never spoils - archaeologists have found pots of honey in ancient Egyptian tombs that are over 3,000 years old and still perfectly good to eat",
                "Octopuses have three hearts and blue blood",
                "A day on Venus is longer than a year on Venus",
                "Bananas are berries, but strawberries aren't",
                "The Eiffel Tower can be 15 cm taller during the summer",
                "Your brain uses about 20% of your body's energy",
                "There are more possible iterations of a game of chess than there are atoms in the observable universe"
            ]
        }
    },
    computed: {
        currentQuestion() {
            return this.quizzes[this.currentQuestionIndex] || {};
        },
        themeClass() {
            return `theme-${this.theme}`;
        },
        timerColor() {
            if (this.timeLeft > 20) return 'text-green-600';
            if (this.timeLeft > 10) return 'text-yellow-600';
            return 'text-red-600';
        },
        isAnswerSelected() {
            if (this.answers[this.currentQuestionIndex] === 'skipped') {
                return true;
            }
            if (this.currentQuestion.question_type === 'brief_answer') {
                return this.briefAnswer.trim().length > 0;
            }
            return this.selectedAnswer !== null;
        },
        answeredCount() {
            return Object.values(this.answers).filter(answer => 
                answer !== 'skipped' && answer !== null && answer !== ''
            ).length;
        },
        skippedCount() {
            return Object.values(this.answers).filter(answer => 
                answer === 'skipped'
            ).length;
        },
        correctCount() {
            return this.quizzes.reduce((count, quiz, index) => {
                const userAnswer = this.answers[index];
                if (userAnswer && userAnswer !== 'skipped' && 
                    userAnswer.toString().toLowerCase().trim() === quiz.correct_answer.toString().toLowerCase().trim()) {
                    return count + 1;
                }
                return count;
            }, 0);
        }
    },
    async mounted() {
        await this.initializeBrowserId();
        await this.fetchQuizzes();
        this.startTimer();
        this.startTime = Date.now();
        this.initParticles();
        this.initKonamiCode();
        this.initHiddenMessage();
        
        // Add nerd mode class to body when active
        if (this.nerdMode) {
            document.body.classList.add('nerd-mode-active');
        }
    },
    beforeUnmount() {
        this.clearTimer();
        document.body.classList.remove('nerd-mode-active');
    },
    methods: {
        async initializeBrowserId() {
            try {
                let browserId = localStorage.getItem('quiz_browser_id');
                if (!browserId) {
                    browserId = this.generateBrowserId();
                    localStorage.setItem('quiz_browser_id', browserId);
                    console.log('🆕 Generated browser ID at quiz start:', browserId);
                } else {
                    console.log('🔑 Existing browser ID found:', browserId);
                }
            } catch (error) {
                console.error('Error initializing browser ID:', error);
            }
        },

        generateBrowserId() {
            const timestamp = Date.now().toString(36);
            const random = Math.random().toString(36).substr(2, 9);
            return `browser_${timestamp}_${random}`;
        },

        getCurrentQuestionTypeLabel() {
            const labels = {
                'multiple_choice': 'Multiple Choice',
                'brief_answer': 'Brief Answer',
                'true_false': 'True/False'
            };
            return labels[this.currentQuestion.question_type] || 'Question';
        },

        async fetchQuizzes() {
            this.loading = true;
            try {
                const response = await fetch(`/api/quiz-sets/${this.quizSetId}/quizzes`);
                if (!response.ok) {
                    throw new Error('Failed to fetch quizzes');
                }
                this.quizzes = await response.json();
                
                this.totalPossiblePoints = this.quizzes.reduce((total, quiz) => total + (quiz.points || 1), 0);
                this.timeLeft = this.quizzes.length * 60;
            } catch (error) {
                console.error('Error fetching quizzes:', error);
                this.quizzes = this.getMockQuizzes();
                this.totalPossiblePoints = this.quizzes.reduce((total, quiz) => total + (quiz.points || 1), 0);
                this.timeLeft = this.quizzes.length * 60;
            } finally {
                this.loading = false;
            }
        },

        getMockQuizzes() {
            return [
                {
                    id: 1,
                    question: "What is the capital of France?",
                    question_type: "multiple_choice",
                    options: ["London", "Berlin", "Paris", "Madrid"],
                    correct_answer: "Paris",
                    points: 5
                },
                {
                    id: 2,
                    question: "Explain the concept of photosynthesis in your own words.",
                    question_type: "brief_answer",
                    correct_answer: "Photosynthesis is the process by which plants convert light energy into chemical energy to produce food.",
                    points: 5
                },
                {
                    id: 3,
                    question: "The Earth is the third planet from the Sun.",
                    question_type: "true_false",
                    correct_answer: "True",
                    points: 3
                },
                {
                    id: 4,
                    question: "Which of these are programming languages?",
                    question_type: "multiple_choice",
                    options: ["Python", "HTML", "CSS", "JavaScript"],
                    correct_answer: "Python",
                    points: 2
                }
            ];
        },

        startTimer() {
            this.timer = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    this.finishQuiz();
                }
            }, 1000);
        },

        clearTimer() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        selectAnswer(answer) {
            if (this.answers[this.currentQuestionIndex] === 'skipped') {
                return;
            }
            
            if (this.currentQuestion.question_type === 'brief_answer') {
                this.briefAnswer = answer;
            }
            this.selectedAnswer = answer;
            this.answers[this.currentQuestionIndex] = answer;
        },

        skipQuestion() {
            this.answers[this.currentQuestionIndex] = 'skipped';
            this.selectedAnswer = null;
            this.briefAnswer = '';
            this.createClickEffect();
        },

        unskipQuestion() {
            this.answers[this.currentQuestionIndex] = null;
            this.selectedAnswer = null;
            this.briefAnswer = '';
            this.createClickEffect();
        },

        previousQuestion() {
            if (this.currentQuestionIndex > 0) {
                this.currentQuestionIndex--;
                this.loadAnswerForCurrentQuestion();
                this.createClickEffect();
            }
        },

        nextQuestion() {
            if (this.currentQuestionIndex < this.quizzes.length - 1) {
                this.currentQuestionIndex++;
                this.loadAnswerForCurrentQuestion();
                this.createClickEffect();
            } else {
                this.finishQuiz();
            }
        },

        loadAnswerForCurrentQuestion() {
            const savedAnswer = this.answers[this.currentQuestionIndex];
            this.selectedAnswer = savedAnswer === 'skipped' ? null : savedAnswer;
            
            if (this.currentQuestion.question_type === 'brief_answer') {
                this.briefAnswer = savedAnswer === 'skipped' ? '' : (savedAnswer || '');
            } else {
                this.briefAnswer = '';
            }
        },

        finishQuiz() {
            this.clearTimer();
            this.timeTaken = Math.floor((Date.now() - this.startTime) / 1000);
            this.calculateScore();
            this.quizCompleted = true;
            this.createFireworks();
        },

        calculateScore() {
            this.score = this.quizzes.reduce((total, quiz, index) => {
                const userAnswer = this.answers[index];
                if (userAnswer && userAnswer !== 'skipped' && 
                    userAnswer.toString().toLowerCase().trim() === quiz.correct_answer.toString().toLowerCase().trim()) {
                    return total + (quiz.points || 1);
                }
                return total;
            }, 0);
        },

        goToDashboard() {
            this.createClickEffect();
            setTimeout(() => {
                this.$emit('completed');
            }, 300);
        },

        async submitResults() {
            if (!this.participantName.trim()) {
                alert('Please enter your name');
                return;
            }

            try {
                let browserId = localStorage.getItem('quiz_browser_id');
                if (!browserId) {
                    browserId = this.generateBrowserId();
                    localStorage.setItem('quiz_browser_id', browserId);
                    console.log('🆕 Generated new browser ID for quiz submission:', browserId);
                } else {
                    console.log('🔑 Using existing browser ID for submission:', browserId);
                }

                // Create the data object matching the controller's expectations
                const resultData = {
                    quiz_set_id: this.quizSetId,
                    participant_name: this.participantName,
                    score: this.score,
                    total_questions: this.quizzes.length,  // Changed from total_points to total_questions
                    answers: this.answers, // Keep as array, controller will handle it
                    time_taken: this.timeTaken,
                    percentage: parseFloat(((this.score / this.totalPossiblePoints) * 100).toFixed(2)), // Send calculated percentage
                    browser_id: browserId,
                    answered_count: this.answeredCount,
                    skipped_count: this.skippedCount,
                    correct_count: this.correctCount
                };

                console.log('📤 Submitting quiz results with data:', resultData);

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 document.querySelector('input[name="_token"]')?.value ||
                                 '';

                console.log('🔐 CSRF Token found:', csrfToken ? 'Yes' : 'No');

                const response = await fetch('/api/quiz-results', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(resultData)
                });

                console.log('📡 Response status:', response.status);
                
                if (!response.ok) {
                    let errorMessage = `Server error: ${response.status} - ${response.statusText}`;
                    try {
                        const errorData = await response.json();
                        console.error('❌ Server response error data:', errorData);
                        
                        // Check for validation errors
                        if (errorData.errors) {
                            errorMessage = 'Validation errors: ' + Object.values(errorData.errors).flat().join(', ');
                        } else if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        // If response is not JSON, get text
                        const errorText = await response.text();
                        console.error('❌ Server response text:', errorText);
                        errorMessage = errorText || errorMessage;
                    }
                    throw new Error(errorMessage);
                }

                const result = await response.json();
                console.log('✅ Results submitted successfully:', result);
                
                this.showNotification("Results submitted successfully! 🎉", "success");
                this.createFireworks();
                
                setTimeout(() => {
                    this.$emit('completed');
                }, 1000);
                
            } catch (error) {
                console.error('❌ Error submitting results:', error);
                
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    this.showNotification("Network error: Cannot connect to server. Please check your internet connection.", "error");
                } else {
                    this.showNotification("Failed to submit results: " + error.message, "error");
                }
            }
        },

        // New methods for enhanced features
        toggleNerdMode() {
            this.nerdMode = !this.nerdMode;
            if (this.nerdMode) {
                document.body.classList.add('nerd-mode-active');
                this.createFireworks();
                this.showNotification("Nerd Mode Activated!", "success");
            } else {
                document.body.classList.remove('nerd-mode-active');
            }
        },

        initParticles() {
            for (let i = 0; i < 15; i++) {
                this.particles.push({
                    style: {
                        left: `${Math.random() * 100}%`,
                        top: `${Math.random() * 100}%`,
                        animationDelay: `${Math.random() * 5}s`,
                        width: `${Math.random() * 8 + 4}px`,
                        height: `${Math.random() * 8 + 4}px`,
                        background: `hsl(${Math.random() * 360}, 70%, 60%)`
                    }
                });
            }
        },

        createFireworks() {
            for (let i = 0; i < 12; i++) {
                setTimeout(() => {
                    const firework = document.createElement('div');
                    firework.className = 'firework';
                    firework.style.cssText = `
                        left: ${Math.random() * 100}%;
                        top: ${Math.random() * 100}%;
                        --firework-color: hsl(${Math.random() * 360}, 100%, 60%);
                    `;
                    const container = this.$refs.particleContainer;
                    if (container) {
                        container.appendChild(firework);
                    }
                    
                    setTimeout(() => {
                        if (firework.parentNode) {
                            firework.parentNode.removeChild(firework);
                        }
                    }, 1000);
                }, i * 100);
            }
        },

        createClickEffect() {
            const container = this.$refs.particleContainer;
            if (!container) return;
            
            for (let i = 0; i < 8; i++) {
                const particle = document.createElement('div');
                particle.className = 'click-particle';
                particle.style.cssText = `
                    --tx: ${(Math.random() - 0.5) * 100}px;
                    --ty: ${(Math.random() - 0.5) * 100}px;
                    background: hsl(${Math.random() * 360}, 100%, 60%);
                `;
                container.appendChild(particle);
                
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.parentNode.removeChild(particle);
                    }
                }, 600);
            }
        },

        initKonamiCode() {
            const konamiMap = {
                'ArrowUp': '↑',
                'ArrowDown': '↓',
                'ArrowLeft': '←',
                'ArrowRight': '→',
                'KeyB': 'B',
                'KeyA': 'A'
            };
            
            let sequence = [];
            
            document.addEventListener('keydown', (e) => {
                const key = konamiMap[e.code];
                if (key) {
                    sequence.push(key);
                    
                    if (sequence.length > this.konamiSequence.length) {
                        sequence.shift();
                    }
                    
                    this.konamiProgress = 0;
                    for (let i = 0; i < sequence.length; i++) {
                        if (sequence[i] === this.konamiSequence[i]) {
                            this.konamiProgress = i + 1;
                        } else {
                            sequence = [];
                            this.konamiProgress = 0;
                            break;
                        }
                    }
                    
                    if (this.konamiProgress === this.konamiSequence.length) {
                        this.activateKonami();
                        sequence = [];
                        this.konamiProgress = 0;
                    }
                } else {
                    sequence = [];
                    this.konamiProgress = 0;
                }
            });
        },

        activateKonami() {
            this.nerdMode = true;
            this.showHiddenMessage = true;
            this.randomFact = this.funFacts[Math.floor(Math.random() * this.funFacts.length)];
            this.createFireworks();
            
            // Boost score for fun
            this.score = Math.min(this.score + 10, this.totalPossiblePoints);
            
            this.showNotification("Konami Code Unlocked! +10 Bonus Points!", "achievement");
            
            setTimeout(() => {
                this.showHiddenMessage = false;
            }, 8000);
        },

        initHiddenMessage() {
            // Show random fact every 45 seconds
            setInterval(() => {
                if (!this.showHiddenMessage && Math.random() > 0.7) {
                    this.randomFact = this.funFacts[Math.floor(Math.random() * this.funFacts.length)];
                    this.showHiddenMessage = true;
                    
                    setTimeout(() => {
                        this.showHiddenMessage = false;
                    }, 5000);
                }
            }, 45000);
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification-slide notification-${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        },

        activateFocusMode() {
            this.isTextAreaFocused = true;
        },

        deactivateFocusMode() {
            this.isTextAreaFocused = false;
        }
    }
}
</script>

<style scoped>
/* Enhanced Theme Variables */
:root {
    --quiz-bg: #f9fafb;
    --header-bg: #ffffff;
    --header-border: #e5e7eb;
    --header-title: #1e40af;
    --header-subtitle: #4b5563;
    --progress-count: #1f2937;
    --card-bg: #ffffff;
    --card-border: #e5e7eb;
    --question-number-bg: linear-gradient(135deg, #3b82f6, #8b5cf6);
    --question-number-text: #ffffff;
    --question-type-bg: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    --question-type-text: #374151;
    --question-points-bg: linear-gradient(135deg, #10b981, #34d399);
    --question-points-text: #ffffff;
    --question-text: #1f2937;
    --skip-badge-bg: linear-gradient(135deg, #f59e0b, #f97316);
    --skip-badge-text: #ffffff;
    --correct-answer-glow: rgba(34, 197, 94, 0.3);
    --incorrect-answer-glow: rgba(239, 68, 68, 0.3);
    --selected-answer-glow: rgba(59, 130, 246, 0.3);
}

.dark-theme {
    --quiz-bg: #111827;
    --header-bg: #1f2937;
    --header-border: #374151;
    --header-title: #60a5fa;
    --header-subtitle: #d1d5db;
    --progress-count: #f3f4f6;
    --card-bg: #1f2937;
    --card-border: #374151;
    --question-number-bg: linear-gradient(135deg, #1d4ed8, #7c3aed);
    --question-number-text: #bfdbfe;
    --question-type-bg: linear-gradient(135deg, #374151, #4b5563);
    --question-type-text: #d1d5db;
    --question-points-bg: linear-gradient(135deg, #047857, #10b981);
    --question-points-text: #a7f3d0;
    --question-text: #f3f4f6;
    --skip-badge-bg: linear-gradient(135deg, #d97706, #f59e0b);
    --skip-badge-text: #fbbf24;
    --correct-answer-glow: rgba(34, 197, 94, 0.5);
    --incorrect-answer-glow: rgba(239, 68, 68, 0.5);
    --selected-answer-glow: rgba(59, 130, 246, 0.5);
}

/* Base Background */
.min-h-screen.theme-light {
    background-color: var(--quiz-bg);
}

.min-h-screen.theme-dark {
    background-color: var(--quiz-bg);
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
    opacity: 0.2;
    filter: blur(2px);
    animation: float 20s infinite ease-in-out;
}

.circuit-board-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(to right, rgba(59, 130, 246, 0.1) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: 0.05;
}

/* Enhanced Header */
.header.theme-light {
    background: linear-gradient(135deg, var(--header-bg), #f8fafc);
    border-bottom: 2px solid var(--header-border);
    backdrop-filter: blur(10px);
}

.header.theme-dark {
    background: linear-gradient(135deg, var(--header-bg), #1e293b);
    border-bottom: 2px solid var(--header-border);
    backdrop-filter: blur(10px);
}

.header-title {
    color: var(--header-title);
}

.header-subtitle {
    color: var(--header-subtitle);
}

.progress-count {
    color: var(--progress-count);
}

/* Enhanced Quiz Card */
.quiz-card.theme-light {
    background: linear-gradient(135deg, var(--card-bg), #f8fafc);
    border: 2px solid var(--card-border);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 10px 20px rgba(0, 0, 0, 0.05);
    border-radius: 1rem;
    padding: 2rem;
}

.quiz-card.theme-dark {
    background: linear-gradient(135deg, var(--card-bg), #1e293b);
    border: 2px solid var(--card-border);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 10px 20px rgba(0, 0, 0, 0.2);
    border-radius: 1rem;
    padding: 2rem;
}

/* Enhanced Question Number */
.question-number {
    background: var(--question-number-bg);
    color: var(--question-number-text);
    border-radius: 9999px;
    font-size: 1rem;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

/* Enhanced Question Type */
.question-type {
    background: var(--question-type-bg);
    color: var(--question-type-text);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

/* Enhanced Question Points */
.question-points {
    background: var(--question-points-bg);
    color: var(--question-points-text);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

/* Enhanced Skip Badge */
.skip-badge {
    background: var(--skip-badge-bg);
    color: var(--skip-badge-text);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

/* Question Text */
.question-text {
    color: var(--question-text);
}

/* Progress Bar */
.progress-bar {
    background: linear-gradient(90deg, 
        #3b82f6, 
        #8b5cf6, 
        #ec4899, 
        #f59e0b,
        #10b981
    );
    background-size: 200% 100%;
    animation: gradient-shift 3s ease infinite;
}

.progress-shimmer {
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 255, 255, 0.5),
        transparent
    );
    animation: data-stream 2s linear infinite;
}

/* Celebration Section */
.score-celebration {
    position: relative;
    padding: 2.5rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.05));
    border-radius: 1.5rem;
    margin-bottom: 3rem;
    overflow: visible;
    border: 2px solid rgba(255, 215, 0, 0.2);
}

.trophy-container {
    min-height: 160px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    margin-bottom: 2rem;
}

/* Celebration Particles */
.celebration-particles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
}

.celebration-particle {
    position: absolute;
    animation: float-particle 3s ease-in-out infinite;
    opacity: 0.8;
}

/* Celebration Text */
.celebration-text {
    margin: 2rem 0;
    padding: 1rem;
}

.celebration-text h2 {
    font-size: 3rem;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    text-shadow: 0 2px 20px rgba(255, 215, 0, 0.3);
}

.celebration-text p {
    font-size: 1.5rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

/* Achievement Badges */
.achievement-badges {
    margin: 2rem 0;
    padding: 1rem;
}

.badge-item {
    backdrop-filter: blur(12px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transition: all 0.4s ease;
    min-width: 160px;
}

.badge-item:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
}

/* Score Summary Container */
.score-summary-container {
    margin: 3rem 0;
    padding: 1rem;
}

.score-summary-container .relative {
    min-height: 300px;
    display: flex;
    align-items: center;
}

/* Results Card */
.results-card {
    background: linear-gradient(135deg, #ffffff, #f8fafc);
    border: 3px solid #e5e7eb;
    box-shadow: 0 40px 80px rgba(0, 0, 0, 0.2);
    padding: 3rem !important;
    max-width: 1000px;
    margin: 0 auto;
    border-radius: 2rem;
}

.dark-theme .results-card {
    background: linear-gradient(135deg, #1f2937, #1e293b);
    border: 3px solid #374151;
}

/* Dark theme adjustments */
.dark-theme .score-celebration {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.6), rgba(15, 23, 42, 0.6));
    border: 2px solid rgba(255, 215, 0, 0.3);
}

.dark-theme .badge-item {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.9));
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.dark-theme .celebration-text h2 {
    color: #f3f4f6;
}

.dark-theme .celebration-text p {
    color: #d1d5db;
}

/* Dark theme overrides */
.dark-theme .text-gray-800 {
    color: #f3f4f6;
}

.dark-theme .text-gray-700 {
    color: #d1d5db;
}

.dark-theme .text-gray-600 {
    color: #9ca3af;
}

.dark-theme .text-gray-500 {
    color: #9ca3af;
}

.dark-theme .text-blue-800 {
    color: #bfdbfe;
}

.dark-theme .text-yellow-700 {
    color: #fbbf24;
}

.dark-theme .text-yellow-600 {
    color: #fbbf24;
}

/* Enhanced backgrounds for dark theme */
.dark-theme .bg-white {
    background-color: #1f2937;
}

.dark-theme .bg-blue-50 {
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.5), rgba(37, 99, 235, 0.5));
}

.dark-theme .bg-green-50 {
    background: linear-gradient(135deg, rgba(6, 78, 59, 0.5), rgba(16, 185, 129, 0.5));
}

.dark-theme .bg-red-50 {
    background: linear-gradient(135deg, rgba(127, 29, 29, 0.5), rgba(239, 68, 68, 0.5));
}

.dark-theme .bg-yellow-50 {
    background: linear-gradient(135deg, rgba(120, 53, 15, 0.5), rgba(245, 158, 11, 0.5));
}

.dark-theme .bg-gradient-to-r {
    background: linear-gradient(135deg, #1e3a8a, #064e3b);
}

.dark-theme .bg-gray-50 {
    background-color: #374151;
}

/* Enhanced borders for dark theme */
.dark-theme .border-blue-200 {
    border-color: #60a5fa;
}

.dark-theme .border-gray-200 {
    border-color: #4b5563;
}

.dark-theme .border-gray-300 {
    border-color: #4b5563;
}

/* Easter Egg Trigger */
.easter-egg-trigger {
    position: fixed;
    top: 80px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    animation: pulse-glow 2s infinite;
}

.easter-egg-trigger:hover {
    transform: scale(1.1) rotate(15deg);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
}

.easter-egg-trigger.nerd-mode-active {
    background: linear-gradient(135deg, #00ff00 0%, #008800 100%);
    animation: spin 2s linear infinite, pulse-glow 1s infinite;
}

.easter-tooltip {
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

.easter-tooltip::before {
    content: '';
    position: absolute;
    bottom: 100%;
    right: 15px;
    border-width: 0 6px 6px 6px;
    border-style: solid;
    border-color: transparent transparent #1f2937 transparent;
}

/* Konami Code Hint */
.konami-hint {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: #00ff00;
    padding: 8px 16px;
    border-radius: 20px;
    font-family: monospace;
    font-size: 12px;
    z-index: 1000;
    opacity: 0.7;
    backdrop-filter: blur(10px);
}

/* Stat Cards */
.stat-card {
    background: linear-gradient(135deg, var(--stat-card-bg), var(--card-bg));
    border-radius: 1rem;
    padding: 1.5rem;
    border: 2px solid var(--card-border);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.blue-card {
    border-left: 4px solid #3b82f6;
}

.green-card {
    border-left: 4px solid #10b981;
}

.purple-card {
    border-left: 4px solid #8b5cf6;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(45deg, currentColor, #ffffff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    color: var(--tile-description-color);
    font-size: 0.875rem;
    font-weight: 600;
}

/* Hidden Message */
.hidden-message {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 15px;
    max-width: 400px;
    z-index: 1000;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
}

.message-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* Nerd Terminal */
.nerd-terminal {
    position: fixed;
    bottom: 30px;
    left: 30px;
    width: 400px;
    max-width: 90%;
    background: #1a1a1a;
    border-radius: 10px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    z-index: 1001;
    overflow: hidden;
    font-family: 'Courier New', monospace;
    border: 1px solid #00ff00;
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

.terminal-title {
    color: #888;
    font-size: 14px;
    font-family: 'Courier New', monospace;
}

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
    color: #00ff00;
    font-size: 14px;
    max-height: 300px;
    overflow-y: auto;
}

.terminal-line {
    margin-bottom: 10px;
    line-height: 1.5;
}

.prompt {
    color: #00ff00;
    margin-right: 10px;
    font-weight: bold;
}

.blink {
    animation: blink 1s infinite;
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
    font-family: 'Courier New', monospace;
    color: #00ff00;
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

/* Fireworks */
.firework {
    position: absolute;
    width: 4px;
    height: 4px;
    background: var(--firework-color);
    border-radius: 50%;
    animation: firework-explode 1s ease-out forwards;
    pointer-events: none;
}

/* Click Particles */
.click-particle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: var(--click-color);
    border-radius: 50%;
    animation: particle-burst 0.6s ease-out forwards;
    pointer-events: none;
}

/* Neural Network */
.neural-network {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.neural-node {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #0066ff;
    border-radius: 50%;
    animation: node-pulse 2s infinite;
}

/* Score Celebration */
.score-celebration {
    position: flex;
    overflow: hidden;
}

.score-celebration::before {
    content: '🎉';
    position: flex;
    font-size: 2em;
    animation: float 1s ease-in-out infinite;
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

.animate-spin-slow {
    animation: spin 3s linear infinite;
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

.animate-gradient-shift {
    background-size: 200% 200%;
    animation: gradient-shift 3s ease infinite;
}

.animate-count-up {
    animation: slide-up 0.5s ease-out;
}

/* Transition Effects */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-down-enter,
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(20px);
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

/* New Animations */
@keyframes float-particle {
    0%, 100% {
        transform: translateY(0) translateX(0) scale(1);
        opacity: 0.8;
    }
    33% {
        transform: translateY(-30px) translateX(15px) scale(1.2);
        opacity: 1;
    }
    66% {
        transform: translateY(-15px) translateX(-15px) scale(0.9);
        opacity: 0.6;
    }
}

@keyframes celebration-pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes trophy-spin {
    0% {
        transform: rotateY(0deg);
    }
    100% {
        transform: rotateY(360deg);
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

@keyframes confetti-fall {
    0% {
        transform: translateY(-100vh) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

/* Celebration Glow */
.celebration-glow {
    text-shadow: 
        0 0 20px rgba(255, 215, 0, 0.5),
        0 0 40px rgba(255, 215, 0, 0.3),
        0 0 60px rgba(255, 215, 0, 0.1);
    animation: celebration-pulse 2s ease-in-out infinite;
}

/* Interactive Card Base */
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
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.7s ease;
}

.interactive-card:hover::before {
    left: 100%;
}

/* Transform effects */
.transform {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}

/* Answer Effect Classes */
.correct-answer {
    position: relative;
    overflow: hidden;
}

.correct-answer::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: inherit;
    background: linear-gradient(45deg, #10b981, #34d399, #10b981);
    z-index: -1;
    opacity: 0.5;
    animation: gradient-shift 2s infinite;
}

.incorrect-answer {
    position: relative;
    overflow: hidden;
}

.incorrect-answer::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: inherit;
    background: linear-gradient(45deg, #ef4444, #f87171, #ef4444);
    z-index: -1;
    opacity: 0.5;
    animation: gradient-shift 2s infinite;
}

/* Countdown Timer Effects */
.countdown-pulse {
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #10b981;
    animation: ping-slow 1s infinite;
}

.countdown-warning {
    background: #ef4444;
    animation: ping-slow 0.5s infinite;
}

/* Lab Bubbles */
.lab-bubbles {
    position: relative;
}

.lab-bubbles::before,
.lab-bubbles::after {
    content: '';
    position: absolute;
    width: 15px;
    height: 15px;
    background: #00ffff;
    border-radius: 50%;
    animation: bubble-rise 3s infinite ease-in;
}

.lab-bubbles::before {
    left: 20%;
    animation-delay: 0s;
}

.lab-bubbles::after {
    left: 70%;
    animation-delay: 1.5s;
}

/* Glitch Effect */
.glitch {
    position: relative;
}

.glitch::before,
.glitch::after {
    content: attr(data-text);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.glitch:hover::before {
    animation: glitch-anim-1 0.5s infinite linear alternate-reverse;
    clip-path: polygon(0 0, 100% 0, 100% 35%, 0 65%);
    color: #ff00ff;
}

.glitch:hover::after {
    animation: glitch-anim-2 0.5s infinite linear alternate-reverse;
    clip-path: polygon(0 65%, 100% 35%, 100% 100%, 0 100%);
    color: #00ffff;
}

/* Cyber Glow */
.cyber-glow {
    text-shadow: 
        0 0 10px currentColor,
        0 0 20px currentColor,
        0 0 40px currentColor;
}

/* Cyber Scan Line */
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
        #00ff00,
        transparent
    );
    animation: cyber-scan 2s linear infinite;
}

/* Gradient Text */
.gradient-text {
    background: linear-gradient(45deg, 
        #0066ff, 
        #9900ff, 
        #00ffff
    );
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gradient-shift 3s ease infinite;
}

/* Loading animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Ensure click events work */
.cursor-pointer {
    cursor: pointer !important;
}

/* Focus mode effect */
.focus-mode-active {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe) !important;
    border-color: #0ea5e9 !important;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2) !important;
}

.dark-theme .focus-mode-active {
    background: linear-gradient(135deg, #0c4a6e, #075985) !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.3) !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .nerd-terminal {
        width: calc(100% - 40px);
        left: 20px;
        bottom: 20px;
    }
    
    .hidden-message {
        left: 20px;
        right: 20px;
        max-width: none;
    }
    
    .easter-egg-trigger {
        width: 40px;
        height: 40px;
        top: 70px;
        right: 10px;
    }
    
    .konami-hint {
        font-size: 10px;
        padding: 6px 12px;
        bottom: 10px;
    }
    
    .quiz-card.theme-light,
    .quiz-card.theme-dark {
        padding: 1.5rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    /* Responsive celebration */
    .results-card {
        padding: 1.5rem !important;
    }
    
    .score-celebration {
        padding: 1.5rem;
    }
    
    .celebration-text h2 {
        font-size: 2rem;
    }
    
    .celebration-text p {
        font-size: 1.1rem;
    }
    
    .trophy-container {
        min-height: 120px;
    }
    
    .badge-item {
        min-width: 140px;
        padding: 0.75rem;
    }
}

@media (max-width: 480px) {
    .celebration-text h2 {
        font-size: 1.75rem;
    }
    
    .score-celebration {
        padding: 1rem;
    }
    
    .trophy-container span {
        font-size: 5rem;
    }
    
    .achievement-badges {
        flex-direction: column;
        align-items: center;
    }
    
    .badge-item {
        width: 100%;
        max-width: 250px;
    }
}

/* Confetti effect */
.confetti {
    position: absolute;
    width: 10px;
    height: 20px;
    background: var(--confetti-color);
    animation: confetti-fall 5s linear infinite;
}
</style>