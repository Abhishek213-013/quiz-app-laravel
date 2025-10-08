<template>
    <div class="flex-1">
        <div class="min-h-screen bg-gray-100">
            <!-- Sticky Header -->
            <div class="sticky top-0 z-50 bg-white shadow-lg border-b border-gray-200">
                <div class="container mx-auto px-4 py-4 max-w-4xl">
                    <div class="flex items-center justify-between">
                        <!-- Empty div for spacing -->
                        <div class="w-24"></div>
                        
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
                    <!-- Question -->
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                        {{ currentQuestion.question }}
                    </h2>

                    <!-- Options -->
                    <div class="space-y-3">
                        <div 
                            v-for="(option, index) in currentQuestion.options" 
                            :key="index"
                            class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200"
                            :class="{
                                'bg-blue-50 border-blue-500': selectedAnswer === option,
                                'hover:border-blue-300': !selectedAnswer
                            }"
                            @click="selectAnswer(option)"
                        >
                            <div class="flex items-center">
                                <div class="w-8 h-8 flex items-center justify-center rounded-full border-2 border-gray-300 mr-3"
                                    :class="{'bg-blue-500 border-blue-500 text-white': selectedAnswer === option}">
                                    {{ String.fromCharCode(65 + index) }}
                                </div>
                                <span class="text-lg">{{ option }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between mt-8">
                        <button 
                            @click="previousQuestion"
                            :disabled="currentQuestionIndex === 0"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        
                        <button 
                            @click="nextQuestion"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
                        >
                            {{ currentQuestionIndex === quizzes.length - 1 ? 'Finish' : 'Next' }}
                        </button>
                    </div>
                </div>

                <!-- Results -->
                <div v-if="quizCompleted" class="bg-white rounded-lg shadow-lg p-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Quiz Completed!</h2>
                    <div class="text-6xl font-bold text-green-600 mb-4">{{ score }}/{{ quizzes.length }}</div>
                    <p class="text-xl text-gray-600 mb-6">
                        You scored {{ ((score / quizzes.length) * 100).toFixed(1) }}%
                    </p>
                    
                    <!-- Participant Name Form -->
                    <div class="max-w-md mx-auto mb-6">
                        <input 
                            v-model="participantName"
                            type="text" 
                            placeholder="Enter your name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button 
                            @click="goToDashboard"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
                        >
                            Back to Dashboard
                        </button>
                        <button 
                            @click="submitResults"
                            :disabled="!participantName.trim()"
                            class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Submit Results
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600">Loading quiz...</p>
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
            answers: {},
            timeLeft: 60,
            timer: null,
            quizCompleted: false,
            score: 0,
            participantName: '',
            loading: false,
            startTime: null
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
        async fetchQuizzes() {
            this.loading = true;
            try {
                const response = await fetch(`/api/quiz-sets/${this.quizSetId}/quizzes`);
                if (!response.ok) {
                    throw new Error('Failed to fetch quizzes');
                }
                this.quizzes = await response.json();
                
                // Calculate time: 60 seconds per question
                this.timeLeft = this.quizzes.length * 5;
            } catch (error) {
                console.error('Error fetching quizzes:', error);
                alert('Failed to load quiz. Please try again.');
            } finally {
                this.loading = false;
            }
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
            this.selectedAnswer = answer;
            this.answers[this.currentQuestionIndex] = answer;
        },

        previousQuestion() {
            if (this.currentQuestionIndex > 0) {
                this.currentQuestionIndex--;
                this.selectedAnswer = this.answers[this.currentQuestionIndex] || null;
            }
        },

        nextQuestion() {
            if (this.currentQuestionIndex < this.quizzes.length - 1) {
                this.currentQuestionIndex++;
                this.selectedAnswer = this.answers[this.currentQuestionIndex] || null;
            } else {
                this.finishQuiz();
            }
        },

        finishQuiz() {
            this.clearTimer();
            this.calculateScore();
            this.quizCompleted = true;
        },

        calculateScore() {
            this.score = this.quizzes.reduce((total, quiz, index) => {
                if (this.answers[index] === quiz.correct_answer) {
                    return total + 1;
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

            const timeTaken = Math.floor((Date.now() - this.startTime) / 1000);

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
                        answers: this.answers,
                        time_taken: timeTaken
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