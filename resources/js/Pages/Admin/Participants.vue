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
                  :class="{'bg-blue-50 text-blue-600 font-semibold': activeIs('/admin/dashboard')}">
              <i class="fas fa-home w-5"></i>
              <span>Dashboard</span>
            </Link>
          </li>

          <li>
            <Link href="/admin/participants" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50"
                  :class="{'bg-blue-50 text-blue-600 font-semibold': activeIs('/admin/participants')}">
              <i class="fas fa-users w-5"></i>
              <span>Participants</span>
            </Link>
          </li>

          <li>
            <Link href="/admin/quizzes" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50"
                  :class="{'bg-blue-50 text-blue-600 font-semibold': activeIs('/admin/quizzes')}">
              <i class="fas fa-clipboard-list w-5"></i>
              <span>Manage Quizzes</span>
            </Link>
          </li>

          <li>
            <Link href="/admin/records" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50"
                  :class="{'bg-blue-50 text-blue-600 font-semibold': activeIs('/admin/records')}">
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
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Participants Overview</h1>
          <p class="text-gray-600 mt-2">View and manage all quiz participants and their performance</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-users text-3xl text-blue-600"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Participants</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ participants.length }}</dd>
                </dl>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-chart-line text-3xl text-green-600"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Attempts</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ totalAttempts }}</dd>
                </dl>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-trophy text-3xl text-yellow-600"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Avg. Score</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ averageScore }}%</dd>
                </dl>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-star text-3xl text-purple-600"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Best Score</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ bestScore }}%</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Participants Table -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">All Participants</h2>
              <div class="relative">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search participants..."
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64"
                >
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('name')">
                    <div class="flex items-center space-x-1">
                      <span>Participant Name</span>
                      <i :class="sortColumn === 'name' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-300'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('best_score')">
                    <div class="flex items-center space-x-1">
                      <span>Best Score</span>
                      <i :class="sortColumn === 'best_score' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-300'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('best_percentage')">
                    <div class="flex items-center space-x-1">
                      <span>Best Percentage</span>
                      <i :class="sortColumn === 'best_percentage' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-300'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('total_attempts')">
                    <div class="flex items-center space-x-1">
                      <span>Total Attempts</span>
                      <i :class="sortColumn === 'total_attempts' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-300'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('average_score')">
                    <div class="flex items-center space-x-1">
                      <span>Average Score</span>
                      <i :class="sortColumn === 'average_score' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-300'"></i>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('last_attempt')">
                    <div class="flex items-center space-x-1">
                      <span>Last Attempt</span>
                      <i :class="sortColumn === 'last_attempt' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-300'"></i>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="participant in filteredParticipants" :key="participant.name" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-medium mr-3">
                        {{ getInitials(participant.name) }}
                      </div>
                      <div class="text-sm font-medium text-gray-900">{{ participant.name }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ participant.best_score }}/15</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getPercentageClass(participant.best_percentage)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ participant.best_percentage }}%
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ participant.total_attempts }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getPercentageClass(participant.average_score)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ participant.average_score }}%
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(participant.last_attempt) }}
                  </td>
                </tr>
                <tr v-if="filteredParticipants.length === 0">
                  <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    No participants found matching your search.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ filteredParticipants.length }} of {{ participants.length }} participants
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

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
  if (percentage >= 80) return 'bg-green-100 text-green-800'
  if (percentage >= 60) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const toggleMobileSidebar = () => {
  mobileSidebar.value = !mobileSidebar.value
}

const logout = () => {
  router.post('/admin/logout')
}

const page = usePage()
const activeIs = (path) => {
  return page.url === path
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>