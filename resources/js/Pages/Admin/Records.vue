<template>
  <div class="min-h-screen" :class="isDark ? 'dark-theme' : 'light-theme'">
    <AdminNavbar 
      title="Records & Analytics"
      :is-dark="isDark"
      @toggle-theme="toggleTheme"
      @toggle-mobile-sidebar="toggleMobileSidebar"
      @logout="handleLogout"
    />

    <div class="flex">
      <AdminSidebar 
        :mobile-sidebar="mobileSidebar"
        current-page="/admin/records"
        @close-mobile-sidebar="toggleMobileSidebar"
      />

      <!-- Main Content -->
      <div class="main-content">
        <!-- Content -->
        <main class="content">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <i class="fas fa-spinner fa-spin loading-icon"></i>
            <p class="loading-text">Loading records data...</p>
          </div>

          <!-- Main Content -->
          <div v-else>
            <!-- Quick Stats Section -->
            <div class="stats-section">
              <h2 class="section-title">Performance Records</h2>
              <div class="stats-grid">
                <div class="stat-card blue">
                  <div class="stat-number">{{ overallStats.highestScore }}%</div>
                  <div class="stat-label">Highest Score</div>
                </div>
                <div class="stat-card green">
                  <div class="stat-number">{{ overallStats.averageScore }}%</div>
                  <div class="stat-label">Average Score</div>
                </div>
                <div class="stat-card purple">
                  <div class="stat-number">{{ overallStats.totalParticipants }}</div>
                  <div class="stat-label">Total Participants</div>
                </div>
                <div class="stat-card orange">
                  <div class="stat-number">{{ overallStats.totalAttempts }}</div>
                  <div class="stat-label">Total Attempts</div>
                </div>
              </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
              <!-- Score Distribution Chart -->
              <div class="chart-card">
                <h2 class="chart-title">Score Distribution</h2>
                <canvas id="scoreDistributionChart"></canvas>
              </div>

              <!-- Weekly Performance Trend -->
              <div class="chart-card">
                <h2 class="chart-title">Weekly Performance Trend</h2>
                <canvas id="weeklyTrendChart"></canvas>
              </div>
            </div>

            <!-- Top Performers Section -->
            <div class="content-card mb-8">
              <h2 class="card-title-large">Top Performers</h2>
              <p class="card-subtitle">Best performing participants across all quizzes</p>
              
              <!-- All-time Top Scorers -->
              <div class="mb-8">
                <h3 class="card-subtitle mb-4">All-time Highest Scores</h3>
                <div class="scorers-grid">
                  <div 
                    v-for="(performer, index) in topPerformers.allTime" 
                    :key="performer.id"
                    class="scorer-card"
                  >
                    <div class="scorer-header">
                      <div class="scorer-info">
                        <div class="scorer-rank">
                          {{ index + 1 }}
                        </div>
                        <div>
                          <h3 class="scorer-title">{{ performer.participant_name }}</h3>
                          <p class="scorer-category">{{ performer.quiz_set_name }}</p>
                        </div>
                      </div>
                      <div class="scorer-performance">
                        <div class="scorer-percentage">{{ performer.percentage }}%</div>
                        <div class="scorer-score">{{ performer.score }}/{{ performer.total_questions }}</div>
                      </div>
                    </div>
                    <div class="scorer-footer">
                      <div class="scorer-date">{{ formatDate(performer.created_at) }}</div>
                      <div class="scorer-time">{{ performer.time_taken }}s</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Weekly Leaderboard -->
              <div>
                <h3 class="card-subtitle mb-4">Weekly Leaderboard</h3>
                <div class="overflow-x-auto">
                  <table class="participants-table">
                    <thead>
                      <tr>
                        <th class="table-header">Rank</th>
                        <th class="table-header">Participant</th>
                        <th class="table-header">Quiz Set</th>
                        <th class="table-header">Score</th>
                        <th class="table-header">Percentage</th>
                        <th class="table-header">Time</th>
                        <th class="table-header">Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr 
                        v-for="(leader, index) in weeklyLeaderboard" 
                        :key="leader.id"
                        class="table-row"
                      >
                        <td class="table-cell">
                          <div :class="getRankClass(index + 1)" class="rank-badge">
                            {{ index + 1 }}
                          </div>
                        </td>
                        <td class="table-cell">
                          <div class="user-name">{{ leader.participant_name }}</div>
                        </td>
                        <td class="table-cell">
                          <div class="quiz-name">{{ leader.quiz_set_name }}</div>
                        </td>
                        <td class="table-cell">
                          <div class="score-value">{{ leader.score }}/{{ leader.total_questions }}</div>
                        </td>
                        <td class="table-cell">
                          <div :class="progressBadgeClass(leader.percentage)" class="progress-badge">
                            {{ leader.percentage }}%
                          </div>
                        </td>
                        <td class="table-cell">
                          <div class="score-date">{{ leader.time_taken }}s</div>
                        </td>
                        <td class="table-cell">
                          <div class="score-date">{{ formatDate(leader.created_at) }}</div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Quiz Set Performance -->
            <div class="performance-section">
              <!-- Highest Scores by Quiz Set -->
              <div class="content-card">
                <h2 class="card-title-large">Highest Scores by Quiz Set</h2>
                <div class="performance-content">
                  <div class="score-distribution">
                    <div class="distribution-grid">
                      <div 
                        v-for="set in quizSetPerformance.highest" 
                        :key="set.quiz_set_id"
                        class="distribution-item high"
                      >
                        <div class="distribution-count">{{ set.highest_percentage }}%</div>
                        <div class="distribution-range">{{ set.quiz_set_name }}</div>
                        <div class="distribution-label">by {{ set.top_performer }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Lowest Scores by Quiz Set -->
              <div class="content-card">
                <h2 class="card-title-large">Lowest Scores by Quiz Set</h2>
                <div class="performance-content">
                  <div class="score-distribution">
                    <div class="distribution-grid">
                      <div 
                        v-for="set in quizSetPerformance.lowest" 
                        :key="set.quiz_set_id"
                        class="distribution-item low"
                      >
                        <div class="distribution-count">{{ set.lowest_percentage }}%</div>
                        <div class="distribution-range">{{ set.quiz_set_name }}</div>
                        <div class="distribution-label">by {{ set.lowest_performer }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Individual Records Section -->
            <div class="content-card">
              <h2 class="card-title-large">Individual Records</h2>
              <p class="card-subtitle">Detailed view of all quiz attempts and performances</p>
              
              <!-- Filters -->
              <div class="filters-section">
                <div class="search-input-container">
                  <input 
                    v-model="searchParticipant"
                    type="text"
                    placeholder="Search participant..."
                    class="search-input"
                  >
                  <i class="fas fa-search search-icon"></i>
                </div>
                <select 
                  v-model="selectedQuizSet"
                  class="filter-select"
                >
                  <option value="">All Quiz Sets</option>
                  <option v-for="set in quizSets" :key="set.id" :value="set.id">{{ set.name }}</option>
                </select>
              </div>

              <!-- Records Table -->
              <div class="overflow-x-auto">
                <table class="participants-table">
                  <thead>
                    <tr>
                      <th class="table-header" @click="sortRecords('participant_name')">
                        <div class="flex items-center space-x-2">
                          <span>Participant</span>
                          <i :class="getSortIcon('participant_name')"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('quiz_set_name')">
                        <div class="flex items-center space-x-2">
                          <span>Quiz Set</span>
                          <i :class="getSortIcon('quiz_set_name')"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('score')">
                        <div class="flex items-center space-x-2">
                          <span>Score</span>
                          <i :class="getSortIcon('score')"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('percentage')">
                        <div class="flex items-center space-x-2">
                          <span>Percentage</span>
                          <i :class="getSortIcon('percentage')"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('time_taken')">
                        <div class="flex items-center space-x-2">
                          <span>Time Taken</span>
                          <i :class="getSortIcon('time_taken')"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('created_at')">
                        <div class="flex items-center space-x-2">
                          <span>Date</span>
                          <i :class="getSortIcon('created_at')"></i>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr 
                      v-for="record in filteredRecords" 
                      :key="record.id"
                      class="table-row"
                    >
                      <td class="table-cell">
                        <div class="user-name">{{ record.participant_name }}</div>
                      </td>
                      <td class="table-cell">
                        <div class="quiz-name">{{ record.quiz_set_name }}</div>
                        <div class="quiz-category">{{ record.category }}</div>
                      </td>
                      <td class="table-cell">
                        <div class="score-value">{{ record.score }}/{{ record.total_questions }}</div>
                      </td>
                      <td class="table-cell">
                        <div :class="progressBadgeClass(record.percentage)" class="progress-badge">
                          {{ record.percentage }}%
                        </div>
                      </td>
                      <td class="table-cell">
                        <div class="score-date">{{ record.time_taken }}s</div>
                      </td>
                      <td class="table-cell">
                        <div class="score-date">{{ formatDate(record.created_at) }}</div>
                      </td>
                    </tr>
                    <tr v-if="filteredRecords.length === 0">
                      <td colspan="6" class="empty-state">
                        <div class="empty-content">
                          <i class="fas fa-chart-bar empty-icon"></i>
                          <p class="empty-title">No records found</p>
                          <p class="empty-subtitle">Try adjusting your search criteria</p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Table Footer -->
              <div class="table-footer">
                <div class="footer-text">
                  Showing <span class="highlight">{{ filteredRecords.length }}</span> of 
                  <span class="highlight">{{ allRecords.length }}</span> records
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';
import AdminNavbar from './AdminNavbar.vue';
import AdminSidebar from './AdminSidebar.vue';

export default {
  name: 'Records',
  components: {
    AdminNavbar,
    AdminSidebar
  },
  data() {
    return {
      isDark: false,
      mobileSidebar: false,
      loading: true,
      searchParticipant: '',
      selectedQuizSet: '',
      sortField: 'created_at',
      sortDirection: 'desc',
      overallStats: {},
      topPerformers: { allTime: [] },
      weeklyLeaderboard: [],
      quizSetPerformance: { highest: [], lowest: [] },
      allRecords: [],
      quizSets: []
    }
  },
  computed: {
    filteredRecords() {
      let filtered = this.allRecords
      
      // Filter by participant name
      if (this.searchParticipant) {
        filtered = filtered.filter(record =>
          record.participant_name.toLowerCase().includes(this.searchParticipant.toLowerCase())
        )
      }
      
      // Filter by quiz set
      if (this.selectedQuizSet) {
        filtered = filtered.filter(record => record.quiz_set_id == this.selectedQuizSet)
      }
      
      // Sort records
      filtered.sort((a, b) => {
        let aValue = a[this.sortField]
        let bValue = b[this.sortField]
        
        if (this.sortField === 'created_at') {
          aValue = new Date(aValue)
          bValue = new Date(bValue)
        }
        
        if (aValue < bValue) return this.sortDirection === 'asc' ? -1 : 1
        if (aValue > bValue) return this.sortDirection === 'asc' ? 1 : -1
        return 0
      })
      
      return filtered
    }
  },
  mounted() {
    this.fetchRecordsData()
  },
  beforeUnmount() {
    this.cleanupCharts()
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
    async fetchRecordsData() {
      try {
        this.loading = true
        
        // Simulate API calls with demo data
        await this.loadDemoData()
        
        // Calculate statistics
        this.calculateOverallStats()
        this.calculateTopPerformers()
        this.calculateWeeklyLeaderboard()
        this.calculateQuizSetPerformance()

        // Render charts
        setTimeout(this.renderCharts, 100)

      } catch (error) {
        console.error('Error fetching records data:', error)
      } finally {
        this.loading = false
      }
    },
    loadDemoData() {
      // Demo data for records
      this.allRecords = [
        {
          id: 1,
          participant_name: 'John Smith',
          quiz_set_name: 'World Geography',
          category: 'Geography',
          quiz_set_id: 1,
          score: 18,
          total_questions: 20,
          percentage: 90,
          time_taken: 45,
          created_at: new Date()
        },
        {
          id: 2,
          participant_name: 'Emily Wilson',
          quiz_set_name: 'Basic Science',
          category: 'Science',
          quiz_set_id: 2,
          score: 19,
          total_questions: 20,
          percentage: 95,
          time_taken: 52,
          created_at: new Date(Date.now() - 86400000)
        },
        {
          id: 3,
          participant_name: 'Mike Davis',
          quiz_set_name: 'World Geography',
          category: 'Geography',
          quiz_set_id: 1,
          score: 16,
          total_questions: 20,
          percentage: 80,
          time_taken: 38,
          created_at: new Date(Date.now() - 172800000)
        },
        {
          id: 4,
          participant_name: 'Sarah Johnson',
          quiz_set_name: 'Movie Trivia',
          category: 'Entertainment',
          quiz_set_id: 3,
          score: 14,
          total_questions: 20,
          percentage: 70,
          time_taken: 61,
          created_at: new Date(Date.now() - 259200000)
        },
        {
          id: 5,
          participant_name: 'David Brown',
          quiz_set_name: 'Basic Science',
          category: 'Science',
          quiz_set_id: 2,
          score: 17,
          total_questions: 20,
          percentage: 85,
          time_taken: 49,
          created_at: new Date(Date.now() - 345600000)
        }
      ]

      this.quizSets = [
        { id: 1, name: 'World Geography' },
        { id: 2, name: 'Basic Science' },
        { id: 3, name: 'Movie Trivia' }
      ]
    },
    calculateOverallStats() {
      const records = this.allRecords
      
      if (records.length === 0) {
        this.overallStats = {
          highestScore: 0,
          averageScore: 0,
          totalParticipants: 0,
          totalAttempts: 0
        }
        return
      }

      const percentages = records.map(record => parseFloat(record.percentage))
      const uniqueParticipants = new Set(records.map(r => r.participant_name))
      
      this.overallStats = {
        highestScore: Math.max(...percentages),
        averageScore: Math.round(percentages.reduce((sum, percentage) => sum + percentage, 0) / percentages.length),
        totalParticipants: uniqueParticipants.size,
        totalAttempts: records.length
      }
    },
    calculateTopPerformers() {
      const records = this.allRecords
      
      if (records.length === 0) {
        this.topPerformers.allTime = []
        return
      }

      this.topPerformers.allTime = records
        .sort((a, b) => parseFloat(b.percentage) - parseFloat(a.percentage))
        .slice(0, 6)
    },
    calculateWeeklyLeaderboard() {
      const oneWeekAgo = new Date()
      oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)
      
      const weeklyRecords = this.allRecords
        .filter(record => new Date(record.created_at) >= oneWeekAgo)
        
      this.weeklyLeaderboard = weeklyRecords
        .sort((a, b) => parseFloat(b.percentage) - parseFloat(a.percentage))
        .slice(0, 10)
    },
    calculateQuizSetPerformance() {
      const records = this.allRecords
      
      if (records.length === 0) {
        this.quizSetPerformance = { highest: [], lowest: [] }
        return
      }

      const setPerformance = {}
      
      // Group records by quiz set
      records.forEach(record => {
        if (!setPerformance[record.quiz_set_id]) {
          setPerformance[record.quiz_set_id] = {
            quiz_set_id: record.quiz_set_id,
            quiz_set_name: record.quiz_set_name,
            category: record.category,
            percentages: []
          }
        }
        setPerformance[record.quiz_set_id].percentages.push({
          percentage: parseFloat(record.percentage),
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
      
      this.quizSetPerformance.highest = highest.sort((a, b) => b.highest_percentage - a.highest_percentage)
      this.quizSetPerformance.lowest = lowest.sort((a, b) => a.lowest_percentage - b.lowest_percentage)
    },
    renderCharts() {
      this.renderScoreDistributionChart()
      this.renderWeeklyTrendChart()
    },
    renderScoreDistributionChart() {
      const ctx = document.getElementById('scoreDistributionChart')
      if (!ctx) return

      const records = this.allRecords
      
      if (records.length === 0) return

      const scoreRanges = [
        { range: '90-100%', min: 90, max: 100 },
        { range: '80-89%', min: 80, max: 89 },
        { range: '70-79%', min: 70, max: 79 },
        { range: '60-69%', min: 60, max: 69 },
        { range: '50-59%', min: 50, max: 59 },
        { range: '0-49%', min: 0, max: 49 }
      ]

      const data = scoreRanges.map(range => {
        return records.filter(record => {
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
            }
          }
        }
      })
    },
    renderWeeklyTrendChart() {
      const ctx = document.getElementById('weeklyTrendChart')
      if (!ctx) return

      // Demo data for weekly trend
      const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
      const averageScores = [75, 82, 78, 85, 88, 80, 83]
      const attemptCounts = [12, 15, 18, 22, 25, 20, 16]

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
    },
    cleanupCharts() {
      const charts = ['scoreDistributionChart', 'weeklyTrendChart']
      charts.forEach(chartId => {
        const canvas = document.getElementById(chartId)
        if (canvas && canvas.chart) {
          canvas.chart.destroy()
        }
      })
    },
    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },
    progressBadgeClass(percentage) {
      const perc = parseFloat(percentage)
      if (isNaN(perc)) return 'progress-badge-low'
      
      if (perc >= 80) return 'progress-badge-high'
      if (perc >= 60) return 'progress-badge-medium'
      return 'progress-badge-low'
    },
    getRankClass(rank) {
      if (rank === 1) return 'rank-gold'
      if (rank === 2) return 'rank-silver'
      if (rank === 3) return 'rank-bronze'
      return 'rank-other'
    },
    getSortIcon(field) {
      if (this.sortField !== field) return 'fas fa-sort text-gray-400'
      return this.sortDirection === 'asc' ? 'fas fa-arrow-up text-blue-600' : 'fas fa-arrow-down text-blue-600'
    },
    sortRecords(field) {
      if (this.sortField === field) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortField = field
        this.sortDirection = 'desc'
      }
    }
  }
}
</script>

<style>
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
  background-color: #ffedd5;
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

/* Charts Section */
.charts-section {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  margin-bottom: 3rem;
}
@media (min-width: 1024px) {
  .charts-section {
    grid-template-columns: repeat(2, 1fr);
  }
}

.chart-card {
  background-color: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 1rem;
  box-shadow: var(--shadow);
}

.chart-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 1rem;
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

/* Scorers Grid */
.scorers-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}
@media (min-width: 768px) {
  .scorers-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (min-width: 1024px) {
  .scorers-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.scorer-card {
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  padding: 1rem;
  transition: all 0.2s ease;
}
.scorer-card:hover {
  background-color: var(--hover-bg);
  transform: translateY(-2px);
}

.scorer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.scorer-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.scorer-rank {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
}

.scorer-title {
  font-weight: 600;
  color: var(--text-primary);
  font-size: 0.875rem;
}

.scorer-category {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.scorer-performance {
  text-align: right;
}

.scorer-percentage {
  font-weight: 700;
  color: #16a34a;
  font-size: 1.125rem;
}
.dark-theme .scorer-percentage {
  color: #4ade80;
}

.scorer-score {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.scorer-footer {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Rank Badges */
.rank-badge {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
}

.rank-gold {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
}

.rank-silver {
  background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
  color: white;
}

.rank-bronze {
  background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
  color: white;
}

.rank-other {
  background-color: var(--hover-bg);
  color: var(--text-primary);
}

/* Filters Section */
.filters-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media (min-width: 640px) {
  .filters-section {
    flex-direction: row;
    align-items: end;
  }
}

.search-input-container {
  position: relative;
  flex: 1;
}

.search-input {
  padding-left: 2.5rem;
  padding-right: 1rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  background-color: var(--bg-primary);
  color: var(--text-primary);
  width: 100%;
  transition: all 0.2s ease;
}
.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.filter-select {
  padding: 0.5rem 2.5rem 0.5rem 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  background-color: var(--bg-primary);
  color: var(--text-primary);
  width: 100%;
  min-width: 10rem;
}
@media (min-width: 640px) {
  .filter-select {
    width: auto;
  }
}

/* Table Styles */
.participants-table {
  width: 100%;
  border-collapse: collapse;
}

.table-header {
  padding: 1rem 1.5rem;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background-color: var(--hover-bg);
  cursor: pointer;
  transition: background-color 0.2s ease;
}
.table-header:hover {
  background-color: var(--border-color);
}

.table-row {
  transition: all 0.2s ease;
  border-bottom: 1px solid var(--border-color);
}
.table-row:hover {
  background-color: var(--hover-bg);
}

.table-cell {
  padding: 1rem 1.5rem;
  white-space: nowrap;
}

/* User Styles */
.user-name {
  font-weight: 500;
  color: var(--text-primary);
}

.quiz-name {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.quiz-category {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Score Styles */
.score-value {
  font-weight: 600;
  color: var(--text-primary);
}

.score-date {
  font-size: 0.875rem;
  color: var(--text-muted);
}

/* Progress Badges */
.progress-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 500;
  flex-shrink: 0;
}

.progress-badge-high {
  background-color: #dcfce7;
  color: #16a34a;
}
.dark-theme .progress-badge-high {
  background-color: #14532d;
  color: #4ade80;
}

.progress-badge-medium {
  background-color: #fef9c3;
  color: #ca8a04;
}
.dark-theme .progress-badge-medium {
  background-color: #713f12;
  color: #facc15;
}

.progress-badge-low {
  background-color: #fee2e2;
  color: #dc2626;
}
.dark-theme .progress-badge-low {
  background-color: #7f1d1d;
  color: #f87171;
}

/* Empty State */
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
  font-size: 3rem;
  margin-bottom: 1rem;
  color: var(--border-color);
}

.empty-title {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.empty-subtitle {
  font-size: 0.875rem;
}

/* Table Footer */
.table-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--border-color);
  background-color: var(--hover-bg);
  border-radius: 0 0 0.5rem 0.5rem;
}

.footer-text {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-secondary);
}

.highlight {
  color: #3b82f6;
  font-weight: 700;
}

/* Performance Section */
.performance-section {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  max-width: 56rem;
  margin: 0 auto;
}
@media (min-width: 1024px) {
  .performance-section {
    grid-template-columns: repeat(2, 1fr);
  }
}

.performance-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.score-distribution {
  margin-top: 1rem;
}

.distribution-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
}
@media (min-width: 640px) {
  .distribution-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.distribution-item {
  text-align: center;
  padding: 1rem;
  border-radius: 0.5rem;
  transition: all 0.2s ease;
}
.distribution-item:hover {
  transform: translateY(-2px);
}

.distribution-item.high {
  background-color: #dcfce7;
}
.dark-theme .distribution-item.high {
  background-color: #14532d;
}

.distribution-item.low {
  background-color: #fee2e2;
}
.dark-theme .distribution-item.low {
  background-color: #7f1d1d;
}

.distribution-count {
  font-weight: 600;
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}
.distribution-item.high .distribution-count {
  color: #16a34a;
}
.dark-theme .distribution-item.high .distribution-count {
  color: #4ade80;
}
.distribution-item.low .distribution-count {
  color: #dc2626;
}
.dark-theme .distribution-item.low .distribution-count {
  color: #f87171;
}

.distribution-range {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.distribution-label {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: var(--hover-bg);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: var(--text-muted);
}

/* Responsive Design */
@media (max-width: 768px) {
  .content {
    padding: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .performance-section {
    grid-template-columns: 1fr;
  }
  
  .scorers-grid {
    grid-template-columns: 1fr;
  }
  
  .table-cell {
    padding: 0.75rem 1rem;
  }
}
</style>