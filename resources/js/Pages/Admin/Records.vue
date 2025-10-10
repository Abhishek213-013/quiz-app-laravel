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
              <h1 class="text-xl font-semibold text-gray-800">Records & Analytics</h1>
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
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Performance Records</h1>
          <p class="text-lg text-gray-600">Comprehensive analytics and performance metrics</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
          <p class="text-gray-600">Loading records data...</p>
        </div>

        <!-- Main Content -->
        <div v-else>
          <!-- Overall Stats Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 text-center shadow-lg border border-blue-200">
              <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                  <i class="fas fa-trophy text-white text-xl"></i>
                </div>
              </div>
              <div class="text-3xl font-bold text-blue-700 mb-2">{{ overallStats.highestScore }}%</div>
              <div class="text-sm font-medium text-blue-600">Highest Score</div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 text-center shadow-lg border border-green-200">
              <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                  <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
              </div>
              <div class="text-3xl font-bold text-green-700 mb-2">{{ overallStats.averageScore }}%</div>
              <div class="text-sm font-medium text-green-600">Average Score</div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 text-center shadow-lg border border-purple-200">
              <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                  <i class="fas fa-users text-white text-xl"></i>
                </div>
              </div>
              <div class="text-3xl font-bold text-purple-700 mb-2">{{ overallStats.totalParticipants }}</div>
              <div class="text-sm font-medium text-purple-600">Total Participants</div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 text-center shadow-lg border border-orange-200">
              <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                  <i class="fas fa-clock text-white text-xl"></i>
                </div>
              </div>
              <div class="text-3xl font-bold text-orange-700 mb-2">{{ overallStats.totalAttempts }}</div>
              <div class="text-sm font-medium text-orange-600">Total Attempts</div>
            </div>
          </div>

          <!-- Charts Section -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Score Distribution Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Score Distribution</h2>
              <canvas id="scoreDistributionChart"></canvas>
            </div>

            <!-- Weekly Performance Trend -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Weekly Performance Trend</h2>
              <canvas id="weeklyTrendChart"></canvas>
            </div>
          </div>

          <!-- Top Performers Section -->
          <div class="bg-white rounded-2xl shadow-lg p-6 mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Top Performers</h2>
            
            <!-- All-time Top Scorers -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-gray-700 mb-4">All-time Highest Scores</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                  v-for="(performer, index) in topPerformers.allTime" 
                  :key="performer.id"
                  class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow"
                >
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                        {{ index + 1 }}
                      </div>
                      <div>
                        <div class="font-semibold text-gray-800">{{ performer.participant_name }}</div>
                        <div class="text-xs text-gray-500">{{ performer.quiz_set_name }}</div>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-2xl font-bold text-green-600">{{ performer.percentage }}%</div>
                      <div class="text-xs text-gray-500">{{ performer.score }}/{{ performer.total_questions }}</div>
                    </div>
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ formatDate(performer.created_at) }} • {{ performer.time_taken }}s
                  </div>
                </div>
              </div>
            </div>

            <!-- Weekly Leaderboard -->
            <div>
              <h3 class="text-lg font-semibold text-gray-700 mb-4">Weekly Leaderboard</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Rank</th>
                      <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Participant</th>
                      <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Quiz Set</th>
                      <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Score</th>
                      <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Percentage</th>
                      <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Time</th>
                      <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr 
                      v-for="(leader, index) in weeklyLeaderboard" 
                      :key="leader.id"
                      class="hover:bg-gray-50 transition-colors"
                    >
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                               :class="getRankClass(index + 1)">
                            {{ index + 1 }}
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ leader.participant_name }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ leader.quiz_set_name }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ leader.score }}/{{ leader.total_questions }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getPercentageClass(leader.percentage)" class="px-2 py-1 text-xs font-semibold rounded-full">
                          {{ leader.percentage }}%
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ leader.time_taken }}s
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(leader.created_at) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Quiz Set Performance -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Highest Scores by Quiz Set -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Highest Scores by Quiz Set</h2>
              <div class="space-y-4">
                <div 
                  v-for="set in quizSetPerformance.highest" 
                  :key="set.quiz_set_id"
                  class="border border-gray-200 rounded-lg p-4 hover:bg-blue-50 transition-colors"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="font-semibold text-gray-800">{{ set.quiz_set_name }}</h3>
                      <p class="text-sm text-gray-600">{{ set.category }}</p>
                    </div>
                    <div class="text-right">
                      <div class="text-2xl font-bold text-green-600">{{ set.highest_percentage }}%</div>
                      <div class="text-xs text-gray-500">by {{ set.top_performer }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Lowest Scores by Quiz Set -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Lowest Scores by Quiz Set</h2>
              <div class="space-y-4">
                <div 
                  v-for="set in quizSetPerformance.lowest" 
                  :key="set.quiz_set_id"
                  class="border border-gray-200 rounded-lg p-4 hover:bg-red-50 transition-colors"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="font-semibold text-gray-800">{{ set.quiz_set_name }}</h3>
                      <p class="text-sm text-gray-600">{{ set.category }}</p>
                    </div>
                    <div class="text-right">
                      <div class="text-2xl font-bold text-red-600">{{ set.lowest_percentage }}%</div>
                      <div class="text-xs text-gray-500">by {{ set.lowest_performer }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Individual Records Section -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Individual Records</h2>
            
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4 mb-6">
              <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Participant</label>
                <input 
                  v-model="searchParticipant"
                  type="text"
                  placeholder="Enter participant name..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
              <div class="w-full sm:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quiz Set</label>
                <select 
                  v-model="selectedQuizSet"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Sets</option>
                  <option v-for="set in quizSets" :key="set.id" :value="set.id">{{ set.name }}</option>
                </select>
              </div>
            </div>

            <!-- Records Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer" @click="sortRecords('participant_name')">
                      Participant
                      <i :class="getSortIcon('participant_name')" class="ml-1"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer" @click="sortRecords('quiz_set_name')">
                      Quiz Set
                      <i :class="getSortIcon('quiz_set_name')" class="ml-1"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer" @click="sortRecords('score')">
                      Score
                      <i :class="getSortIcon('score')" class="ml-1"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer" @click="sortRecords('percentage')">
                      Percentage
                      <i :class="getSortIcon('percentage')" class="ml-1"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer" @click="sortRecords('time_taken')">
                      Time Taken
                      <i :class="getSortIcon('time_taken')" class="ml-1"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider cursor-pointer" @click="sortRecords('created_at')">
                      Date
                      <i :class="getSortIcon('created_at')" class="ml-1"></i>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr 
                    v-for="record in filteredRecords" 
                    :key="record.id"
                    class="hover:bg-gray-50 transition-colors"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ record.participant_name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ record.quiz_set_name }}</div>
                      <div class="text-xs text-gray-500">{{ record.category }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-semibold text-gray-900">{{ record.score }}/{{ record.total_questions }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getPercentageClass(record.percentage)" class="px-2 py-1 text-xs font-semibold rounded-full">
                        {{ record.percentage }}%
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ record.time_taken }}s
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(record.created_at) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between mt-6">
              <div class="text-sm text-gray-700">
                Showing {{ filteredRecords.length }} of {{ allRecords.length }} records
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
import Chart from 'chart.js/auto'

// Reactive data
const loading = ref(true)
const mobileSidebar = ref(false)
const searchParticipant = ref('')
const selectedQuizSet = ref('')
const sortField = ref('created_at')
const sortDirection = ref('desc')

// Data stores
const overallStats = ref({})
const topPerformers = ref({ allTime: [] })
const weeklyLeaderboard = ref([])
const quizSetPerformance = ref({ highest: [], lowest: [] })
const allRecords = ref([])
const quizSets = ref([])

// Fetch all data
const fetchRecordsData = async () => {
  try {
    loading.value = true
    
    // Fetch records data
    const recordsResponse = await fetch('/admin/records-data')
    const recordsData = await recordsResponse.json()

    // Fetch quiz sets
    const setsResponse = await fetch('/api/quiz-sets')
    const setsData = await setsResponse.json()

    allRecords.value = recordsData
    quizSets.value = setsData

    // Calculate overall stats
    calculateOverallStats()
    
    // Calculate top performers
    calculateTopPerformers()
    
    // Calculate weekly leaderboard
    calculateWeeklyLeaderboard()
    
    // Calculate quiz set performance
    calculateQuizSetPerformance()

    // Render charts after data is loaded
    setTimeout(renderCharts, 100)

  } catch (error) {
    console.error('Error fetching records data:', error)
    alert('Failed to load records data')
  } finally {
    loading.value = false
  }
}

// Calculate overall statistics
const calculateOverallStats = () => {
  const records = allRecords.value
  
  if (records.length === 0) {
    overallStats.value = {
      highestScore: 0,
      averageScore: 0,
      totalParticipants: 0,
      totalAttempts: 0
    }
    return
  }

  // Convert percentages to numbers and filter out invalid values
  const validRecords = records.filter(record => {
    const percentage = parseFloat(record.percentage)
    return !isNaN(percentage) && isFinite(percentage)
  })

  if (validRecords.length === 0) {
    overallStats.value = {
      highestScore: 0,
      averageScore: 0,
      totalParticipants: 0,
      totalAttempts: 0
    }
    return
  }

  const percentages = validRecords.map(record => parseFloat(record.percentage))
  
  overallStats.value = {
    highestScore: Math.max(...percentages),
    averageScore: Math.round(percentages.reduce((sum, percentage) => sum + percentage, 0) / percentages.length),
    totalParticipants: new Set(validRecords.map(r => r.participant_name)).size,
    totalAttempts: validRecords.length
  }
}

// Calculate top performers
const calculateTopPerformers = () => {
  const records = allRecords.value
  
  if (records.length === 0) {
    topPerformers.value.allTime = []
    return
  }

  // Filter valid records with numeric percentages
  const validRecords = records.filter(record => {
    const percentage = parseFloat(record.percentage)
    return !isNaN(percentage) && isFinite(percentage)
  })

  // All-time top performers (top 6)
  topPerformers.value.allTime = validRecords
    .sort((a, b) => parseFloat(b.percentage) - parseFloat(a.percentage))
    .slice(0, 6)
}

// Calculate weekly leaderboard
const calculateWeeklyLeaderboard = () => {
  const oneWeekAgo = new Date()
  oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)
  
  const weeklyRecords = allRecords.value
    .filter(record => {
      const recordDate = new Date(record.created_at)
      const percentage = parseFloat(record.percentage)
      return recordDate >= oneWeekAgo && !isNaN(percentage) && isFinite(percentage)
    })
    
  if (weeklyRecords.length === 0) {
    weeklyLeaderboard.value = []
    return
  }

  weeklyLeaderboard.value = weeklyRecords
    .sort((a, b) => parseFloat(b.percentage) - parseFloat(a.percentage))
    .slice(0, 10)
}

// Calculate quiz set performance
const calculateQuizSetPerformance = () => {
  const records = allRecords.value
  
  if (records.length === 0) {
    quizSetPerformance.value = { highest: [], lowest: [] }
    return
  }

  const setPerformance = {}
  
  // Group records by quiz set
  records.forEach(record => {
    const percentage = parseFloat(record.percentage)
    if (isNaN(percentage) || !isFinite(percentage)) return
    
    if (!setPerformance[record.quiz_set_id]) {
      setPerformance[record.quiz_set_id] = {
        quiz_set_id: record.quiz_set_id,
        quiz_set_name: record.quiz_set_name,
        category: record.category,
        percentages: []
      }
    }
    setPerformance[record.quiz_set_id].percentages.push({
      percentage: percentage,
      participant_name: record.participant_name
    })
  })
  
  // Calculate highest and lowest for each set
  const highest = []
  const lowest = []
  
  Object.values(setPerformance).forEach(set => {
    if (set.percentages.length > 0) {
      const percentages = set.percentages.map(p => p.percentage)
      const highestScore = Math.max(...percentages)
      const lowestScore = Math.min(...percentages)
      
      highest.push({
        ...set,
        highest_percentage: highestScore,
        top_performer: set.percentages.find(p => p.percentage === highestScore)?.participant_name || 'Unknown'
      })
      
      lowest.push({
        ...set,
        lowest_percentage: lowestScore,
        lowest_performer: set.percentages.find(p => p.percentage === lowestScore)?.participant_name || 'Unknown'
      })
    }
  })
  
  quizSetPerformance.value.highest = highest.sort((a, b) => b.highest_percentage - a.highest_percentage)
  quizSetPerformance.value.lowest = lowest.sort((a, b) => a.lowest_percentage - b.lowest_percentage)
}

// Filtered records based on search and filter
const filteredRecords = computed(() => {
  let filtered = allRecords.value
  
  // Filter by participant name
  if (searchParticipant.value) {
    filtered = filtered.filter(record =>
      record.participant_name.toLowerCase().includes(searchParticipant.value.toLowerCase())
    )
  }
  
  // Filter by quiz set
  if (selectedQuizSet.value) {
    filtered = filtered.filter(record => record.quiz_set_id == selectedQuizSet.value)
  }
  
  // Sort records
  filtered.sort((a, b) => {
    let aValue = a[sortField.value]
    let bValue = b[sortField.value]
    
    if (sortField.value === 'created_at') {
      aValue = new Date(aValue)
      bValue = new Date(bValue)
    }
    
    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1
    return 0
  })
  
  return filtered
})

// Chart rendering functions
const renderCharts = () => {
  renderScoreDistributionChart()
  renderWeeklyTrendChart()
}

const renderScoreDistributionChart = () => {
  const ctx = document.getElementById('scoreDistributionChart')
  if (!ctx) return

  const records = allRecords.value
  
  if (records.length === 0) {
    // Show empty state for chart
    return
  }

  // Filter valid records
  const validRecords = records.filter(record => {
    const percentage = parseFloat(record.percentage)
    return !isNaN(percentage) && isFinite(percentage)
  })

  const scoreRanges = [
    { range: '90-100%', min: 90, max: 100 },
    { range: '80-89%', min: 80, max: 89 },
    { range: '70-79%', min: 70, max: 79 },
    { range: '60-69%', min: 60, max: 69 },
    { range: '50-59%', min: 50, max: 59 },
    { range: '0-49%', min: 0, max: 49 }
  ]

  const data = scoreRanges.map(range => {
    return validRecords.filter(record => {
      const percentage = parseFloat(record.percentage)
      return percentage >= range.min && percentage <= range.max
    }).length
  })

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: scoreRanges.map(r => r.range),
      datasets: [{
        label: 'Number of Attempts',
        data: data,
        backgroundColor: [
          '#10B981', '#34D399', '#60A5FA', '#FBBF24', '#F59E0B', '#EF4444'
        ],
        borderRadius: 6,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Attempts'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Score Range'
          }
        }
      }
    }
  })
}

const renderWeeklyTrendChart = () => {
  const ctx = document.getElementById('weeklyTrendChart')
  if (!ctx) return

  const records = allRecords.value
  
  if (records.length === 0) {
    // Show empty state for chart
    return
  }

  const oneWeekAgo = new Date()
  oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)

  const weeklyData = {}
  for (let i = 6; i >= 0; i--) {
    const date = new Date()
    date.setDate(date.getDate() - i)
    const dateStr = date.toISOString().split('T')[0]
    weeklyData[dateStr] = { total: 0, sum: 0, count: 0 }
  }

  records.forEach(record => {
    const percentage = parseFloat(record.percentage)
    if (isNaN(percentage) || !isFinite(percentage)) return
    
    const recordDate = new Date(record.created_at).toISOString().split('T')[0]
    if (weeklyData[recordDate]) {
      weeklyData[recordDate].total++
      weeklyData[recordDate].sum += percentage
      weeklyData[recordDate].count++
    }
  })

  const labels = Object.keys(weeklyData).map(date => {
    return new Date(date).toLocaleDateString('en-US', { weekday: 'short' })
  })

  const averageScores = Object.values(weeklyData).map(day => 
    day.count > 0 ? Math.round(day.sum / day.count) : 0
  )

  const attemptCounts = Object.values(weeklyData).map(day => day.total)

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Average Score (%)',
          data: averageScores,
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          fill: true,
          tension: 0.4,
          yAxisID: 'y'
        },
        {
          label: 'Attempts',
          data: attemptCounts,
          borderColor: '#10B981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          fill: true,
          tension: 0.4,
          yAxisID: 'y1'
        }
      ]
    },
    options: {
      responsive: true,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      scales: {
        y: {
          type: 'linear',
          display: true,
          position: 'left',
          title: {
            display: true,
            text: 'Average Score (%)'
          },
          min: 0,
          max: 100
        },
        y1: {
          type: 'linear',
          display: true,
          position: 'right',
          title: {
            display: true,
            text: 'Number of Attempts'
          },
          grid: {
            drawOnChartArea: false,
          },
        }
      }
    }
  })
}

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getPercentageClass = (percentage) => {
  const perc = parseFloat(percentage)
  if (isNaN(perc)) return 'bg-gray-100 text-gray-800'
  
  if (perc >= 80) return 'bg-green-100 text-green-800'
  if (perc >= 60) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

const getRankClass = (rank) => {
  if (rank === 1) return 'bg-yellow-400 text-white'
  if (rank === 2) return 'bg-gray-400 text-white'
  if (rank === 3) return 'bg-orange-400 text-white'
  return 'bg-blue-100 text-blue-800'
}

const getSortIcon = (field) => {
  if (sortField.value !== field) return 'fas fa-sort text-gray-300'
  return sortDirection.value === 'asc' ? 'fas fa-arrow-up text-blue-600' : 'fas fa-arrow-down text-blue-600'
}

const sortRecords = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'desc'
  }
}

const toggleMobileSidebar = () => {
  mobileSidebar.value = !mobileSidebar.value
}

const logout = () => {
  router.post('/admin/logout')
}

// Lifecycle
onMounted(() => {
  fetchRecordsData()
})

// Clean up charts when component is destroyed
import { onUnmounted } from 'vue'
onUnmounted(() => {
  const charts = ['scoreDistributionChart', 'weeklyTrendChart']
  charts.forEach(chartId => {
    const canvas = document.getElementById(chartId)
    if (canvas && canvas.chart) {
      canvas.chart.destroy()
    }
  })
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