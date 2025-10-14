<template>
  <div class="min-h-screen" :class="themeClass">
    <AdminNavbar 
      title="Participants Management"
      :is-dark="isDark"
      :profile="profile" 
      @toggle-theme="toggleTheme"
      @toggle-mobile-sidebar="toggleMobileSidebar"
      @logout="handleLogout"
    />

    <div class="flex">
      <AdminSidebar 
        :mobile-sidebar="mobileSidebar"
        :profile="profile"  
        current-page="/admin/participants"
        @close-mobile-sidebar="toggleMobileSidebar"
      />

      <!-- Main Content -->
      <div class="main-content">
        <!-- Content -->
        <main class="content">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <i class="fas fa-spinner fa-spin loading-icon"></i>
            <p class="loading-text">Loading participants data...</p>
          </div>

          <!-- Main Content -->
          <div v-else>
            <!-- Quick Stats Section -->
            <div class="stats-section">
              <div class="flex justify-between items-center mb-8">
                <h2 class="section-title">Participants Overview</h2>
                <button @click="refreshData" class="refresh-btn" :disabled="refreshing">
                  <i class="fas fa-sync-alt" :class="{ 'fa-spin': refreshing }"></i>
                  {{ refreshing ? 'Refreshing...' : 'Refresh Data' }}
                </button>
              </div>
              <div class="stats-grid">
                <div class="stat-card" :class="`stat-${theme.primaryColor}`">
                  <div class="stat-number">{{ participants.length }}</div>
                  <div class="stat-label">Total Participants</div>
                </div>
                <div class="stat-card" :class="`stat-${theme.primaryColor}`">
                  <div class="stat-number">{{ totalAttempts }}</div>
                  <div class="stat-label">Total Attempts</div>
                </div>
                <div class="stat-card" :class="`stat-${theme.primaryColor}`">
                  <div class="stat-number">{{ averageScore }}%</div>
                  <div class="stat-label">Average Score</div>
                </div>
              </div>
            </div>

            <!-- Search and Filters Section -->
            <div class="content-card mb-8">
              <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                  <h2 class="card-title-large">All Participants</h2>
                  <p class="card-subtitle">Manage and analyze participant performance</p>
                </div>
                <div class="relative w-full sm:w-auto">
                  <div class="relative">
                    <input
                      v-model="search"
                      type="text"
                      placeholder="Search participants..."
                      class="search-input"
                    >
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Participants Table -->
            <div class="content-card mb-8">
              <h2 class="card-title-large">Participant Performance</h2>
              <p class="card-subtitle">Detailed overview of all participants and their quiz results</p>
              
              <div class="overflow-x-auto">
                <table class="participants-table">
                  <thead>
                    <tr>
                      <th class="table-header" @click="sortBy('name')">
                        <div class="flex items-center space-x-2">
                          <span>Participant</span>
                          <i :class="sortColumn === 'name' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortBy('best_score')">
                        <div class="flex items-center space-x-2">
                          <span>Best Score</span>
                          <i :class="sortColumn === 'best_score' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortBy('best_percentage')">
                        <div class="flex items-center space-x-2">
                          <span>Best %</span>
                          <i :class="sortColumn === 'best_percentage' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortBy('total_attempts')">
                        <div class="flex items-center space-x-2">
                          <span>Attempts</span>
                          <i :class="sortColumn === 'total_attempts' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortBy('average_score')">
                        <div class="flex items-center space-x-2">
                          <span>Avg. Score</span>
                          <i :class="sortColumn === 'average_score' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                      </th>
                      <th class="table-header" @click="sortBy('last_attempt')">
                        <div class="flex items-center space-x-2">
                          <span>Last Activity</span>
                          <i :class="sortColumn === 'last_attempt' ? (sortDirection === 'asc' ? 'fas fa-arrow-up' : 'fas fa-arrow-down') : 'fas fa-sort text-gray-400'"></i>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr 
                      v-for="participant in filteredParticipants" 
                      :key="participant.name" 
                      class="table-row"
                    >
                      <td class="table-cell">
                        <div class="flex items-center">
                          <div class="user-avatar-small" :class="`avatar-${theme.primaryColor}`">
                            <i class="fas fa-user"></i>
                          </div>
                          <div class="user-details">
                            <div class="user-name">{{ participant.name }}</div>
                            <div class="quiz-category">Active Participant</div>
                          </div>
                        </div>
                      </td>
                      <td class="table-cell">
                        <div class="score-value">{{ participant.best_score }}/15</div>
                        <div class="score-date">Questions</div>
                      </td>
                      <td class="table-cell">
                        <div :class="progressBadgeClass(participant.best_percentage)" class="progress-badge">
                          {{ participant.best_percentage }}%
                        </div>
                      </td>
                      <td class="table-cell">
                        <div class="attempt-score">
                          <div class="score-value">{{ participant.total_attempts }}</div>
                          <div class="score-date">Total attempts</div>
                        </div>
                      </td>
                      <td class="table-cell">
                        <div :class="progressBadgeClass(participant.average_score)" class="progress-badge">
                          {{ participant.average_score }}%
                        </div>
                      </td>
                      <td class="table-cell">
                        <div class="attempt-score">
                          <div class="score-value">{{ formatDate(participant.last_attempt) }}</div>
                          <div class="score-date">Last attempt</div>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="filteredParticipants.length === 0">
                      <td colspan="6" class="empty-state">
                        <div class="empty-content">
                          <i class="fas fa-users empty-icon"></i>
                          <p class="empty-title">No participants found</p>
                          <p class="empty-subtitle">Try adjusting your search criteria</p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Table Footer -->
              <div class="table-footer">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                  <div class="footer-text">
                    Showing <span class="highlight">{{ filteredParticipants.length }}</span> of 
                    <span class="highlight">{{ participants.length }}</span> participants
                  </div>
                  <div class="footer-info">
                    <i class="fas fa-info-circle info-icon"></i>
                    <span>Sorted by {{ sortColumn.replace('_', ' ') }} ({{ sortDirection }})</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Performance Overview -->
            <div class="performance-section">
              <!-- Score Distribution -->
              <div class="content-card">
                <h2 class="card-title-large">Score Distribution</h2>
                <div class="performance-content">
                  <div class="score-distribution">
                    <div class="distribution-label">Performance Overview</div>
                    <div class="distribution-grid">
                      <div class="distribution-item high">
                        <div class="distribution-count">{{ scoreDistribution.high }}</div>
                        <div class="distribution-range">80%+</div>
                      </div>
                      <div class="distribution-item medium">
                        <div class="distribution-count">{{ scoreDistribution.medium }}</div>
                        <div class="distribution-range">60–79%</div>
                      </div>
                      <div class="distribution-item low">
                        <div class="distribution-count">{{ scoreDistribution.low }}</div>
                        <div class="distribution-range">Below 60%</div>
                      </div>
                    </div>
                  </div>

                  <div class="completion-rate">
                    <div class="rate-label">Active Participation Rate</div>
                    <div class="rate-number">{{ participationRate }}%</div>
                  </div>
                </div>
              </div>

              <!-- Recent Activity -->
              <div class="content-card">
                <h2 class="card-title-large">Recent Activity</h2>
                <div class="activity-list">
                  <div 
                    v-for="activity in recentActivities" 
                    :key="activity.id" 
                    class="activity-item"
                  >
                    <div class="activity-icon" :class="`bg-${theme.primaryColor}`">
                      <i :class="[activity.icon, 'text-white text-sm']"></i>
                    </div>
                    <div class="activity-content">
                      <p class="activity-message">{{ activity.message }}</p>
                      <p class="activity-time">{{ activity.time }}</p>
                    </div>
                  </div>
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
import AdminNavbar from './AdminNavbar.vue';
import AdminSidebar from './AdminSidebar.vue';

export default {
  name: 'Participants',
  components: {
    AdminNavbar,
    AdminSidebar
  },
  props: {
    participants: {
      type: Array,
      default: () => []
    },
    profile: {
      type: Object,
      default: () => ({
        firstName: 'Admin',
        lastName: 'User',
        email: 'admin@quiz.com',
        avatar: null,
        role: 'admin'
      })
    },
    theme: {
      type: Object,
      default: () => ({
        colorScheme: 'light',
        primaryColor: 'blue',
        layout: 'sidebar'
      })
    }
  },
  data() {
    return {
      isDark: false,
      mobileSidebar: false,
      loading: false,
      refreshing: false,
      search: '',
      sortColumn: 'total_attempts',
      sortDirection: 'desc',
      recentActivities: [
        {
          id: 1,
          message: "New participant John Smith registered",
          time: "2 hours ago",
          icon: "fas fa-user-plus"
        },
        {
          id: 2,
          message: "Emily Wilson achieved 95% in Science quiz",
          time: "5 hours ago",
          icon: "fas fa-trophy"
        },
        {
          id: 3,
          message: "15 new quiz attempts recorded",
          time: "1 day ago",
          icon: "fas fa-chart-line"
        },
        {
          id: 4,
          message: "Mike Davis completed Geography challenge",
          time: "2 days ago",
          icon: "fas fa-globe"
        }
      ]
    }
  },
  computed: {
    themeClass() {
      return this.theme.colorScheme === 'dark' ? 'dark-theme' : 'light-theme';
    },
    filteredParticipants() {
      let filtered = this.participants.filter(participant => 
        participant.name.toLowerCase().includes(this.search.toLowerCase())
      )

      // Sort the filtered results
      filtered.sort((a, b) => {
        let aValue = a[this.sortColumn]
        let bValue = b[this.sortColumn]

        if (this.sortColumn === 'last_attempt') {
          aValue = new Date(aValue)
          bValue = new Date(bValue)
        }

        if (aValue < bValue) return this.sortDirection === 'asc' ? -1 : 1
        if (aValue > bValue) return this.sortDirection === 'asc' ? 1 : -1
        return 0
      })

      return filtered
    },
    totalAttempts() {
      return this.participants.reduce((sum, participant) => sum + participant.total_attempts, 0)
    },
    averageScore() {
      if (this.participants.length === 0) return 0
      const total = this.participants.reduce((sum, participant) => sum + participant.average_score, 0)
      return Math.round(total / this.participants.length)
    },
    scoreDistribution() {
      const distribution = { high: 0, medium: 0, low: 0 }
      this.participants.forEach(participant => {
        const score = participant.average_score
        if (score >= 80) distribution.high++
        else if (score >= 60) distribution.medium++
        else distribution.low++
      })
      return distribution
    },
    participationRate() {
      if (this.participants.length === 0) return 0
      const activeParticipants = this.participants.filter(p => p.total_attempts > 0).length
      return Math.round((activeParticipants / this.participants.length) * 100)
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
    async refreshData() {
      this.refreshing = true;
      
      // Simulate API call delay
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // In a real application, you would fetch fresh data from the API here
      // For now, we'll just reset the refreshing state
      this.refreshing = false;
    },
    sortBy(column) {
      if (this.sortColumn === column) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortColumn = column
        this.sortDirection = 'desc'
      }
    },
    progressBadgeClass(percentage) {
      const perc = Math.round(parseFloat(percentage)) || 0
      
      if (perc >= 80) return 'progress-badge-high'
      if (perc >= 60) return 'progress-badge-medium'
      return 'progress-badge-low'
    },
    formatDate(dateString) {
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
  }
}
</script>

<style>
/* Dynamic Theme Variables */
:root {
  --primary-color: #3b82f6;
  --primary-light: #dbeafe;
  --primary-dark: #1e40af;
}

.primary-blue {
  --primary-color: #3b82f6;
  --primary-light: #dbeafe;
  --primary-dark: #1e40af;
}

.primary-green {
  --primary-color: #10b981;
  --primary-light: #dcfce7;
  --primary-dark: #047857;
}

.primary-purple {
  --primary-color: #8b5cf6;
  --primary-light: #f3e8ff;
  --primary-dark: #6d28d9;
}

.primary-red {
  --primary-color: #ef4444;
  --primary-light: #fee2e2;
  --primary-dark: #b91c1c;
}

.primary-orange {
  --primary-color: #f59e0b;
  --primary-light: #fef3c7;
  --primary-dark: #d97706;
}

/* Apply primary color to elements */
.stat-blue { background-color: var(--primary-light); }
.stat-blue .stat-number { color: var(--primary-color); }

.stat-green { background-color: var(--primary-light); }
.stat-green .stat-number { color: var(--primary-color); }

.stat-purple { background-color: var(--primary-light); }
.stat-purple .stat-number { color: var(--primary-color); }

.stat-red { background-color: var(--primary-light); }
.stat-red .stat-number { color: var(--primary-color); }

.stat-orange { background-color: var(--primary-light); }
.stat-orange .stat-number { color: var(--primary-color); }

.avatar-blue { background-color: var(--primary-light); color: var(--primary-color); }
.avatar-green { background-color: var(--primary-light); color: var(--primary-color); }
.avatar-purple { background-color: var(--primary-light); color: var(--primary-color); }
.avatar-red { background-color: var(--primary-light); color: var(--primary-color); }
.avatar-orange { background-color: var(--primary-light); color: var(--primary-color); }

.bg-blue { background-color: var(--primary-color); }
.bg-green { background-color: var(--primary-color); }
.bg-purple { background-color: var(--primary-color); }
.bg-red { background-color: var(--primary-color); }
.bg-orange { background-color: var(--primary-color); }

.table-header:hover { background-color: var(--primary-light); }
.table-header i.fas.fa-arrow-up,
.table-header i.fas.fa-arrow-down { color: var(--primary-color); }
.highlight { color: var(--primary-color); }
.info-icon { color: var(--primary-color); }
.loading-icon { color: var(--primary-color); }

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
  margin-bottom: 1rem;
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
    grid-template-columns: repeat(3, 1fr);
  }
}

.stat-card {
  border-radius: 0.5rem;
  padding: 1.5rem;
  text-align: center;
}

.stat-number {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
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

/* Search Input */
.search-input {
  padding-left: 3rem;
  padding-right: 1rem;
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  background-color: var(--bg-primary);
  color: var(--text-primary);
  width: 100%;
  max-width: 20rem;
  transition: all 0.2s ease;
}
.search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 10%, transparent);
}
.search-input::placeholder {
  color: var(--text-muted);
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
.user-avatar-small {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-right: 1rem;
}

.user-details {
  min-width: 0;
  flex: 1;
}

.user-name {
  font-weight: 500;
  color: var(--text-primary);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.quiz-category {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Score Styles */
.attempt-score {
  text-align: right;
}

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

.footer-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: var(--text-muted);
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

.distribution-label {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 0.75rem;
}

.distribution-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem;
}

.distribution-item {
  text-align: center;
  padding: 0.75rem;
  border-radius: 0.5rem;
}
.distribution-item.high {
  background-color: #dcfce7;
}
.dark-theme .distribution-item.high {
  background-color: #14532d;
}
.distribution-item.medium {
  background-color: #fef9c3;
}
.dark-theme .distribution-item.medium {
  background-color: #713f12;
}
.distribution-item.low {
  background-color: #fee2e2;
}
.dark-theme .distribution-item.low {
  background-color: #7f1d1d;
}

.distribution-count {
  font-weight: 600;
  font-size: 1.125rem;
}
.distribution-item.high .distribution-count {
  color: #16a34a;
}
.dark-theme .distribution-item.high .distribution-count {
  color: #4ade80;
}
.distribution-item.medium .distribution-count {
  color: #ca8a04;
}
.dark-theme .distribution-item.medium .distribution-count {
  color: #facc15;
}
.distribution-item.low .distribution-count {
  color: #dc2626;
}
.dark-theme .distribution-item.low .distribution-count {
  color: #f87171;
}

.distribution-range {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.completion-rate {
  text-align: center;
  padding-top: 1rem;
}

.rate-label {
  font-size: 0.875rem;
  color: var(--text-muted);
}

.rate-number {
  font-size: 1.5rem;
  font-weight: 700;
  color: #16a34a;
}
.dark-theme .rate-number {
  color: #4ade80;
}

/* Activity List */
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  border-radius: 0.5rem;
}
.activity-item:hover {
  background-color: var(--hover-bg);
}

.activity-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.activity-content {
  flex: 1;
}

.activity-message {
  font-weight: 500;
  color: var(--text-primary);
}

.activity-time {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
}

/* Refresh Button */
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
  
  .table-cell {
    padding: 0.75rem 1rem;
  }
}
</style>