<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r hidden md:flex flex-col sticky top-0 h-screen">
      <div class="px-6 py-5 flex items-center gap-3 border-b">
        <i class="fas fa-brain text-blue-600 text-2xl"></i>
        <span class="font-bold text-lg text-gray-800">Quiz Admin</span>
      </div>

      <nav class="flex-1 overflow-auto px-2 py-4">
        <ul class="space-y-1">
          <li>
            <Link href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50"
                  :class="{'bg-blue-50 text-blue-600 font-semibold': $page.url === '/admin/dashboard'}">
              <i class="fas fa-home w-5"></i>
              <span>Dashboard</span>
            </Link>
          </li>

          <li>
            <Link href="/admin/participants" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50"
                  :class="{'bg-blue-50 text-blue-600 font-semibold': $page.url === '/admin/participants'}">
              <i class="fas fa-users w-5"></i>
              <span>Participants</span>
            </Link>
          </li>

          <li>
            <Link href="/admin/quizzes" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50"
                  :class="{'bg-blue-50 text-blue-600 font-semibold': $page.url === '/admin/quizzes'}">
              <i class="fas fa-clipboard-list w-5"></i>
              <span>Manage Quizzes</span>
            </Link>
          </li>

          <li>
            <Link href="/admin/records" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50"
                  :class="{'bg-blue-50 text-blue-600 font-semibold': $page.url === '/admin/records'}">
              <i class="fas fa-chart-bar w-5"></i>
              <span>Records</span>
            </Link>
          </li>
        </ul>
      </nav>

      <div class="p-4 border-t">
        <div class="text-xs text-gray-400 mb-2">Logged in as</div>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-user text-blue-600 text-sm"></i>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-800">Administrator</div>
            <div class="text-xs text-gray-500">Super Admin</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main panel -->
    <div class="flex-1 min-w-0">
      <!-- Topbar -->
      <header class="bg-white border-b sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-4">
              <!-- Mobile menu button -->
              <button @click="toggleMobileSidebar" class="md:hidden p-2 rounded-md hover:bg-gray-100">
                <i class="fas fa-bars"></i>
              </button>
              <h1 class="text-xl font-semibold text-gray-800">Manage Quizzes</h1>
            </div>

            <div class="flex items-center gap-4">
              <button @click="logout" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-gray-800">
                <i class="fas fa-sign-out-alt"></i>
                <span class="hidden sm:inline">Logout</span>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Mobile sliding sidebar -->
      <transition name="fade">
        <aside v-if="mobileSidebar" class="md:hidden fixed inset-y-0 left-0 w-64 bg-white z-30 border-r p-4 overflow-auto">
          <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
              <i class="fas fa-brain text-blue-600"></i>
              <span class="font-semibold">Quiz Admin</span>
            </div>
            <button @click="toggleMobileSidebar"><i class="fas fa-times"></i></button>
          </div>
          <nav class="space-y-2">
            <Link href="/admin/dashboard" class="block py-2 px-3 rounded hover:bg-gray-50" @click="toggleMobileSidebar">Dashboard</Link>
            <Link href="/admin/participants" class="block py-2 px-3 rounded hover:bg-gray-50" @click="toggleMobileSidebar">Participants</Link>
            <Link href="/admin/quizzes" class="block py-2 px-3 rounded hover:bg-gray-50" @click="toggleMobileSidebar">Manage Quizzes</Link>
            <Link href="/admin/records" class="block py-2 px-3 rounded hover:bg-gray-50" @click="toggleMobileSidebar">Records</Link>
          </nav>
        </aside>
      </transition>

      <!-- Content -->
      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Error Alert -->
        <div v-if="errorMessage" class="mb-6">
          <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center">
              <i class="fas fa-exclamation-circle text-red-600 mr-3 text-lg"></i>
              <div class="flex-1">
                <h3 class="text-sm font-semibold text-red-800">Error</h3>
                <p class="text-sm text-red-700 mt-1">{{ errorMessage }}</p>
              </div>
              <button @click="errorMessage = ''" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Success Alert -->
        <div v-if="successMessage" class="mb-6">
          <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-center">
              <i class="fas fa-check-circle text-green-600 mr-3 text-lg"></i>
              <div class="flex-1">
                <h3 class="text-sm font-semibold text-green-800">Success</h3>
                <p class="text-sm text-green-700 mt-1">{{ successMessage }}</p>
              </div>
              <button @click="successMessage = ''" class="text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Loading Overlay -->
        <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-xl p-6 flex items-center gap-3">
            <i class="fas fa-spinner fa-spin text-blue-600 text-xl"></i>
            <span class="text-gray-700">Processing...</span>
          </div>
        </div>

        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Quizzes</h1>
          <p class="text-lg text-gray-600">Add, edit, or delete quiz sets and questions with different question types</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 text-center shadow-lg border border-blue-200">
            <div class="flex items-center justify-center mb-4">
              <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                <i class="fas fa-clipboard-list text-white text-xl"></i>
              </div>
            </div>
            <div class="text-3xl font-bold text-blue-700 mb-2">{{ quizSets.length }}</div>
            <div class="text-sm font-medium text-blue-600">Total Quiz Sets</div>
          </div>

          <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 text-center shadow-lg border border-green-200">
            <div class="flex items-center justify-center mb-4">
              <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                <i class="fas fa-question-circle text-white text-xl"></i>
              </div>
            </div>
            <div class="text-3xl font-bold text-green-700 mb-2">{{ totalQuestions }}</div>
            <div class="text-sm font-medium text-green-600">Total Questions</div>
          </div>

          <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 text-center shadow-lg border border-purple-200">
            <div class="flex items-center justify-center mb-4">
              <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                <i class="fas fa-list-alt text-white text-xl"></i>
              </div>
            </div>
            <div class="text-3xl font-bold text-purple-700 mb-2">{{ multipleChoiceCount }}</div>
            <div class="text-sm font-medium text-purple-600">Multiple Choice</div>
          </div>

          <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 text-center shadow-lg border border-orange-200">
            <div class="flex items-center justify-center mb-4">
              <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                <i class="fas fa-edit text-white text-xl"></i>
              </div>
            </div>
            <div class="text-3xl font-bold text-orange-700 mb-2">{{ briefAnswerCount }}</div>
            <div class="text-sm font-medium text-orange-600">Brief Answers</div>
          </div>
        </div>

        <!-- Quiz Sets Grid -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
              <h2 class="text-xl font-bold text-gray-800 mb-1">Quiz Sets</h2>
              <p class="text-gray-600 text-sm">Create and manage your quiz collections</p>
            </div>
            <button 
              @click="showAddSetModal = true"
              class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 font-semibold shadow-md transition-all duration-200 flex items-center gap-2"
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
              class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md p-6 border border-gray-200 hover:shadow-lg transition-all duration-200"
            >
              <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                  {{ quizSet.category }}
                </span>
                <div class="flex space-x-2">
                  <button 
                    @click="editQuizSet(quizSet)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                    title="Edit Quiz Set"
                    :disabled="loading"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                  <button 
                    @click="deleteQuizSet(quizSet.id)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Delete Quiz Set"
                    :disabled="loading"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
              <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ quizSet.name }}</h3>
              <p class="text-gray-600 mb-4 text-sm">{{ quizSet.description }}</p>
              <div class="flex items-center justify-between text-sm text-gray-500">
                <div class="flex items-center gap-2">
                  <span class="font-semibold">{{ quizSet.question_count }} questions</span>
                  <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ quizSet.time_limit }}s</span>
                </div>
                <button 
                  @click="viewQuestions(quizSet)"
                  class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium transition-colors"
                  :disabled="loading"
                >
                  Manage Questions
                </button>
              </div>
            </div>
          </div>

          <!-- Empty state for quiz sets -->
          <div v-if="quizSets.length === 0 && !loading" class="text-center py-12">
            <div class="flex flex-col items-center justify-center text-gray-500">
              <i class="fas fa-clipboard-list text-6xl mb-4 text-gray-300"></i>
              <h3 class="text-xl font-medium mb-2">No quiz sets yet</h3>
              <p class="text-sm mb-4">Get started by creating your first quiz set</p>
              <button 
                @click="showAddSetModal = true"
                class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold"
                :disabled="loading"
              >
                Create Your First Set
              </button>
            </div>
          </div>
        </div>

        <!-- Questions Management -->
        <div v-if="selectedSet" class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-gray-800">Questions for {{ selectedSet.name }}</h2>
              <p class="text-gray-600 mt-1">Total: {{ quizzes.length }} questions</p>
            </div>
            <div class="flex space-x-3">
              <button 
                @click="selectedSet = null; quizzes = []"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 font-medium transition-colors"
                :disabled="loading"
              >
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Sets
              </button>
              <button 
                @click="showAddQuestionModal = true"
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 font-medium transition-colors"
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
              class="border border-gray-200 rounded-xl p-6 hover:border-blue-300 transition-colors"
            >
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                      Q{{ index + 1 }}
                    </span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                      {{ getQuestionTypeLabel(quiz.question_type) }}
                    </span>
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">
                      {{ quiz.points }} point{{ quiz.points !== 1 ? 's' : '' }}
                    </span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-800">{{ quiz.question }}</h3>
                </div>
                <div class="flex space-x-2 ml-4">
                  <button 
                    @click="editQuestion(quiz)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                    title="Edit Question"
                    :disabled="loading"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                  <button 
                    @click="deleteQuestion(quiz.id)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Delete Question"
                    :disabled="loading"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>

              <!-- Display based on question type -->
              <div v-if="quiz.question_type === 'multiple_choice'" class="grid grid-cols-2 gap-3">
                <div 
                  v-for="(option, optIndex) in quiz.options" 
                  :key="optIndex"
                  class="p-3 border border-gray-200 rounded-lg"
                  :class="{'bg-green-50 border-green-300': option === quiz.correct_answer}"
                >
                  <div class="flex items-center">
                    <span class="font-semibold text-gray-700 mr-3">{{ String.fromCharCode(65 + optIndex) }}.</span>
                    <span class="flex-1">{{ option }}</span>
                    <span v-if="option === quiz.correct_answer" class="text-green-600 ml-2">
                      <i class="fas fa-check-circle"></i>
                    </span>
                  </div>
                </div>
              </div>

              <div v-else-if="quiz.question_type === 'brief_answer'" class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center gap-2 mb-2">
                  <i class="fas fa-key text-green-600"></i>
                  <span class="font-medium text-gray-700">Correct Answer:</span>
                </div>
                <p class="text-gray-800 bg-white p-3 rounded border">{{ quiz.correct_answer }}</p>
              </div>

              <div v-else-if="quiz.question_type === 'true_false'" class="flex gap-4">
                <div 
                  class="flex-1 p-4 border rounded-lg"
                  :class="quiz.correct_answer === 'True' ? 'bg-green-50 border-green-300' : 'bg-gray-50'"
                >
                  <div class="flex items-center justify-between">
                    <span class="font-semibold">True</span>
                    <span v-if="quiz.correct_answer === 'True'" class="text-green-600">
                      <i class="fas fa-check-circle"></i>
                    </span>
                  </div>
                </div>
                <div 
                  class="flex-1 p-4 border rounded-lg"
                  :class="quiz.correct_answer === 'False' ? 'bg-green-50 border-green-300' : 'bg-gray-50'"
                >
                  <div class="flex items-center justify-between">
                    <span class="font-semibold">False</span>
                    <span v-if="quiz.correct_answer === 'False'" class="text-green-600">
                      <i class="fas fa-check-circle"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty state for questions -->
          <div v-if="quizzes.length === 0 && !loading" class="text-center py-12">
            <div class="flex flex-col items-center justify-center text-gray-500">
              <i class="fas fa-question-circle text-6xl mb-4 text-gray-300"></i>
              <h3 class="text-xl font-medium mb-2">No questions yet</h3>
              <p class="text-sm mb-4">Get started by adding your first question</p>
              <button 
                @click="showAddQuestionModal = true"
                class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold"
                :disabled="loading"
              >
                Add First Question
              </button>
            </div>
          </div>
        </div>

        <!-- Add/Edit Quiz Set Modal -->
        <div v-if="showAddSetModal || showEditSetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">
              {{ showEditSetModal ? 'Edit Quiz Set' : 'Add New Quiz Set' }}
            </h3>
            <form @submit.prevent="saveQuizSet">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                  <input 
                    v-model="quizSetForm.name"
                    type="text" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Enter quiz set name"
                    :disabled="loading"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                  <input 
                    v-model="quizSetForm.category"
                    type="text" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="e.g., Science, History, Geography"
                    :disabled="loading"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea 
                    v-model="quizSetForm.description"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Describe this quiz set..."
                    :disabled="loading"
                  ></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Limit (seconds)</label>
                    <input 
                      v-model.number="quizSetForm.time_limit"
                      type="number" 
                      required
                      min="1"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                      :disabled="loading"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Count</label>
                    <input 
                      v-model.number="quizSetForm.question_count"
                      type="number" 
                      required
                      min="1"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                      :disabled="loading"
                    >
                  </div>
                </div>
              </div>
              <div class="flex justify-end space-x-3 mt-6">
                <button 
                  type="button"
                  @click="closeSetModal"
                  class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors"
                  :disabled="loading"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 font-semibold transition-colors flex items-center gap-2"
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
        <div v-if="showAddQuestionModal || showEditQuestionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">
              {{ showEditQuestionModal ? 'Edit Question' : 'Add New Question' }}
            </h3>
            <form @submit.prevent="saveQuestion">
              <div class="space-y-6">
                <!-- Question Type Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">Question Type</label>
                  <div class="grid grid-cols-3 gap-3">
                    <button
                      type="button"
                      @click="setQuestionType('multiple_choice')"
                      :class="[
                        'p-4 border-2 rounded-xl text-center transition-all duration-200',
                        questionForm.question_type === 'multiple_choice' 
                          ? 'border-blue-500 bg-blue-50 text-blue-700' 
                          : 'border-gray-200 hover:border-gray-300 text-gray-700'
                      ]"
                      :disabled="loading"
                    >
                      <i class="fas fa-list-ol text-xl mb-2 block"></i>
                      <span class="font-medium">Multiple Choice</span>
                    </button>
                    <button
                      type="button"
                      @click="setQuestionType('brief_answer')"
                      :class="[
                        'p-4 border-2 rounded-xl text-center transition-all duration-200',
                        questionForm.question_type === 'brief_answer' 
                          ? 'border-green-500 bg-green-50 text-green-700' 
                          : 'border-gray-200 hover:border-gray-300 text-gray-700'
                      ]"
                      :disabled="loading"
                    >
                      <i class="fas fa-edit text-xl mb-2 block"></i>
                      <span class="font-medium">Brief Answer</span>
                    </button>
                    <button
                      type="button"
                      @click="setQuestionType('true_false')"
                      :class="[
                        'p-4 border-2 rounded-xl text-center transition-all duration-200',
                        questionForm.question_type === 'true_false' 
                          ? 'border-purple-500 bg-purple-50 text-purple-700' 
                          : 'border-gray-200 hover:border-gray-300 text-gray-700'
                      ]"
                      :disabled="loading"
                    >
                      <i class="fas fa-check-circle text-xl mb-2 block"></i>
                      <span class="font-medium">True/False</span>
                    </button>
                  </div>
                </div>

                <!-- Question Text -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                  <textarea 
                    v-model="questionForm.question"
                    rows="3"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Enter your question here..."
                    :disabled="loading"
                  ></textarea>
                </div>

                <!-- Multiple Choice Options -->
                <div v-if="questionForm.question_type === 'multiple_choice'">
                  <label class="block text-sm font-medium text-gray-700 mb-3">Options</label>
                  <div class="grid grid-cols-2 gap-4">
                    <div v-for="(_, index) in 4" :key="index" class="relative">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Option {{ String.fromCharCode(65 + index) }}
                      </label>
                      <input 
                        v-model="questionForm.options[index]"
                        type="text" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        :placeholder="`Enter option ${String.fromCharCode(65 + index)}`"
                        :disabled="loading"
                      >
                    </div>
                  </div>
                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                    <select 
                      v-model="questionForm.correct_answer"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
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
                  <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                  <textarea 
                    v-model="questionForm.correct_answer"
                    rows="2"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Enter the correct answer..."
                    :disabled="loading"
                  ></textarea>
                </div>

                <!-- True/False -->
                <div v-else-if="questionForm.question_type === 'true_false'">
                  <label class="block text-sm font-medium text-gray-700 mb-3">Correct Answer</label>
                  <div class="grid grid-cols-2 gap-4">
                    <button
                      type="button"
                      @click="questionForm.correct_answer = 'True'"
                      :class="[
                        'p-4 border-2 rounded-xl text-center transition-all duration-200',
                        questionForm.correct_answer === 'True' 
                          ? 'border-green-500 bg-green-50 text-green-700' 
                          : 'border-gray-200 hover:border-gray-300 text-gray-700'
                      ]"
                      :disabled="loading"
                    >
                      <i class="fas fa-check text-xl mb-2 block"></i>
                      <span class="font-medium">True</span>
                    </button>
                    <button
                      type="button"
                      @click="questionForm.correct_answer = 'False'"
                      :class="[
                        'p-4 border-2 rounded-xl text-center transition-all duration-200',
                        questionForm.correct_answer === 'False' 
                          ? 'border-red-500 bg-red-50 text-red-700' 
                          : 'border-gray-200 hover:border-gray-300 text-gray-700'
                      ]"
                      :disabled="loading"
                    >
                      <i class="fas fa-times text-xl mb-2 block"></i>
                      <span class="font-medium">False</span>
                    </button>
                  </div>
                </div>

                <!-- Points -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                  <input 
                    v-model.number="questionForm.points"
                    type="number" 
                    required
                    min="1"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    :disabled="loading"
                  >
                </div>
              </div>
              <div class="flex justify-end space-x-3 mt-6">
                <button 
                  type="button"
                  @click="closeQuestionModal"
                  class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors"
                  :disabled="loading"
                >
                  Cancel
                </button>
                <button 
                  type="submit"
                  class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 font-semibold transition-colors flex items-center gap-2"
                  :disabled="loading"
                >
                  <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                  {{ showEditQuestionModal ? 'Update' : 'Add' }} Question
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

// State management
const errorMessage = ref('')
const successMessage = ref('')
const loading = ref(false)
const quizSets = ref([])
const selectedSet = ref(null)
const quizzes = ref([])
const showAddSetModal = ref(false)
const showEditSetModal = ref(false)
const showAddQuestionModal = ref(false)
const showEditQuestionModal = ref(false)
const mobileSidebar = ref(false)
const editingId = ref(null)

// Forms
const quizSetForm = ref({
  name: '',
  category: '',
  description: '',
  time_limit: 60,
  question_count: 10
})

const questionForm = ref({
  question: '',
  question_type: 'multiple_choice',
  options: ['', '', '', ''],
  correct_answer: '',
  points: 1
})

// Computed properties
const totalQuestions = computed(() => {
  return quizSets.value.reduce((total, set) => total + set.question_count, 0)
})

const multipleChoiceCount = computed(() => {
  return quizzes.value.filter(q => q.question_type === 'multiple_choice').length
})

const briefAnswerCount = computed(() => {
  return quizzes.value.filter(q => q.question_type === 'brief_answer').length
})

// Utility functions
const clearMessages = () => {
  errorMessage.value = ''
  successMessage.value = ''
}

const showError = (message) => {
  errorMessage.value = message
  setTimeout(() => {
    errorMessage.value = ''
  }, 5000)
}

const showSuccess = (message) => {
  successMessage.value = message
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

// API helper function
// In your Vue component, replace the apiRequest function with this:
const apiRequest = async (url, options = {}) => {
  const defaultHeaders = {
    'X-Admin-Key': 'admin123',
    'Content-Type': 'application/json',
    ...options.headers
  }

  try {
    console.log(`Making API request to: ${url}`, options)
    
    const response = await fetch(url, { ...options, headers: defaultHeaders })
    
    console.log(`Response status: ${response.status}`, response)
    
    // Get the response text first to see what we're dealing with
    const responseText = await response.text()
    console.log('Raw response:', responseText)
    
    if (!response.ok) {
      // Try to parse as JSON, but if it fails, use the raw text
      let errorData
      try {
        errorData = JSON.parse(responseText)
      } catch {
        errorData = { 
          error: `HTTP ${response.status}: ${response.statusText}`,
          rawResponse: responseText.substring(0, 200) // First 200 chars
        }
      }
      throw new Error(errorData.error || 'Request failed')
    }

    // Parse the successful response
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
}

// Core methods
const toggleMobileSidebar = () => {
  mobileSidebar.value = !mobileSidebar.value
}

const logout = () => {
  router.post('/admin/logout')
}

const getQuestionTypeLabel = (type) => {
  const labels = {
    'multiple_choice': 'Multiple Choice',
    'brief_answer': 'Brief Answer',
    'true_false': 'True/False'
  }
  return labels[type] || type
}

const setQuestionType = (type) => {
  questionForm.value.question_type = type
  questionForm.value.correct_answer = ''
  if (type === 'multiple_choice') {
    questionForm.value.options = ['', '', '', '']
  }
}

// Data fetching
const fetchQuizSets = async () => {
  try {
    loading.value = true
    clearMessages()
    
    const data = await apiRequest('/api/admin-api/quiz-sets')
    quizSets.value = data
    
  } catch (error) {
    showError(`Failed to load quiz sets: ${error.message}`)
  } finally {
    loading.value = false
  }
}

const viewQuestions = async (quizSet) => {
  selectedSet.value = quizSet
  try {
    loading.value = true
    clearMessages()
    
    const data = await apiRequest(`/api/quiz-sets/${quizSet.id}/quizzes`)
    quizzes.value = data
    
  } catch (error) {
    showError(`Failed to load questions: ${error.message}`)
  } finally {
    loading.value = false
  }
}

// Quiz Set operations
const editQuizSet = (quizSet) => {
  quizSetForm.value = { ...quizSet }
  editingId.value = quizSet.id
  showEditSetModal.value = true
}

const deleteQuizSet = async (id) => {
  if (!confirm('Are you sure you want to delete this quiz set? This will also delete all questions in this set.')) return

  try {
    loading.value = true
    clearMessages()
    
    await apiRequest(`/api/admin-api/quiz-sets/${id}`, { method: 'DELETE' })
    
    await fetchQuizSets()
    if (selectedSet.value && selectedSet.value.id === id) {
      selectedSet.value = null
      quizzes.value = []
    }
    
    showSuccess('Quiz set deleted successfully')
  } catch (error) {
    showError(`Failed to delete quiz set: ${error.message}`)
  } finally {
    loading.value = false
  }
}

const saveQuizSet = async () => {
  try {
    loading.value = true
    clearMessages()
    
    const url = showEditSetModal.value 
      ? `/api/admin-api/quiz-sets/${editingId.value}`
      : '/api/admin-api/quiz-sets'
    
    const method = showEditSetModal.value ? 'PUT' : 'POST'

    await apiRequest(url, {
      method: method,
      body: JSON.stringify(quizSetForm.value)
    })

    await fetchQuizSets()
    closeSetModal()
    
    showSuccess(`Quiz set ${showEditSetModal.value ? 'updated' : 'created'} successfully`)
  } catch (error) {
    showError(`Failed to save quiz set: ${error.message}`)
  } finally {
    loading.value = false
  }
}

const closeSetModal = () => {
  showAddSetModal.value = false
  showEditSetModal.value = false
  quizSetForm.value = {
    name: '',
    category: '',
    description: '',
    time_limit: 60,
    question_count: 10
  }
  editingId.value = null
}

// Question operations
const editQuestion = (quiz) => {
  questionForm.value = {
    question: quiz.question,
    question_type: quiz.question_type,
    options: [...(quiz.options || ['', '', '', ''])],
    correct_answer: quiz.correct_answer,
    points: quiz.points
  }
  editingId.value = quiz.id
  showEditQuestionModal.value = true
}

const deleteQuestion = async (id) => {
  if (!confirm('Are you sure you want to delete this question?')) return

  try {
    loading.value = true
    clearMessages()
    
    await apiRequest(`/api/admin-api/quizzes/${id}`, { method: 'DELETE' })
    await viewQuestions(selectedSet.value)
    
    showSuccess('Question deleted successfully')
  } catch (error) {
    showError(`Failed to delete question: ${error.message}`)
  } finally {
    loading.value = false
  }
}

const saveQuestion = async () => {
  try {
    loading.value = true
    clearMessages()
    
    const formData = {
      ...questionForm.value,
      quiz_set_id: selectedSet.value.id
    }

    // For brief answer and true/false, we don't need options array
    if (formData.question_type !== 'multiple_choice') {
      delete formData.options
    }

    const url = showEditQuestionModal.value 
      ? `/api/admin-api/quizzes/${editingId.value}`
      : '/api/admin-api/quizzes'
    
    const method = showEditQuestionModal.value ? 'PUT' : 'POST'

    await apiRequest(url, {
      method: method,
      body: JSON.stringify(formData)
    })

    await viewQuestions(selectedSet.value)
    closeQuestionModal()
    
    showSuccess(`Question ${showEditQuestionModal.value ? 'updated' : 'added'} successfully`)
  } catch (error) {
    showError(`Failed to save question: ${error.message}`)
  } finally {
    loading.value = false
  }
}

const closeQuestionModal = () => {
  showAddQuestionModal.value = false
  showEditQuestionModal.value = false
  questionForm.value = {
    question: '',
    question_type: 'multiple_choice',
    options: ['', '', '', ''],
    correct_answer: '',
    points: 1
  }
  editingId.value = null
}

// Lifecycle
onMounted(() => {
  fetchQuizSets()
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>