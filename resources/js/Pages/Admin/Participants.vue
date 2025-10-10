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
              <h1 class="text-xl font-semibold text-gray-800">Participants Management</h1>
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
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Participants Overview</h1>
          <p class="text-lg text-gray-600">View and manage all quiz participants and their performance</p>
        </div>

        <!-- Stats Cards - Enhanced with Dashboard styling -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 text-center shadow-lg border border-blue-200">
            <div class="flex items-center justify-center mb-4">
              <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-white text-xl"></i>
              </div>
            </div>
            <div class="text-3xl font-bold text-blue-700 mb-2">{{ participants.length }}</div>
            <div class="text-sm font-medium text-blue-600">Total Participants</div>
          </div>

          <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 text-center shadow-lg border border-green-200">
            <div class="flex items-center justify-center mb-4">
              <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-white text-xl"></i>
              </div>
            </div>
            <div class="text-3xl font-bold text-green-700 mb-2">{{ totalAttempts }}</div>
            <div class="text-sm font-medium text-green-600">Total Attempts</div>
          </div>

          <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-6 text-center shadow-lg border border-yellow-200">
            <div class="flex items-center justify-center mb-4">
              <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                <i class="fas fa-trophy text-white text-xl"></i>
              </div>
            </div>
            <div class="text-3xl font-bold text-yellow-700 mb-2">{{ averageScore }}%</div>
            <div class="text-sm font-medium text-yellow-600">Avg. Score</div>
          </div>

          <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 text-center shadow-lg border border-purple-200">
            <div class="flex items-center justify-center mb-4">
              <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-white text-xl"></i>
              </div>
            </div>
            <div class="text-3xl font-bold text-purple-700 mb-2">{{ bestScore }}%</div>
            <div class="text-sm font-medium text-purple-600">Best Score</div>
          </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <h2 class="text-xl font-bold text-gray-800 mb-1">All Participants</h2>
              <p class="text-gray-600 text-sm">Manage and analyze participant performance</p>
            </div>
            <div class="relative w-full sm:w-auto">
              <div class="relative">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search participants..."
                  class="pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-80 bg-gray-50 transition-all duration-200"
                >
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Participants Table - Enhanced Styling -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors" @click="sortBy('name')">
                    <div class="flex items-center space-x-2">
                      <span>Participant</span>
                      <i :class="sortColumn === 'name' ? (sortDirection === 'asc' ? 'fas fa-arrow-up text-blue-600' : 'fas fa-arrow-down text-blue-600') : 'fas fa-sort text-gray-400'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors" @click="sortBy('best_score')">
                    <div class="flex items-center space-x-2">
                      <span>Best Score</span>
                      <i :class="sortColumn === 'best_score' ? (sortDirection === 'asc' ? 'fas fa-arrow-up text-blue-600' : 'fas fa-arrow-down text-blue-600') : 'fas fa-sort text-gray-400'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors" @click="sortBy('best_percentage')">
                    <div class="flex items-center space-x-2">
                      <span>Best %</span>
                      <i :class="sortColumn === 'best_percentage' ? (sortDirection === 'asc' ? 'fas fa-arrow-up text-blue-600' : 'fas fa-arrow-down text-blue-600') : 'fas fa-sort text-gray-400'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors" @click="sortBy('total_attempts')">
                    <div class="flex items-center space-x-2">
                      <span>Attempts</span>
                      <i :class="sortColumn === 'total_attempts' ? (sortDirection === 'asc' ? 'fas fa-arrow-up text-blue-600' : 'fas fa-arrow-down text-blue-600') : 'fas fa-sort text-gray-400'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors" @click="sortBy('average_score')">
                    <div class="flex items-center space-x-2">
                      <span>Avg. Score</span>
                      <i :class="sortColumn === 'average_score' ? (sortDirection === 'asc' ? 'fas fa-arrow-up text-blue-600' : 'fas fa-arrow-down text-blue-600') : 'fas fa-sort text-gray-400'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition-colors" @click="sortBy('last_attempt')">
                    <div class="flex items-center space-x-2">
                      <span>Last Activity</span>
                      <i :class="sortColumn === 'last_attempt' ? (sortDirection === 'asc' ? 'fas fa-arrow-up text-blue-600' : 'fas fa-arrow-down text-blue-600') : 'fas fa-sort text-gray-400'"></i>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr 
                  v-for="participant in filteredParticipants" 
                  :key="participant.name" 
                  class="hover:bg-blue-50 transition-all duration-200 group"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold shadow-md mr-4 group-hover:scale-110 transition-transform duration-200">
                        {{ getInitials(participant.name) }}
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">{{ participant.name }}</div>
                        <div class="text-xs text-gray-500">Active Participant</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ participant.best_score }}/15</div>
                    <div class="text-xs text-gray-500">Questions</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getPercentageClass(participant.best_percentage)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm transition-all duration-200">
                      {{ participant.best_percentage }}%
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="text-sm font-semibold text-gray-900 mr-2">{{ participant.total_attempts }}</div>
                      <div class="w-16 bg-gray-200 rounded-full h-2">
                        <div 
                          class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                          :style="{ width: Math.min(100, (participant.total_attempts / Math.max(...props.participants.map(p => p.total_attempts))) * 100) + '%' }"
                        ></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getPercentageClass(participant.average_score)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm transition-all duration-200">
                      {{ participant.average_score }}%
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-medium">{{ formatDate(participant.last_attempt) }}</div>
                    <div class="text-xs text-gray-500">Last attempt</div>
                  </td>
                </tr>
                <tr v-if="filteredParticipants.length === 0">
                  <td colspan="6" class="px-6 py-8 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                      <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                      <p class="text-lg font-medium mb-1">No participants found</p>
                      <p class="text-sm">Try adjusting your search criteria</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Enhanced Footer -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
              <div class="text-sm text-gray-700 font-medium">
                Showing <span class="text-blue-600 font-bold">{{ filteredParticipants.length }}</span> of 
                <span class="text-blue-600 font-bold">{{ participants.length }}</span> participants
              </div>
              <div class="flex items-center space-x-2 text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500"></i>
                <span>Sorted by {{ sortColumn.replace('_', ' ') }} ({{ sortDirection }})</span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

// Props
const props = defineProps({
  participants: {
    type: Array,
    default: () => []
  }
})

// Reactive data
const search = ref('')
const sortColumn = ref('total_attempts')
const sortDirection = ref('desc')
const mobileSidebar = ref(false)

// Computed properties
const filteredParticipants = computed(() => {
  let filtered = props.participants.filter(participant => 
    participant.name.toLowerCase().includes(search.value.toLowerCase())
  )

  // Sort the filtered results
  filtered.sort((a, b) => {
    let aValue = a[sortColumn.value]
    let bValue = b[sortColumn.value]

    if (sortColumn.value === 'last_attempt') {
      aValue = new Date(aValue)
      bValue = new Date(bValue)
    }

    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1
    return 0
  })

  return filtered
})

const totalAttempts = computed(() => {
  return props.participants.reduce((sum, participant) => sum + participant.total_attempts, 0)
})

const averageScore = computed(() => {
  if (props.participants.length === 0) return 0
  const total = props.participants.reduce((sum, participant) => sum + participant.average_score, 0)
  return Math.round(total / props.participants.length)
})

const bestScore = computed(() => {
  if (props.participants.length === 0) return 0
  return Math.max(...props.participants.map(p => p.best_percentage))
})

// Methods
const sortBy = (column) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column
    sortDirection.value = 'desc'
  }
}

const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
}

const getPercentageClass = (percentage) => {
  if (percentage >= 80) return 'bg-green-100 text-green-800 border border-green-200 hover:bg-green-200'
  if (percentage >= 60) return 'bg-yellow-100 text-yellow-800 border border-yellow-200 hover:bg-yellow-200'
  return 'bg-red-100 text-red-800 border border-red-200 hover:bg-red-200'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  const minutes = Math.floor(diff / 60000)
  
  if (minutes < 60) return `${minutes}m ago`
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours}h ago`
  const days = Math.floor(hours / 24)
  if (days < 7) return `${days}d ago`
  
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const toggleMobileSidebar = () => {
  mobileSidebar.value = !mobileSidebar.value
}

const logout = () => {
  router.post('/admin/logout')
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>