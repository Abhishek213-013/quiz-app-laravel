Now in this page in sidebar.
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
              <h1 class="text-xl font-semibold text-gray-800">Admin Dashboard</h1>
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
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
          <p class="text-gray-600">Loading dashboard data...</p>
        </div>

        <!-- Main Content -->
        <div v-else>
          <!-- Quick Stats Section -->
          <div class="max-w-4xl mx-auto mb-12">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Quick Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="bg-blue-50 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ displayStats.total_participants || initialStats.totalParticipants || 0 }}</div>
                <div class="text-gray-600">Total Participants</div>
              </div>
              <div class="bg-green-50 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ displayStats.total_quiz_sets || initialStats.totalQuizSets || 0 }}</div>
                <div class="text-gray-600">Active Quiz Sets</div>
              </div>
              <div class="bg-purple-50 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ displayStats.total_attempts || initialStats.totalAttempts || 0 }}</div>
                <div class="text-gray-600">Total Attempts</div>
              </div>
            </div>
          </div>

          <!-- Charts Section -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Pie Chart -->
            <div class="bg-white p-6 rounded-2xl shadow">
              <h2 class="text-lg font-semibold text-gray-700 mb-4">Quiz Set Participation</h2>
              <canvas id="pieChart"></canvas>
            </div>

            <!-- Bar Chart -->
            <div class="bg-white p-6 rounded-2xl shadow">
              <h2 class="text-lg font-semibold text-gray-700 mb-4">Participants per Day (This Week)</h2>
              <canvas id="barChart"></canvas>
            </div>
          </div>

          <!-- Top Scorers Section -->
          <div class="bg-white rounded-lg shadow-lg p-6 mb-12" v-if="topScorers.length > 0">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Top Scorers This Week</h2>
            <p class="text-sm text-gray-600 mb-6">Highest scores per quiz set</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="scorer in topScorers" :key="scorer.quiz_set_id" class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between mb-3">
                  <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-gray-800 truncate">{{ scorer.quiz_set_name }}</h3>
                    <p class="text-xs text-gray-500">{{ scorer.category }}</p>
                  </div>
                  <div class="text-sm px-2 py-1 rounded-full bg-blue-100 text-blue-800 flex-shrink-0 ml-2">
                    {{ scorer.total_attempts }} attempts
                  </div>
                </div>
                <div class="space-y-2">
                  <div v-for="(participant, index) in scorer.top_participants" :key="participant.participant_name" 
                       class="flex items-center justify-between p-2 rounded hover:bg-gray-100">
                    <div class="flex items-center min-w-0 flex-1">
                      <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-sm font-medium mr-3 flex-shrink-0">
                        {{ index + 1 }}
                      </div>
                      <div class="min-w-0 flex-1">
                        <div class="font-medium text-gray-800 text-sm truncate">{{ participant.participant_name }}</div>
                        <div class="text-xs text-gray-500">{{ participant.score }}/{{ participant.total_questions }}</div>
                      </div>
                    </div>
                    <div class="text-right ml-3 flex-shrink-0">
                      <div class="font-bold text-green-600">{{ Math.round(participant.percentage) }}%</div>
                      <div class="text-xs text-gray-500">{{ formatDate(participant.created_at) }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Quiz Attempts -->
          <div class="bg-white rounded-lg shadow-lg p-6 mb-12" v-if="recentAttempts.length > 0">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Quiz Attempts</h2>
            <p class="text-sm text-gray-600 mb-6">Latest quiz completions</p>
            <div class="space-y-4">
              <div 
                v-for="attempt in recentAttempts" 
                :key="attempt.id" 
                class="flex items-center justify-between p-4 rounded-lg border hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-center min-w-0 flex-1">
                  <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 mr-4">
                    <i class="fas fa-user"></i>
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="font-medium text-gray-800 truncate">{{ attempt.participant_name }}</div>
                    <div class="text-sm text-gray-600">{{ attempt.quiz_set_name }}</div>
                    <div class="text-xs text-gray-500">{{ attempt.category }}</div>
                  </div>
                </div>
                <div class="text-right ml-4 flex-shrink-0">
                  <div class="font-semibold text-gray-800">{{ attempt.score }}/{{ attempt.total_questions }}</div>
                  <div class="text-sm text-gray-500">{{ formatDate(attempt.created_at) }}</div>
                </div>
                <div :class="progressBadgeClass(attempt.percentage)" class="ml-4 px-3 py-1 rounded-full text-sm font-medium flex-shrink-0">
                  {{ Math.round(attempt.percentage) }}%
                </div>
              </div>
            </div>
          </div>

          <!-- Performance Overview -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h2 class="text-2xl font-bold text-gray-800 mb-6">Recent Activity</h2>
              <div class="space-y-4">
                <div 
                  v-for="activity in recentActivities" 
                  :key="activity.id" 
                  class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg"
                >
                  <div :class="['w-10 h-10 rounded-full flex items-center justify-center', activity.bgColor]">
                    <i :class="[activity.icon, 'text-white text-sm']"></i>
                  </div>
                  <div class="flex-1">
                    <p class="text-gray-800 font-medium">{{ activity.message }}</p>
                    <p class="text-gray-500 text-sm">{{ activity.time }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Performance Overview -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h2 class="text-2xl font-bold text-gray-800 mb-6">Performance Overview</h2>
              <div class="space-y-6">
                <div class="text-center">
                  <div class="text-4xl font-bold text-blue-600 mb-2">
                    {{ isNaN(averageScore) ? 0 : averageScore }}%
                  </div>
                  <div class="text-gray-600">Average Score</div>
                </div>
                
                <div>
                  <div class="text-sm text-gray-600 mb-3">Score Distribution</div>
                  <div class="grid grid-cols-3 gap-3 text-sm">
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                      <div class="font-semibold text-green-700 text-lg">{{ topBucketCounts.high }}</div>
                      <div class="text-xs text-gray-500">80%+</div>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 rounded-lg">
                      <div class="font-semibold text-yellow-700 text-lg">{{ topBucketCounts.medium }}</div>
                      <div class="text-xs text-gray-500">60–79%</div>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-lg">
                      <div class="font-semibold text-red-700 text-lg">{{ topBucketCounts.low }}</div>
                      <div class="text-xs text-gray-500">Below 60%</div>
                    </div>
                  </div>
                </div>

                <div class="text-center pt-4">
                  <div class="text-sm text-gray-600">Completion Rate</div>
                  <div class="text-2xl font-bold text-green-600">{{ completionRate }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import Chart from 'chart.js/auto'

// Get initial data from server-side props
const page = usePage()
const initialStats = computed(() => page.props.initialStats || {})

// Data refs
const loading = ref(false) // Start with false since we have initial data
const displayStats = ref({})
const quizSetDistribution = ref([])
const weeklyParticipants = ref({ labels: [], data: [] })
const topScorers = ref([])
const recentAttempts = ref([])

// Initialize with server-side data
if (page.props.initialStats) {
  displayStats.value = {
    total_participants: page.props.initialStats.totalParticipants,
    total_quiz_sets: page.props.initialStats.totalQuizSets,
    total_attempts: page.props.initialStats.totalAttempts
  }
}

// Recent activities data
const recentActivities = ref([
  {
    id: 1,
    message: "New Geography quiz set added",
    time: "2 hours ago",
    icon: "fas fa-globe-americas",
    bgColor: "bg-blue-500"
  },
  {
    id: 2,
    message: "Science & Technology article published",
    time: "5 hours ago",
    icon: "fas fa-flask",
    bgColor: "bg-green-500"
  },
  {
    id: 3,
    message: "History quiz completed by 15 participants",
    time: "1 day ago",
    icon: "fas fa-history",
    bgColor: "bg-purple-500"
  },
  {
    id: 4,
    message: "New World Facts article available",
    time: "2 days ago",
    icon: "fas fa-book-open",
    bgColor: "bg-orange-500"
  }
])

// Fetch data from API
const fetchDashboardData = async () => {
  try {
    loading.value = true
    
    const response = await axios.get('/admin/dashboard-data')
    const data = response.data

    // Check if we got a redirect response (unauthorized)
    if (data.redirect) {
      window.location.href = data.redirect
      return
    }

    displayStats.value = data.stats || {}
    quizSetDistribution.value = data.quizSetDistribution || []
    weeklyParticipants.value = data.weeklyParticipants || { labels: [], data: [] }
    topScorers.value = data.topScorers || []
    recentAttempts.value = data.recentAttempts || []

    // Render charts after a small delay to ensure DOM is ready
    setTimeout(renderCharts, 100)
    
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
    if (error.response?.status === 401) {
      // Redirect to login if unauthorized
      window.location.href = '/admin/login'
    }
  } finally {
    loading.value = false
  }
}

// Computed properties
const averageScore = computed(() => {
  const arr = recentAttempts.value
  if (!arr || arr.length === 0) return 0
  
  let sum = 0
  let count = 0
  
  for (const a of arr) {
    const percentage = a.percentage || 0
    if (percentage && !isNaN(percentage)) {
      sum += parseFloat(percentage)
      count++
    }
  }
  
  if (count === 0) return 0
  return Math.round(sum / count)
})

const completionRate = computed(() => {
  const participants = displayStats.value.total_participants || initialStats.value.totalParticipants || 0
  const attempts = displayStats.value.total_attempts || initialStats.value.totalAttempts || 0
  if (participants === 0 || attempts === 0) return 0
  return Math.min(100, Math.round((attempts / participants) * 100))
})

const topBucketCounts = computed(() => {
  const arr = recentAttempts.value
  if (!arr || arr.length === 0) return { high: 0, medium: 0, low: 0 }
  
  const buckets = { high: 0, medium: 0, low: 0 }
  
  for (const a of arr) {
    const percentage = a.percentage || 0
    const p = parseFloat(percentage) || 0
    
    if (p >= 80) buckets.high++
    else if (p >= 60) buckets.medium++
    else buckets.low++
  }
  
  return buckets
})

// Chart rendering functions
const renderCharts = () => {
  renderPieChart()
  renderBarChart()
}

const renderPieChart = () => {
  const ctx = document.getElementById('pieChart')
  if (!ctx) {
    console.error('Pie chart canvas not found')
    return
  }

  // Use demo data if no real data
  const labels = quizSetDistribution.value.length > 0 
    ? quizSetDistribution.value.map(set => set.quiz_set_name)
    : ['Geography', 'Science', 'Sports', 'Movies', 'Mixed']
  
  const data = quizSetDistribution.value.length > 0
    ? quizSetDistribution.value.map(set => set.participant_count)
    : [40, 35, 25, 30, 20]

  // Destroy existing chart if it exists
  if (ctx.chart) {
    ctx.chart.destroy()
  }

  ctx.chart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: labels,
      datasets: [
        {
          data: data,
          backgroundColor: ['#6366F1', '#8B5CF6', '#EC4899', '#10B981', '#F59E0B'],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { 
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true,
          }
        },
      },
    },
  })
}

const renderBarChart = () => {
  const ctx = document.getElementById('barChart')
  if (!ctx) {
    console.error('Bar chart canvas not found')
    return
  }

  // Use demo data if no real data
  const labels = weeklyParticipants.value.labels.length > 0
    ? weeklyParticipants.value.labels
    : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  
  const data = weeklyParticipants.value.data.length > 0
    ? weeklyParticipants.value.data
    : [12, 18, 9, 15, 22, 11, 19]

  // Destroy existing chart if it exists
  if (ctx.chart) {
    ctx.chart.destroy()
  }

  ctx.chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Participants',
          data: data,
          backgroundColor: '#4F46E5',
          borderRadius: 6,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: { 
          beginAtZero: true, 
          ticks: { 
            stepSize: 5 
          } 
        },
      },
      plugins: {
        legend: { display: false },
      },
    },
  })
}

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    const now = new Date()
    const diff = now - date
    const minutes = Math.floor(diff / 60000)
    if (minutes < 60) return `${minutes}m ago`
    const hours = Math.floor(minutes / 60)
    if (hours < 24) return `${hours}h ago`
    const days = Math.floor(hours / 24)
    return `${days}d ago`
  } catch (error) {
    return 'Invalid date'
  }
}

const progressBadgeClass = (percentage) => {
  const perc = Math.round(parseFloat(percentage)) || 0
  
  if (perc >= 80) return 'bg-green-100 text-green-800'
  if (perc >= 60) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

// Navigation
const logout = () => {
  router.post('/admin/logout')
}

// Mobile sidebar
const mobileSidebar = ref(false)
const toggleMobileSidebar = () => {
  mobileSidebar.value = !mobileSidebar.value
}

// Lifecycle
onMounted(() => {
  // We already have initial data, but fetch fresh data
  fetchDashboardData()
})

// Clean up charts when component is destroyed
import { onUnmounted } from 'vue'
onUnmounted(() => {
  const pieCanvas = document.getElementById('pieChart')
  const barCanvas = document.getElementById('barChart')
  
  if (pieCanvas && pieCanvas.chart) {
    pieCanvas.chart.destroy()
  }
  if (barCanvas && barCanvas.chart) {
    barCanvas.chart.destroy()
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Ensure charts are responsive */
canvas {
  max-width: 100%;
  height: auto;
}
</style>
