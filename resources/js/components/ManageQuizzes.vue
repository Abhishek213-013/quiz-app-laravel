<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
        <!-- Simplified Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Manage Quizzes</h1>
                <p class="text-gray-600 mt-2">Add, edit, or delete quiz sets and questions</p>
            </div>
            <button 
                @click="showAddSetModal = true"
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600"
            >
                Add New Set
            </button>
        </div>

        <!-- Quiz Sets Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div 
                v-for="quizSet in quizSets" 
                :key="quizSet.id"
                class="bg-white rounded-lg shadow-md p-6"
            >
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                        {{ quizSet.category }}
                    </span>
                    <div class="flex space-x-2">
                        <button 
                            @click="editQuizSet(quizSet)"
                            class="p-1 text-blue-600 hover:text-blue-800"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button 
                            @click="deleteQuizSet(quizSet.id)"
                            class="p-1 text-red-600 hover:text-red-800"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ quizSet.name }}</h3>
                <p class="text-gray-600 mb-4">{{ quizSet.description }}</p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>{{ quizSet.question_count }} questions</span>
                    <button 
                        @click="viewQuestions(quizSet)"
                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Manage Questions
                    </button>
                </div>
            </div>
        </div>

        <!-- Questions Management -->
        <div v-if="selectedSet" class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Questions for {{ selectedSet.name }}</h2>
                    <p class="text-gray-600 mt-1">Total: {{ quizzes.length }} questions</p>
                </div>
                <div class="flex space-x-3">
                    <button 
                        @click="selectedSet = null; quizzes = []"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
                    >
                        Back to Sets
                    </button>
                    <button 
                        @click="showAddQuestionModal = true"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600"
                    >
                        Add Question
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div 
                    v-for="(quiz, index) in quizzes" 
                    :key="quiz.id"
                    class="border border-gray-200 rounded-lg p-4"
                >
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-800">Q{{ index + 1 }}: {{ quiz.question }}</h3>
                        <div class="flex space-x-2">
                            <button 
                                @click="editQuestion(quiz)"
                                class="p-1 text-blue-600 hover:text-blue-800"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button 
                                @click="deleteQuestion(quiz.id)"
                                class="p-1 text-red-600 hover:text-red-800"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div 
                            v-for="(option, optIndex) in quiz.options" 
                            :key="optIndex"
                            class="p-2 border border-gray-200 rounded"
                            :class="{'bg-green-100 border-green-500': option === quiz.correct_answer}"
                        >
                            <span class="font-medium">{{ String.fromCharCode(65 + optIndex) }}.</span> {{ option }}
                            <span v-if="option === quiz.correct_answer" class="ml-2 text-green-600 text-sm">✓ Correct</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state for questions -->
            <div v-if="quizzes.length === 0" class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No questions yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first question.</p>
            </div>
        </div>

        <!-- Add/Edit Quiz Set Modal -->
        <div v-if="showAddSetModal || showEditSetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">
                    {{ showEditSetModal ? 'Edit Quiz Set' : 'Add New Quiz Set' }}
                </h3>
                <form @submit.prevent="saveQuizSet">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input 
                                v-model="quizSetForm.name"
                                type="text" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <input 
                                v-model="quizSetForm.category"
                                type="text" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea 
                                v-model="quizSetForm.description"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            ></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit (seconds)</label>
                            <input 
                                v-model.number="quizSetForm.time_limit"
                                type="number" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question Count</label>
                            <input 
                                v-model.number="quizSetForm.question_count"
                                type="number" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button 
                            type="button"
                            @click="closeSetModal"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                        >
                            {{ showEditSetModal ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add/Edit Question Modal -->
        <div v-if="showAddQuestionModal || showEditQuestionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-semibold mb-4">
                    {{ showEditQuestionModal ? 'Edit Question' : 'Add New Question' }}
                </h3>
                <form @submit.prevent="saveQuestion">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                            <textarea 
                                v-model="questionForm.question"
                                rows="3"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            ></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div v-for="(_, index) in 4" :key="index">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Option {{ String.fromCharCode(65 + index) }}
                                </label>
                                <input 
                                    v-model="questionForm.options[index]"
                                    type="text" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correct Answer</label>
                            <select 
                                v-model="questionForm.correct_answer"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Select correct option</option>
                                <option 
                                    v-for="(option, index) in questionForm.options" 
                                    :key="index"
                                    :value="option"
                                >
                                    {{ String.fromCharCode(65 + index) }}: {{ option }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                            <input 
                                v-model.number="questionForm.points"
                                type="number" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button 
                            type="button"
                            @click="closeQuestionModal"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                        >
                            {{ showEditQuestionModal ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</template>

<script>
export default {
    name: 'ManageQuizzes',
    data() {
        return {
            quizSets: [],
            selectedSet: null,
            quizzes: [],
            showAddSetModal: false,
            showEditSetModal: false,
            showAddQuestionModal: false,
            showEditQuestionModal: false,
            quizSetForm: {
                name: '',
                category: '',
                description: '',
                time_limit: 60,
                question_count: 10
            },
            questionForm: {
                question: '',
                options: ['', '', '', ''],
                correct_answer: '',
                points: 1
            },
            editingId: null
        }
    },
    async mounted() {
        await this.fetchQuizSets();
    },
    methods: {
        async fetchQuizSets() {
            try {
                const response = await fetch('/api/quiz-sets');
                this.quizSets = await response.json();
            } catch (error) {
                console.error('Error fetching quiz sets:', error);
                alert('Failed to load quiz sets');
            }
        },

        async viewQuestions(quizSet) {
            this.selectedSet = quizSet;
            try {
                const response = await fetch(`/api/quiz-sets/${quizSet.id}/quizzes`);
                this.quizzes = await response.json();
            } catch (error) {
                console.error('Error fetching questions:', error);
                alert('Failed to load questions');
            }
        },

        editQuizSet(quizSet) {
            this.quizSetForm = { ...quizSet };
            this.editingId = quizSet.id;
            this.showEditSetModal = true;
        },

        async deleteQuizSet(id) {
            if (!confirm('Are you sure you want to delete this quiz set?')) return;

            try {
                const response = await fetch(`/api/admin/quiz-sets/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Admin-Key': 'admin123',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    await this.fetchQuizSets();
                    if (this.selectedSet && this.selectedSet.id === id) {
                        this.selectedSet = null;
                        this.quizzes = [];
                    }
                } else {
                    throw new Error('Failed to delete quiz set');
                }
            } catch (error) {
                console.error('Error deleting quiz set:', error);
                alert('Failed to delete quiz set');
            }
        },

        async saveQuizSet() {
            try {
                const url = this.showEditSetModal 
                    ? `/api/admin/quiz-sets/${this.editingId}`
                    : '/api/admin/quiz-sets';
                
                const method = this.showEditSetModal ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-Admin-Key': 'admin123',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.quizSetForm)
                });

                if (response.ok) {
                    await this.fetchQuizSets();
                    this.closeSetModal();
                } else {
                    throw new Error('Failed to save quiz set');
                }
            } catch (error) {
                console.error('Error saving quiz set:', error);
                alert('Failed to save quiz set');
            }
        },

        closeSetModal() {
            this.showAddSetModal = false;
            this.showEditSetModal = false;
            this.quizSetForm = {
                name: '',
                category: '',
                description: '',
                time_limit: 60,
                question_count: 10
            };
            this.editingId = null;
        },

        editQuestion(quiz) {
            this.questionForm = {
                question: quiz.question,
                options: [...quiz.options],
                correct_answer: quiz.correct_answer,
                points: quiz.points
            };
            this.editingId = quiz.id;
            this.showEditQuestionModal = true;
        },

        async deleteQuestion(id) {
            if (!confirm('Are you sure you want to delete this question?')) return;

            try {
                const response = await fetch(`/api/admin/quizzes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Admin-Key': 'admin123',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    await this.viewQuestions(this.selectedSet);
                } else {
                    throw new Error('Failed to delete question');
                }
            } catch (error) {
                console.error('Error deleting question:', error);
                alert('Failed to delete question');
            }
        },

        async saveQuestion() {
            try {
                const formData = {
                    ...this.questionForm,
                    quiz_set_id: this.selectedSet.id
                };

                const url = this.showEditQuestionModal 
                    ? `/api/admin/quizzes/${this.editingId}`
                    : '/api/admin/quizzes';
                
                const method = this.showEditQuestionModal ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-Admin-Key': 'admin123',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    await this.viewQuestions(this.selectedSet);
                    this.closeQuestionModal();
                } else {
                    throw new Error('Failed to save question');
                }
            } catch (error) {
                console.error('Error saving question:', error);
                alert('Failed to save question');
            }
        },

        closeQuestionModal() {
            this.showAddQuestionModal = false;
            this.showEditQuestionModal = false;
            this.questionForm = {
                question: '',
                options: ['', '', '', ''],
                correct_answer: '',
                points: 1
            };
            this.editingId = null;
        }
    }
}
</script>