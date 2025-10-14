<template>
    <div class="flex-1">
        <div class="min-h-screen bg-gray-100">
            <!-- Sticky Header -->
            <div class="sticky top-0 z-50 bg-white shadow-lg border-b border-gray-200">
                <div class="container mx-auto px-4 py-4 max-w-4xl">
                    <div class="flex items-center justify-between">
                        <!-- Question Type Indicator -->
                        <div class="text-left">
                            <div class="text-sm font-medium text-gray-500">Question Type</div>
                            <div class="text-lg font-bold text-blue-600">{{ getCurrentQuestionTypeLabel() }}</div>
                        </div>
                        
                        <!-- Timer -->
                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-700">Time Remaining</div>
                            <div class="text-2xl font-bold" :class="timerClass">{{ formatTime(timeLeft) }}</div>
                        </div>

                        <!-- Progress -->
                        <div class="text-right">
                            <div class="text-lg font-semibold text-gray-700">Progress</div>
                            <div class="text-xl font-bold">{{ currentQuestionIndex + 1 }} / {{ quizzes.length }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container mx-auto px-4 py-8 max-w-4xl">
                <!-- Quiz Content -->
                <div v-if="!quizCompleted && quizzes.length > 0" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <!-- Question Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                Q{{ currentQuestionIndex + 1 }}
                            </span>
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                                {{ getCurrentQuestionTypeLabel() }}
                            </span>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">
                                {{ currentQuestion.points }} point{{ currentQuestion.points !== 1 ? 's' : '' }}
                            </span>
                            <!-- Skip Status Badge -->
                            <span v-if="answers[currentQuestionIndex] === 'skipped'" 
                                  class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded">
                                Skipped
                            </span>
                        </div>
                    </div>

                    <!-- Question -->
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                        {{ currentQuestion.question }}
                    </h2>

                    <!-- Multiple Choice Options -->
                    <div v-if="currentQuestion.question_type === 'multiple_choice'" class="space-y-3">
                        <div 
                            v-for="(option, index) in currentQuestion.options" 
                            :key="index"
                            class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200"
                            :class="{
                                'bg-blue-50 border-blue-500': selectedAnswer === option,
                                'border-gray-200': selectedAnswer !== option,
                                'hover:border-blue-300': !selectedAnswer && answers[currentQuestionIndex] !== 'skipped'
                            }"
                            @click="selectAnswer(option)"
                        >
                            <div class="flex items-center">
                                <div class="w-8 h-8 flex items-center justify-center rounded-full border-2 mr-3"
                                    :class="{
                                        'bg-blue-500 border-blue-500 text-white': selectedAnswer === option,
                                        'border-gray-300': selectedAnswer !== option
                                    }">
                                    {{ String.fromCharCode(65 + index) }}
                                </div>
                                <span class="text-lg">{{ option }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Brief Answer Input -->
                    <div v-else-if="currentQuestion.question_type === 'brief_answer'" class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <label class="block text-sm font-medium text-blue-800 mb-2">
                                Type your answer below:
                            </label>
                            <textarea
                                v-model="briefAnswer"
                                rows="4"
                                placeholder="Enter your answer here..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                :disabled="answers[currentQuestionIndex] === 'skipped'"
                                @input="selectAnswer(briefAnswer)"
                            ></textarea>
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Your answer will be automatically saved as you type.
                        </div>
                    </div>

                    <!-- True/False Options -->
                    <div v-else-if="currentQuestion.question_type === 'true_false'" class="grid grid-cols-2 gap-4">
                        <div 
                            class="p-6 border-2 rounded-lg cursor-pointer text-center transition-all duration-200"
                            :class="{
                                'bg-green-50 border-green-500': selectedAnswer === 'True',
                                'border-gray-200': selectedAnswer !== 'True',
                                'hover:border-green-300': !selectedAnswer && answers[currentQuestionIndex] !== 'skipped'
                            }"
                            @click="selectAnswer('True')"
                        >
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 flex items-center justify-center rounded-full border-2 mb-2"
                                    :class="{
                                        'bg-green-500 border-green-500 text-white': selectedAnswer === 'True',
                                        'border-gray-300': selectedAnswer !== 'True'
                                    }">
                                    <i class="fas fa-check text-lg"></i>
                                </div>
                                <span class="text-xl font-semibold text-gray-800">True</span>
                            </div>
                        </div>
                        <div 
                            class="p-6 border-2 rounded-lg cursor-pointer text-center transition-all duration-200"
                            :class="{
                                'bg-red-50 border-red-500': selectedAnswer === 'False',
                                'border-gray-200': selectedAnswer !== 'False',
                                'hover:border-red-300': !selectedAnswer && answers[currentQuestionIndex] !== 'skipped'
                            }"
                            @click="selectAnswer('False')"
                        >
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 flex items-center justify-center rounded-full border-2 mb-2"
                                    :class="{
                                        'bg-red-500 border-red-500 text-white': selectedAnswer === 'False',
                                        'border-gray-300': selectedAnswer !== 'False'
                                    }">
                                    <i class="fas fa-times text-lg"></i>
                                </div>
                                <span class="text-xl font-semibold text-gray-800">False</span>
                            </div>
                        </div>
                    </div>

                    <!-- Skip Question Button -->
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-forward text-yellow-600 mr-2"></i>
                                <span class="text-sm text-yellow-700">
                                    Not sure about this question? You can skip it and come back later.
                                </span>
                            </div>
                            <button 
                                @click="skipQuestion"
                                :disabled="answers[currentQuestionIndex] === 'skipped'"
                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors text-sm"
                            >
                                <i class="fas fa-forward mr-1"></i>
                                {{ answers[currentQuestionIndex] === 'skipped' ? 'Skipped' : 'Skip Question' }}
                            </button>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between mt-8">
                        <div class="flex space-x-3">
                            <button 
                                @click="previousQuestion"
                                :disabled="currentQuestionIndex === 0"
                                class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>
                                Previous
                            </button>
                            
                            <!-- Unskip Button -->
                            <button 
                                v-if="answers[currentQuestionIndex] === 'skipped'"
                                @click="unskipQuestion"
                                class="px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 font-medium transition-colors"
                            >
                                <i class="fas fa-undo mr-2"></i>
                                Unskip
                            </button>
                        </div>
                        
                        <button 
                            @click="nextQuestion"
                            :disabled="!isAnswerSelected && answers[currentQuestionIndex] !== 'skipped'"
                            class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors"
                        >
                            {{ currentQuestionIndex === quizzes.length - 1 ? 'Finish Quiz' : 'Next Question' }}
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Results -->
                <div v-if="quizCompleted" class="bg-white rounded-lg shadow-lg p-8 text-center">
                    <div class="mb-6">
                        <i class="fas fa-trophy text-6xl text-yellow-500 mb-4"></i>
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Quiz Completed!</h2>
                        <p class="text-lg text-gray-600">Great job completing the quiz!</p>
                    </div>
                    
                    <!-- Score Summary -->
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-6 mb-6 border border-blue-200">
                        <div class="text-6xl font-bold text-green-600 mb-2">{{ score }}/{{ totalPossiblePoints }}</div>
                        <p class="text-xl text-gray-600 mb-1">
                            {{ ((score / totalPossiblePoints) * 100).toFixed(1) }}% Score
                        </p>
                        <p class="text-sm text-gray-500">
                            Total Points: {{ score }} out of {{ totalPossiblePoints }}
                        </p>
                    </div>

                    <!-- Detailed Results -->
                    <!-- Detailed Results - Horizontal Layout -->
                    <div class="bg-white rounded-lg p-6 mb-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Quiz Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Total Points -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ score }}/{{ totalPossiblePoints }}</div>
                                <div class="text-sm font-medium text-gray-700 mt-1">Total Points</div>
                            </div>
                            
                            <!-- Answered -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ answeredCount }}</div>
                                <div class="text-sm font-medium text-gray-700 mt-1">Answered</div>
                            </div>
                            
                            <!-- Skipped -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ skippedCount }}</div>
                                <div class="text-sm font-medium text-gray-700 mt-1">Skipped</div>
                            </div>
                            
                            <!-- Correct -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ correctCount }}</div>
                                <div class="text-sm font-medium text-gray-700 mt-1">Correct</div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Taken -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex justify-center items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-blue-500"></i>
                                <span>Time Taken: {{ formatTime(timeTaken) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-list-ol text-green-500"></i>
                                <span>Questions: {{ quizzes.length }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Participant Name Form -->
                    <div class="max-w-md mx-auto mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">
                            Enter your name to save your results:
                        </label>
                        <input 
                            v-model="participantName"
                            type="text" 
                            placeholder="Your Name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button 
                            @click="goToDashboard"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 font-medium transition-colors"
                        >
                            <i class="fas fa-home mr-2"></i>
                            Back to Dashboard
                        </button>
                        <button 
                            @click="submitResults"
                            :disabled="!participantName.trim()"
                            class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors"
                        >
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Results
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600">Loading quiz...</p>
                </div>

                <!-- Error State -->
                <div v-if="!loading && quizzes.length === 0 && !quizCompleted" class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No Questions Available</h3>
                    <p class="text-gray-600">This quiz set doesn't have any questions yet.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'QuizPage',
    props: {
        quizSetId: {
            type: [String, Number],
            required: true
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
            timeTaken: 0
        }
    },
    computed: {
        currentQuestion() {
            return this.quizzes[this.currentQuestionIndex] || {};
        },
        timerClass() {
            if (this.timeLeft > 20) return 'text-green-600';
            if (this.timeLeft > 10) return 'text-yellow-600';
            return 'text-red-600';
        },
        isAnswerSelected() {
            if (this.answers[this.currentQuestionIndex] === 'skipped') {
                return true; // Allow navigation if question is skipped
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
        await this.fetchQuizzes();
        this.startTimer();
        this.startTime = Date.now();
    },
    beforeUnmount() {
        this.clearTimer();
    },
    methods: {
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
                // Mock API call - replace with your actual API endpoint
                const response = await fetch(`/api/quiz-sets/${this.quizSetId}/quizzes`);
                if (!response.ok) {
                    throw new Error('Failed to fetch quizzes');
                }
                this.quizzes = await response.json();
                
                // If you want to test locally without an API, use this mock data:
                // this.quizzes = this.getMockQuizzes();
                
                // Calculate total possible points
                this.totalPossiblePoints = this.quizzes.reduce((total, quiz) => total + (quiz.points || 1), 0);
                
                // Calculate time: 60 seconds per question
                this.timeLeft = this.quizzes.length * 60;
            } catch (error) {
                console.error('Error fetching quizzes:', error);
                // Fallback to mock data if API fails
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
                return; // Don't allow answering if question is skipped
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
        },

        unskipQuestion() {
            this.answers[this.currentQuestionIndex] = null;
            this.selectedAnswer = null;
            this.briefAnswer = '';
        },

        previousQuestion() {
            if (this.currentQuestionIndex > 0) {
                this.currentQuestionIndex--;
                this.loadAnswerForCurrentQuestion();
            }
        },

        nextQuestion() {
            if (this.currentQuestionIndex < this.quizzes.length - 1) {
                this.currentQuestionIndex++;
                this.loadAnswerForCurrentQuestion();
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
            this.$emit('completed');
        },

        async submitResults() {
            if (!this.participantName.trim()) {
                alert('Please enter your name');
                return;
            }

            try {
                const response = await fetch('/api/quiz-results', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        quiz_set_id: this.quizSetId,
                        participant_name: this.participantName,
                        score: this.score,
                        total_questions: this.quizzes.length,
                        total_points: this.totalPossiblePoints,
                        answers: this.answers,
                        time_taken: this.timeTaken,
                        percentage: ((this.score / this.totalPossiblePoints) * 100).toFixed(1),
                        answered_count: this.answeredCount,
                        skipped_count: this.skippedCount,
                        correct_count: this.correctCount
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to submit results');
                }

                alert('Results submitted successfully!');
                this.$emit('completed');
            } catch (error) {
                console.error('Error submitting results:', error);
                alert('Failed to submit results. Please try again.');
            }
        }
    }
}
</script>

<style scoped>
/* Custom styles if needed */
</style>