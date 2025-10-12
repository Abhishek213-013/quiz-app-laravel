<template>
  <div class="min-h-screen" :class="isDark ? 'dark-theme' : 'light-theme'">
    <AdminNavbar 
      title="Manage Quizzes"
      :is-dark="isDark"
      @toggle-theme="toggleTheme"
      @toggle-mobile-sidebar="toggleMobileSidebar"
      @logout="handleLogout"
    />

    <div class="flex">
      <AdminSidebar 
        :mobile-sidebar="mobileSidebar"
        current-page="/admin/quizzes"
        @close-mobile-sidebar="toggleMobileSidebar"
      />

      <!-- Main Content -->
      <div class="main-content">
        <!-- Content -->
        <main class="content">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <i class="fas fa-spinner fa-spin loading-icon"></i>
            <p class="loading-text">Loading quizzes data...</p>
          </div>

          <!-- Main Content -->
          <div v-else>
            <!-- Quick Stats Section -->
            <div class="stats-section">
              <div class="flex justify-between items-center mb-8">
                <h2 class="section-title">Quiz Management Overview</h2>
                <button @click="refreshData" class="refresh-btn" :disabled="refreshing">
                  <i class="fas fa-sync-alt" :class="{ 'fa-spin': refreshing }"></i>
                  {{ refreshing ? 'Refreshing...' : 'Refresh Data' }}
                </button>
              </div>
              <div class="stats-grid">
                <div class="stat-card blue">
                  <div class="stat-number">{{ quizSets.length }}</div>
                  <div class="stat-label">Total Quiz Sets</div>
                </div>
                <div class="stat-card green">
                  <div class="stat-number">{{ totalQuestions }}</div>
                  <div class="stat-label">Total Questions</div>
                </div>
                <div class="stat-card purple">
                  <div class="stat-number">{{ multipleChoiceCount }}</div>
                  <div class="stat-label">Multiple Choice</div>
                </div>
                <div class="stat-card orange">
                  <div class="stat-number">{{ briefAnswerCount }}</div>
                  <div class="stat-label">Brief Answers</div>
                </div>
              </div>
            </div>

            <!-- Rest of the template remains exactly the same -->
            <!-- Error Alert -->
            <div v-if="errorMessage" class="content-card mb-6">
              <div class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mr-3 text-lg"></i>
                <div class="flex-1">
                  <h3 class="text-sm font-semibold text-red-800 dark:text-red-300">Error</h3>
                  <p class="text-sm text-red-700 dark:text-red-400 mt-1">{{ errorMessage }}</p>
                </div>
                <button @click="errorMessage = ''" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <!-- Success Alert -->
            <div v-if="successMessage" class="content-card mb-6">
              <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-3 text-lg"></i>
                <div class="flex-1">
                  <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">Success</h3>
                  <p class="text-sm text-green-700 dark:text-green-400 mt-1">{{ successMessage }}</p>
                </div>
                <button @click="successMessage = ''" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <!-- Quiz Sets Section -->
            <div class="content-card mb-8">
              <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                  <h2 class="card-title-large">Quiz Sets</h2>
                  <p class="card-subtitle">Create and manage your quiz collections</p>
                </div>
                <button 
                  @click="showAddSetModal = true"
                  class="primary-button"
                  :disabled="loading"
                >
                  <i class="fas fa-plus"></i>
                  Add New Set
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                  v-for="quizSet in quizSets" 
                  :key="quizSet.id"
                  class="quiz-set-card"
                >
                  <div class="flex items-center justify-between mb-4">
                    <span class="category-badge">
                      {{ quizSet.category }}
                    </span>
                    <div class="flex space-x-2">
                      <button 
                        @click="editQuizSet(quizSet)"
                        class="icon-button blue"
                        title="Edit Quiz Set"
                        :disabled="loading"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button 
                        @click="deleteQuizSet(quizSet.id)"
                        class="icon-button red"
                        title="Delete Quiz Set"
                        :disabled="loading"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>
                  <h3 class="quiz-set-title">{{ quizSet.name }}</h3>
                  <p class="quiz-set-description">{{ quizSet.description }}</p>
                  <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center gap-2">
                      <span class="question-count">{{ quizSet.question_count }} questions</span>
                      <span class="time-badge">{{ quizSet.time_limit }}s</span>
                    </div>
                    <button 
                      @click="viewQuestions(quizSet)"
                      class="secondary-button"
                      :disabled="loading"
                    >
                      Manage Questions
                    </button>
                  </div>
                </div>
              </div>

              <!-- Empty state for quiz sets -->
              <div v-if="quizSets.length === 0 && !loading" class="empty-state">
                <div class="empty-content">
                  <i class="fas fa-clipboard-list empty-icon"></i>
                  <p class="empty-title">No quiz sets yet</p>
                  <p class="empty-subtitle">Get started by creating your first quiz set</p>
                  <button 
                    @click="showAddSetModal = true"
                    class="primary-button"
                    :disabled="loading"
                  >
                    Create Your First Set
                  </button>
                </div>
              </div>
            </div>

            <!-- Questions Management -->
            <div v-if="selectedSet" class="content-card">
              <div class="flex items-center justify-between mb-6">
                <div>
                  <h2 class="card-title-large">Questions for {{ selectedSet.name }}</h2>
                  <p class="card-subtitle">Total: {{ quizzes.length }} questions</p>
                </div>
                <div class="flex space-x-3">
                  <button 
                    @click="selectedSet = null; quizzes = []"
                    class="secondary-button gray"
                    :disabled="loading"
                  >
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Sets
                  </button>
                  <button 
                    @click="showAddQuestionModal = true"
                    class="primary-button"
                    :disabled="loading"
                  >
                    <i class="fas fa-plus mr-2"></i>
                    Add Question
                  </button>
                </div>
              </div>

              <div class="space-y-4">
                <div 
                  v-for="(quiz, index) in quizzes" 
                  :key="quiz.id"
                  class="question-card"
                >
                  <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                      <div class="flex items-center gap-3 mb-2">
                        <span class="question-number">
                          Q{{ index + 1 }}
                        </span>
                        <span class="type-badge">
                          {{ getQuestionTypeLabel(quiz.question_type) }}
                        </span>
                        <span class="points-badge">
                          {{ quiz.points }} point{{ quiz.points !== 1 ? 's' : '' }}
                        </span>
                      </div>
                      <h3 class="question-text">{{ quiz.question }}</h3>
                    </div>
                    <div class="flex space-x-2 ml-4">
                      <button 
                        @click="editQuestion(quiz)"
                        class="icon-button blue"
                        title="Edit Question"
                        :disabled="loading"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button 
                        @click="deleteQuestion(quiz.id)"
                        class="icon-button red"
                        title="Delete Question"
                        :disabled="loading"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Display based on question type -->
                  <div v-if="quiz.question_type === 'multiple_choice'" class="options-grid">
                    <div 
                      v-for="(option, optIndex) in quiz.options" 
                      :key="optIndex"
                      class="option-item"
                      :class="{'correct-option': option === quiz.correct_answer}"
                    >
                      <div class="flex items-center">
                        <span class="option-label">{{ String.fromCharCode(65 + optIndex) }}.</span>
                        <span class="option-text">{{ option }}</span>
                        <span v-if="option === quiz.correct_answer" class="correct-indicator">
                          <i class="fas fa-check-circle"></i>
                        </span>
                      </div>
                    </div>
                  </div>

                  <div v-else-if="quiz.question_type === 'brief_answer'" class="answer-container">
                    <div class="answer-header">
                      <i class="fas fa-key"></i>
                      <span>Correct Answer:</span>
                    </div>
                    <p class="answer-text">{{ quiz.correct_answer }}</p>
                  </div>

                  <div v-else-if="quiz.question_type === 'true_false'" class="true-false-grid">
                    <div 
                      class="tf-option"
                      :class="quiz.correct_answer === 'True' ? 'correct-tf' : ''"
                    >
                      <div class="flex items-center justify-between">
                        <span class="tf-label">True</span>
                        <span v-if="quiz.correct_answer === 'True'" class="correct-indicator">
                          <i class="fas fa-check-circle"></i>
                        </span>
                      </div>
                    </div>
                    <div 
                      class="tf-option"
                      :class="quiz.correct_answer === 'False' ? 'correct-tf' : ''"
                    >
                      <div class="flex items-center justify-between">
                        <span class="tf-label">False</span>
                        <span v-if="quiz.correct_answer === 'False'" class="correct-indicator">
                          <i class="fas fa-check-circle"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Empty state for questions -->
              <div v-if="quizzes.length === 0 && !loading" class="empty-state">
                <div class="empty-content">
                  <i class="fas fa-question-circle empty-icon"></i>
                  <p class="empty-title">No questions yet</p>
                  <p class="empty-subtitle">Get started by adding your first question</p>
                  <button 
                    @click="showAddQuestionModal = true"
                    class="primary-button"
                    :disabled="loading"
                  >
                    Add First Question
                  </button>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- Add/Edit Quiz Set Modal -->
    <div v-if="showAddSetModal || showEditSetModal" class="modal-overlay">
      <div class="modal-container">
        <h3 class="modal-title">
          {{ showEditSetModal ? 'Edit Quiz Set' : 'Add New Quiz Set' }}
        </h3>
        <form @submit.prevent="saveQuizSet">
          <div class="space-y-4">
            <div>
              <label class="form-label">Name</label>
              <input 
                v-model="quizSetForm.name"
                type="text" 
                required
                class="form-input"
                placeholder="Enter quiz set name"
                :disabled="loading"
              >
            </div>
            <div>
              <label class="form-label">Category</label>
              <input 
                v-model="quizSetForm.category"
                type="text" 
                required
                class="form-input"
                placeholder="e.g., Science, History, Geography"
                :disabled="loading"
              >
            </div>
            <div>
              <label class="form-label">Description</label>
              <textarea 
                v-model="quizSetForm.description"
                rows="3"
                class="form-textarea"
                placeholder="Describe this quiz set..."
                :disabled="loading"
              ></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="form-label">Time Limit (seconds)</label>
                <input 
                  v-model.number="quizSetForm.time_limit"
                  type="number" 
                  required
                  min="1"
                  class="form-input"
                  :disabled="loading"
                >
              </div>
              <div>
                <label class="form-label">Question Count</label>
                <input 
                  v-model.number="quizSetForm.question_count"
                  type="number" 
                  required
                  min="1"
                  class="form-input"
                  :disabled="loading"
                >
              </div>
            </div>
          </div>
          <div class="modal-actions">
            <button 
              type="button"
              @click="closeSetModal"
              class="secondary-button"
              :disabled="loading"
            >
              Cancel
            </button>
            <button 
              type="submit"
              class="primary-button"
              :disabled="loading"
            >
              <i v-if="loading" class="fas fa-spinner fa-spin"></i>
              {{ showEditSetModal ? 'Update' : 'Create' }} Set
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Add/Edit Question Modal -->
    <div v-if="showAddQuestionModal || showEditQuestionModal" class="modal-overlay">
      <div class="modal-container large">
        <h3 class="modal-title">
          {{ showEditQuestionModal ? 'Edit Question' : 'Add New Question' }}
        </h3>
        <form @submit.prevent="saveQuestion">
          <div class="space-y-6">
            <!-- Question Type Selection -->
            <div>
              <label class="form-label">Question Type</label>
              <div class="type-grid">
                <button
                  type="button"
                  @click="setQuestionType('multiple_choice')"
                  :class="[
                    'type-option',
                    questionForm.question_type === 'multiple_choice' ? 'type-option-selected blue' : 'type-option-default'
                  ]"
                  :disabled="loading"
                >
                  <i class="fas fa-list-ol type-icon"></i>
                  <span class="type-label">Multiple Choice</span>
                </button>
                <button
                  type="button"
                  @click="setQuestionType('brief_answer')"
                  :class="[
                    'type-option',
                    questionForm.question_type === 'brief_answer' ? 'type-option-selected green' : 'type-option-default'
                  ]"
                  :disabled="loading"
                >
                  <i class="fas fa-edit type-icon"></i>
                  <span class="type-label">Brief Answer</span>
                </button>
                <button
                  type="button"
                  @click="setQuestionType('true_false')"
                  :class="[
                    'type-option',
                    questionForm.question_type === 'true_false' ? 'type-option-selected purple' : 'type-option-default'
                  ]"
                  :disabled="loading"
                >
                  <i class="fas fa-check-circle type-icon"></i>
                  <span class="type-label">True/False</span>
                </button>
              </div>
            </div>

            <!-- Question Text -->
            <div>
              <label class="form-label">Question</label>
              <textarea 
                v-model="questionForm.question"
                rows="3"
                required
                class="form-textarea"
                placeholder="Enter your question here..."
                :disabled="loading"
              ></textarea>
            </div>

            <!-- Multiple Choice Options -->
            <div v-if="questionForm.question_type === 'multiple_choice'">
              <label class="form-label">Options</label>
              <div class="options-grid-modal">
                <div v-for="(_, index) in 4" :key="index" class="option-input-container">
                  <label class="option-label-modal">
                    Option {{ String.fromCharCode(65 + index) }}
                  </label>
                  <input 
                    v-model="questionForm.options[index]"
                    type="text" 
                    required
                    class="form-input"
                    :placeholder="`Enter option ${String.fromCharCode(65 + index)}`"
                    :disabled="loading"
                  >
                </div>
              </div>
              <div class="mt-4">
                <label class="form-label">Correct Answer</label>
                <select 
                  v-model="questionForm.correct_answer"
                  required
                  class="form-input"
                  :disabled="loading"
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
            </div>

            <!-- Brief Answer -->
            <div v-else-if="questionForm.question_type === 'brief_answer'">
              <label class="form-label">Correct Answer</label>
              <textarea 
                v-model="questionForm.correct_answer"
                rows="2"
                required
                class="form-textarea"
                placeholder="Enter the correct answer..."
                :disabled="loading"
              ></textarea>
            </div>

            <!-- True/False -->
            <div v-else-if="questionForm.question_type === 'true_false'">
              <label class="form-label">Correct Answer</label>
              <div class="tf-grid-modal">
                <button
                  type="button"
                  @click="questionForm.correct_answer = 'True'"
                  :class="[
                    'tf-option-modal',
                    questionForm.correct_answer === 'True' ? 'tf-option-selected green' : 'tf-option-default'
                  ]"
                  :disabled="loading"
                >
                  <i class="fas fa-check tf-icon"></i>
                  <span class="tf-label-modal">True</span>
                </button>
                <button
                  type="button"
                  @click="questionForm.correct_answer = 'False'"
                  :class="[
                    'tf-option-modal',
                    questionForm.correct_answer === 'False' ? 'tf-option-selected red' : 'tf-option-default'
                  ]"
                  :disabled="loading"
                >
                  <i class="fas fa-times tf-icon"></i>
                  <span class="tf-label-modal">False</span>
                </button>
              </div>
            </div>

            <!-- Points -->
            <div>
              <label class="form-label">Points</label>
              <input 
                v-model.number="questionForm.points"
                type="number" 
                required
                min="1"
                class="form-input"
                :disabled="loading"
              >
            </div>
          </div>
          <div class="modal-actions">
            <button 
              type="button"
              @click="closeQuestionModal"
              class="secondary-button"
              :disabled="loading"
            >
              Cancel
            </button>
            <button 
              type="submit"
              class="primary-button"
              :disabled="loading"
            >
              <i v-if="loading" class="fas fa-spinner fa-spin"></i>
              {{ showEditQuestionModal ? 'Update' : 'Add' }} Question
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import AdminNavbar from './AdminNavbar.vue';
import AdminSidebar from './AdminSidebar.vue';

export default {
  name: 'Quizzes',
  components: {
    AdminNavbar,
    AdminSidebar
  },
  data() {
    return {
      isDark: false,
      mobileSidebar: false,
      loading: false,
      refreshing: false, // Added refreshing state
      errorMessage: '',
      successMessage: '',
      quizSets: [],
      selectedSet: null,
      quizzes: [],
      showAddSetModal: false,
      showEditSetModal: false,
      showAddQuestionModal: false,
      showEditQuestionModal: false,
      editingId: null,
      quizSetForm: {
        name: '',
        category: '',
        description: '',
        time_limit: 60,
        question_count: 10
      },
      questionForm: {
        question: '',
        question_type: 'multiple_choice',
        options: ['', '', '', ''],
        correct_answer: '',
        points: 1
      }
    }
  },
  computed: {
    totalQuestions() {
      return this.quizSets.reduce((total, set) => total + set.question_count, 0)
    },
    multipleChoiceCount() {
      return this.quizzes.filter(q => q.question_type === 'multiple_choice').length
    },
    briefAnswerCount() {
      return this.quizzes.filter(q => q.question_type === 'brief_answer').length
    }
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark
    },
    toggleMobileSidebar() {
      this.mobileSidebar = !this.mobileSidebar
    },
    handleLogout() {
      this.$inertia.post('/admin/logout');
    },
    // Added refreshData method
    async refreshData() {
      this.refreshing = true;
      
      try {
        this.clearMessages();
        await this.fetchQuizSets();
        
        // If we're currently viewing questions for a set, refresh those too
        if (this.selectedSet) {
          await this.viewQuestions(this.selectedSet);
        }
        
        this.showSuccess('Data refreshed successfully');
      } catch (error) {
        this.showError(`Failed to refresh data: ${error.message}`);
      } finally {
        this.refreshing = false;
      }
    },
    clearMessages() {
      this.errorMessage = ''
      this.successMessage = ''
    },
    showError(message) {
      this.errorMessage = message
      setTimeout(() => {
        this.errorMessage = ''
      }, 5000)
    },
    showSuccess(message) {
      this.successMessage = message
      setTimeout(() => {
        this.successMessage = ''
      }, 3000)
    },
    getQuestionTypeLabel(type) {
      const labels = {
        'multiple_choice': 'Multiple Choice',
        'brief_answer': 'Brief Answer',
        'true_false': 'True/False'
      }
      return labels[type] || type
    },
    setQuestionType(type) {
      this.questionForm.question_type = type
      this.questionForm.correct_answer = ''
      if (type === 'multiple_choice') {
        this.questionForm.options = ['', '', '', '']
      }
    },
    async apiRequest(url, options = {}) {
      const defaultHeaders = {
        'X-Admin-Key': 'admin123',
        'Content-Type': 'application/json',
        ...options.headers
      }

      try {
        console.log(`Making API request to: ${url}`, options)
        
        const response = await fetch(url, { ...options, headers: defaultHeaders })
        
        console.log(`Response status: ${response.status}`, response)
        
        const responseText = await response.text()
        console.log('Raw response:', responseText)
        
        if (!response.ok) {
          let errorData
          try {
            errorData = JSON.parse(responseText)
          } catch {
            errorData = { 
              error: `HTTP ${response.status}: ${response.statusText}`,
              rawResponse: responseText.substring(0, 200)
            }
          }
          throw new Error(errorData.error || 'Request failed')
        }

        try {
          return JSON.parse(responseText)
        } catch (parseError) {
          console.error('Failed to parse JSON response:', parseError)
          throw new Error('Invalid JSON response from server')
        }
      } catch (error) {
        console.error('API Request failed:', error)
        throw error
      }
    },
    async fetchQuizSets() {
      try {
        this.loading = true
        this.clearMessages()
        
        const data = await this.apiRequest('/api/admin-api/quiz-sets')
        this.quizSets = data
        
      } catch (error) {
        this.showError(`Failed to load quiz sets: ${error.message}`)
      } finally {
        this.loading = false
      }
    },
    async viewQuestions(quizSet) {
      this.selectedSet = quizSet
      try {
        this.loading = true
        this.clearMessages()
        
        const data = await this.apiRequest(`/api/quiz-sets/${quizSet.id}/quizzes`)
        this.quizzes = data
        
      } catch (error) {
        this.showError(`Failed to load questions: ${error.message}`)
      } finally {
        this.loading = false
      }
    },
    editQuizSet(quizSet) {
      this.quizSetForm = { ...quizSet }
      this.editingId = quizSet.id
      this.showEditSetModal = true
    },
    async deleteQuizSet(id) {
      if (!confirm('Are you sure you want to delete this quiz set? This will also delete all questions in this set.')) return

      try {
        this.loading = true
        this.clearMessages()
        
        await this.apiRequest(`/api/admin-api/quiz-sets/${id}`, { method: 'DELETE' })
        
        await this.fetchQuizSets()
        if (this.selectedSet && this.selectedSet.id === id) {
          this.selectedSet = null
          this.quizzes = []
        }
        
        this.showSuccess('Quiz set deleted successfully')
      } catch (error) {
        this.showError(`Failed to delete quiz set: ${error.message}`)
      } finally {
        this.loading = false
      }
    },
    async saveQuizSet() {
      try {
        this.loading = true
        this.clearMessages()
        
        const url = this.showEditSetModal 
          ? `/api/admin-api/quiz-sets/${this.editingId}`
          : '/api/admin-api/quiz-sets'
        
        const method = this.showEditSetModal ? 'PUT' : 'POST'

        await this.apiRequest(url, {
          method: method,
          body: JSON.stringify(this.quizSetForm)
        })

        await this.fetchQuizSets()
        this.closeSetModal()
        
        this.showSuccess(`Quiz set ${this.showEditSetModal ? 'updated' : 'created'} successfully`)
      } catch (error) {
        this.showError(`Failed to save quiz set: ${error.message}`)
      } finally {
        this.loading = false
      }
    },
    closeSetModal() {
      this.showAddSetModal = false
      this.showEditSetModal = false
      this.quizSetForm = {
        name: '',
        category: '',
        description: '',
        time_limit: 60,
        question_count: 10
      }
      this.editingId = null
    },
    editQuestion(quiz) {
      this.questionForm = {
        question: quiz.question,
        question_type: quiz.question_type,
        options: [...(quiz.options || ['', '', '', ''])],
        correct_answer: quiz.correct_answer,
        points: quiz.points
      }
      this.editingId = quiz.id
      this.showEditQuestionModal = true
    },
    async deleteQuestion(id) {
      if (!confirm('Are you sure you want to delete this question?')) return

      try {
        this.loading = true
        this.clearMessages()
        
        await this.apiRequest(`/api/admin-api/quizzes/${id}`, { method: 'DELETE' })
        await this.viewQuestions(this.selectedSet)
        
        this.showSuccess('Question deleted successfully')
      } catch (error) {
        this.showError(`Failed to delete question: ${error.message}`)
      } finally {
        this.loading = false
      }
    },
    async saveQuestion() {
      try {
        this.loading = true
        this.clearMessages()
        
        const formData = {
          ...this.questionForm,
          quiz_set_id: this.selectedSet.id
        }

        if (formData.question_type !== 'multiple_choice') {
          delete formData.options
        }

        const url = this.showEditQuestionModal 
          ? `/api/admin-api/quizzes/${this.editingId}`
          : '/api/admin-api/quizzes'
        
        const method = this.showEditQuestionModal ? 'PUT' : 'POST'

        await this.apiRequest(url, {
          method: method,
          body: JSON.stringify(formData)
        })

        await this.viewQuestions(this.selectedSet)
        this.closeQuestionModal()
        
        this.showSuccess(`Question ${this.showEditQuestionModal ? 'updated' : 'added'} successfully`)
      } catch (error) {
        this.showError(`Failed to save question: ${error.message}`)
      } finally {
        this.loading = false
      }
    },
    closeQuestionModal() {
      this.showAddQuestionModal = false
      this.showEditQuestionModal = false
      this.questionForm = {
        question: '',
        question_type: 'multiple_choice',
        options: ['', '', '', ''],
        correct_answer: '',
        points: 1
      }
      this.editingId = null
    }
  },
  mounted() {
    this.fetchQuizSets()
  }
}
</script>

<style>
/* All existing styles remain exactly the same */

/* Only adding the refresh button styles */
.refresh-btn {
  background-color: var(--bg-secondary);
  color: var(--text-primary);
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  border: 1px solid var(--border-color);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.refresh-btn:hover:not(:disabled) {
  background-color: var(--hover-bg);
}
.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* All other existing styles remain unchanged */
/* Import Font Awesome */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

/* Light Theme */
.light-theme {
  --bg-primary: #f9fafb;
  --bg-secondary: #ffffff;
  --bg-sidebar: #ffffff;
  --text-primary: #111827;
  --text-secondary: #374151;
  --text-muted: #6b7280;
  --border-color: #e5e7eb;
  --hover-bg: #f3f4f6;
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Dark Theme */
.dark-theme {
  --bg-primary: #111827;
  --bg-secondary: #1f2937;
  --bg-sidebar: #1f2937;
  --text-primary: #f9fafb;
  --text-secondary: #e5e7eb;
  --text-muted: #9ca3af;
  --border-color: #374151;
  --hover-bg: #374151;
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
}

/* Base Styles */
.min-h-screen {
  min-height: 100vh;
  background-color: var(--bg-primary);
  transition: all 0.3s ease;
}

.main-content {
  flex: 1;
  min-width: 0;
}

.content {
  padding: 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 3rem 0;
}

.loading-icon {
  font-size: 1.875rem;
  color: #2563eb;
  margin-bottom: 1rem;
}
.dark-theme .loading-icon {
  color: #60a5fa;
}

.loading-text {
  color: var(--text-muted);
}

/* Stats Section */
.stats-section {
  max-width: 56rem;
  margin: 0 auto 3rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  text-align: center;
  color: var(--text-primary);
  margin-bottom: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}
@media (min-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.stat-card {
  border-radius: 0.5rem;
  padding: 1.5rem;
  text-align: center;
}
.stat-card.blue {
  background-color: #dbeafe;
}
.dark-theme .stat-card.blue {
  background-color: #1e3a8a;
}
.stat-card.green {
  background-color: #dcfce7;
}
.dark-theme .stat-card.green {
  background-color: #14532d;
}
.stat-card.purple {
  background-color: #f3e8ff;
}
.dark-theme .stat-card.purple {
  background-color: #581c87;
}
.stat-card.orange {
  background-color: #fed7aa;
}
.dark-theme .stat-card.orange {
  background-color: #7c2d12;
}

.stat-number {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}
.stat-card.blue .stat-number {
  color: #2563eb;
}
.dark-theme .stat-card.blue .stat-number {
  color: #60a5fa;
}
.stat-card.green .stat-number {
  color: #16a34a;
}
.dark-theme .stat-card.green .stat-number {
  color: #4ade80;
}
.stat-card.purple .stat-number {
  color: #7c3aed;
}
.dark-theme .stat-card.purple .stat-number {
  color: #a855f7;
}
.stat-card.orange .stat-number {
  color: #ea580c;
}
.dark-theme .stat-card.orange .stat-number {
  color: #fdba74;
}

.stat-label {
  color: var(--text-muted);
}

/* Content Cards */
.content-card {
  background-color: var(--bg-secondary);
  border-radius: 0.5rem;
  box-shadow: var(--shadow-lg);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.card-title-large {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.card-subtitle {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
}

/* Buttons */
.primary-button {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.primary-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-1px);
}
.primary-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.secondary-button {
  padding: 0.75rem 1.5rem;
  background-color: #3b82f6;
  color: white;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.secondary-button:hover:not(:disabled) {
  background-color: #2563eb;
}
.secondary-button.gray {
  background-color: #6b7280;
}
.secondary-button.gray:hover:not(:disabled) {
  background-color: #4b5563;
}
.secondary-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.icon-button {
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}
.icon-button.blue {
  color: #3b82f6;
}
.icon-button.blue:hover:not(:disabled) {
  background-color: #dbeafe;
}
.icon-button.red {
  color: #ef4444;
}
.icon-button.red:hover:not(:disabled) {
  background-color: #fef2f2;
}
.icon-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Quiz Set Cards */
.quiz-set-card {
  background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--hover-bg) 100%);
  border-radius: 0.75rem;
  padding: 1.5rem;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}
.quiz-set-card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

.category-badge {
  padding: 0.25rem 0.75rem;
  background-color: #dbeafe;
  color: #1e40af;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 9999px;
}
.dark-theme .category-badge {
  background-color: #1e3a8a;
  color: #93c5fd;
}

.quiz-set-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.quiz-set-description {
  color: var(--text-muted);
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.question-count {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
}

.time-badge {
  padding: 0.25rem 0.5rem;
  background-color: var(--hover-bg);
  color: var(--text-muted);
  font-size: 0.75rem;
  border-radius: 0.375rem;
}

/* Question Cards */
.question-card {
  border: 1px solid var(--border-color);
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s ease;
}
.question-card:hover {
  border-color: #3b82f6;
}

.question-number {
  padding: 0.25rem 0.75rem;
  background-color: #dbeafe;
  color: #1e40af;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 9999px;
}

.type-badge {
  padding: 0.25rem 0.5rem;
  background-color: var(--hover-bg);
  color: var(--text-muted);
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 0.375rem;
}

.points-badge {
  padding: 0.25rem 0.5rem;
  background-color: #dcfce7;
  color: #166534;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 0.375rem;
}
.dark-theme .points-badge {
  background-color: #14532d;
  color: #4ade80;
}

.question-text {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
}

/* Options Grid */
.options-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.option-item {
  padding: 1rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  transition: all 0.2s ease;
}
.option-item.correct-option {
  background-color: #dcfce7;
  border-color: #22c55e;
}
.dark-theme .option-item.correct-option {
  background-color: #14532d;
  border-color: #4ade80;
}

.option-label {
  font-weight: 600;
  color: var(--text-primary);
  margin-right: 0.75rem;
}

.option-text {
  flex: 1;
  color: var(--text-primary);
}

.correct-indicator {
  color: #22c55e;
  margin-left: 0.5rem;
}

/* Answer Container */
.answer-container {
  background-color: var(--hover-bg);
  padding: 1rem;
  border-radius: 0.5rem;
}

.answer-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.answer-text {
  background-color: var(--bg-secondary);
  padding: 0.75rem;
  border-radius: 0.375rem;
  border: 1px solid var(--border-color);
  color: var(--text-primary);
}

/* True/False Grid */
.true-false-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.tf-option {
  padding: 1rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  transition: all 0.2s ease;
}
.tf-option.correct-tf {
  background-color: #dcfce7;
  border-color: #22c55e;
}
.dark-theme .tf-option.correct-tf {
  background-color: #14532d;
  border-color: #4ade80;
}

.tf-label {
  font-weight: 600;
  color: var(--text-primary);
}

/* Empty States */
.empty-state {
  padding: 3rem 1.5rem;
  text-align: center;
}

.empty-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  color: var(--border-color);
}

.empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.empty-subtitle {
  font-size: 0.875rem;
  margin-bottom: 1.5rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  z-index: 50;
}

.modal-container {
  background-color: var(--bg-secondary);
  border-radius: 1rem;
  padding: 1.5rem;
  width: 100%;
  max-width: 28rem;
  max-height: 90vh;
  overflow-y: auto;
}
.modal-container.large {
  max-width: 42rem;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.form-input, .form-textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  background-color: var(--bg-primary);
  color: var(--text-primary);
  transition: all 0.2s ease;
}
.form-input:focus, .form-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.form-input:disabled, .form-textarea:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

/* Type Selection Grid */
.type-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem;
}

.type-option {
  padding: 1rem;
  border: 2px solid;
  border-radius: 0.75rem;
  text-align: center;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}
.type-option-default {
  border-color: var(--border-color);
  color: var(--text-muted);
}
.type-option-default:hover:not(:disabled) {
  border-color: var(--text-muted);
}
.type-option-selected.blue {
  border-color: #3b82f6;
  background-color: #dbeafe;
  color: #1e40af;
}
.dark-theme .type-option-selected.blue {
  background-color: #1e3a8a;
  color: #93c5fd;
}
.type-option-selected.green {
  border-color: #10b981;
  background-color: #dcfce7;
  color: #047857;
}
.dark-theme .type-option-selected.green {
  background-color: #14532d;
  color: #4ade80;
}
.type-option-selected.purple {
  border-color: #8b5cf6;
  background-color: #f3e8ff;
  color: #7c3aed;
}
.dark-theme .type-option-selected.purple {
  background-color: #581c87;
  color: #c4b5fd;
}

.type-icon {
  font-size: 1.5rem;
}

.type-label {
  font-weight: 600;
  font-size: 0.875rem;
}

/* Options Grid Modal */
.options-grid-modal {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.option-input-container {
  display: flex;
  flex-direction: column;
}

.option-label-modal {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

/* True/False Modal */
.tf-grid-modal {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.tf-option-modal {
  padding: 1rem;
  border: 2px solid;
  border-radius: 0.75rem;
  text-align: center;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}
.tf-option-default {
  border-color: var(--border-color);
  color: var(--text-muted);
}
.tf-option-default:hover:not(:disabled) {
  border-color: var(--text-muted);
}
.tf-option-selected.green {
  border-color: #10b981;
  background-color: #dcfce7;
  color: #047857;
}
.dark-theme .tf-option-selected.green {
  background-color: #14532d;
  color: #4ade80;
}
.tf-option-selected.red {
  border-color: #ef4444;
  background-color: #fef2f2;
  color: #dc2626;
}
.dark-theme .tf-option-selected.red {
  background-color: #7f1d1d;
  color: #fca5a5;
}

.tf-icon {
  font-size: 1.5rem;
}

.tf-label-modal {
  font-weight: 600;
  font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .content {
    padding: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .options-grid {
    grid-template-columns: 1fr;
  }
  
  .true-false-grid {
    grid-template-columns: 1fr;
  }
  
  .type-grid {
    grid-template-columns: 1fr;
  }
  
  .options-grid-modal {
    grid-template-columns: 1fr;
  }
  
  .tf-grid-modal {
    grid-template-columns: 1fr;
  }
  
  .modal-actions {
    flex-direction: column;
  }
}
</style>