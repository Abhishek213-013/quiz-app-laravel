<template>
  <div class="min-h-screen" :class="themeClass">
    <AdminNavbar 
      title="Admin Dashboard"
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
        current-page="/admin/dashboard"
        @close-mobile-sidebar="toggleMobileSidebar"
      />

      <!-- Main Content -->
      <div class="main-content">
        <!-- Content -->
        <main class="content">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <i class="fas fa-spinner fa-spin loading-icon"></i>
            <p class="loading-text">Loading dashboard data...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <i class="fas fa-exclamation-triangle error-icon"></i>
            <p class="error-text">{{ error }}</p>
            <button @click="fetchDashboardData" class="retry-btn">
              <i class="fas fa-redo"></i>
              Try Again
            </button>
          </div>

          <!-- Main Content -->
          <div v-else>
            <!-- Quick Stats Section -->
            <div class="stats-section">
              <div class="flex justify-between items-center mb-8">
                <h2 class="section-title">Quick Overview</h2>
                <button @click="fetchDashboardData" class="refresh-btn" :disabled="refreshing">
                  <i class="fas fa-sync-alt" :class="{ 'fa-spin': refreshing }"></i>
                  {{ refreshing ? 'Refreshing...' : 'Refresh Data' }}
                </button>
              </div>
              <div class="stats-grid">
                <div class="stat-card" :class="`stat-${theme.primaryColor}`">
                  <div class="stat-number">{{ displayStats.total_participants || 0 }}</div>
                  <div class="stat-label">Total Participants</div>
                </div>
                <div class="stat-card" :class="`stat-${theme.primaryColor}`">
                  <div class="stat-number">{{ displayStats.total_quiz_sets || 0 }}</div>
                  <div class="stat-label">Active Quiz Sets</div>
                </div>
                <div class="stat-card" :class="`stat-${theme.primaryColor}`">
                  <div class="stat-number">{{ displayStats.total_attempts || 0 }}</div>
                  <div class="stat-label">Total Attempts</div>
                </div>
              </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
              <!-- Pie Chart -->
              <div class="chart-card">
                <h2 class="chart-title">Quiz Set Participation</h2>
                <div v-if="quizSetDistribution.length === 0" class="no-data">
                  <i class="fas fa-chart-pie no-data-icon"></i>
                  <p>No participation data available</p>
                </div>
                <div v-else class="chart-container">
                  <canvas id="pieChart" height="300"></canvas>
                </div>
              </div>

              <!-- Bar Chart -->
              <div class="chart-card">
                <h2 class="chart-title">Participants per Day (This Week)</h2>
                <div v-if="weeklyParticipants.data.length === 0 || weeklyParticipants.data.every(val => val === 0)" class="no-data">
                  <i class="fas fa-chart-bar no-data-icon"></i>
                  <p>No weekly data available</p>
                </div>
                <div v-else class="chart-container">
                  <canvas id="barChart" height="300"></canvas>
                </div>
              </div>
            </div>

            <!-- Top Scorers Section -->
            <div class="content-card mb-12" v-if="topScorers.length > 0">
              <h2 class="card-title-large">Top Scorers This Week</h2>
              <p class="card-subtitle">Highest scores per quiz set</p>
              <div class="scorers-grid">
                <div v-for="scorer in topScorers" :key="scorer.quiz_set_id" class="scorer-card">
                  <div class="scorer-header">
                    <div class="scorer-info">
                      <h3 class="scorer-title">{{ scorer.quiz_set_name }}</h3>
                      <p class="scorer-category">{{ scorer.category }}</p>
                    </div>
                    <div class="attempts-badge" :class="`badge-${theme.primaryColor}`">
                      {{ scorer.total_attempts }} attempts
                    </div>
                  </div>
                  <div class="scorer-list">
                    <div v-for="(participant, index) in scorer.top_participants" :key="participant.participant_name" 
                         class="scorer-item">
                      <div class="scorer-rank" :class="`rank-${theme.primaryColor}`">
                        {{ index + 1 }}
                      </div>
                      <div class="scorer-details">
                        <div class="scorer-name">{{ participant.participant_name }}</div>
                        <div class="scorer-score">{{ participant.score }}/{{ participant.total_questions }}</div>
                      </div>
                      <div class="scorer-performance">
                        <div class="scorer-percentage">{{ Math.round(participant.percentage) }}%</div>
                        <div class="scorer-date">{{ formatDate(participant.created_at) }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Quiz Attempts -->
            <div class="content-card mb-12" v-if="recentAttempts.length > 0">
              <h2 class="card-title-large">Recent Quiz Attempts</h2>
              <p class="card-subtitle">Latest quiz completions</p>
              <div class="attempts-list">
                <div 
                  v-for="attempt in recentAttempts" 
                  :key="attempt.id" 
                  class="attempt-item"
                >
                  <div class="attempt-user">
                    <div class="user-avatar-small" :class="`avatar-${theme.primaryColor}`">
                      <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                      <div class="user-name">{{ attempt.participant_name }}</div>
                      <div class="quiz-name">{{ attempt.quiz_set_name }}</div>
                      <div class="quiz-category">{{ attempt.category }}</div>
                    </div>
                  </div>
                  <div class="attempt-score">
                    <div class="score-value">{{ attempt.score }}/{{ attempt.total_questions }}</div>
                    <div class="score-date">{{ formatDate(attempt.created_at) }}</div>
                  </div>
                  <div :class="progressBadgeClass(attempt.percentage)" class="progress-badge">
                    {{ Math.round(attempt.percentage) }}%
                  </div>
                </div>
              </div>
            </div>

            <!-- Performance Overview -->
            <div class="performance-section">
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

              <!-- Performance Overview -->
              <div class="content-card">
                <h2 class="card-title-large">Performance Overview</h2>
                <div class="performance-content">
                  <div class="average-score">
                    <div class="score-number" :class="`text-${theme.primaryColor}`">
                      {{ isNaN(averageScore) ? 0 : averageScore }}%
                    </div>
                    <div class="score-label">Average Score</div>
                  </div>
                  
                  <div class="score-distribution">
                    <div class="distribution-label">Score Distribution</div>
                    <div class="distribution-grid">
                      <div class="distribution-item high">
                        <div class="distribution-count">{{ topBucketCounts.high }}</div>
                        <div class="distribution-range">80%+</div>
                      </div>
                      <div class="distribution-item medium">
                        <div class="distribution-count">{{ topBucketCounts.medium }}</div>
                        <div class="distribution-range">60–79%</div>
                      </div>
                      <div class="distribution-item low">
                        <div class="distribution-count">{{ topBucketCounts.low }}</div>
                        <div class="distribution-range">Below 60%</div>
                      </div>
                    </div>
                  </div>

                  <div class="completion-rate">
                    <div class="rate-label">Completion Rate</div>
                    <div class="rate-number" :class="`text-${theme.primaryColor}`">{{ completionRate }}%</div>
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
import Chart from 'chart.js/auto';
import AdminNavbar from './AdminNavbar.vue';
import AdminSidebar from './AdminSidebar.vue';

export default {
  name: 'Dashboard',
  components: {
    AdminNavbar,
    AdminSidebar
  },
  props: {
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
      loading: true,
      refreshing: false,
      error: null,
      displayStats: {},
      quizSetDistribution: [],
      weeklyParticipants: { labels: [], data: [] },
      topScorers: [],
      recentAttempts: [],
      chartInstances: {
        pie: null,
        bar: null
      },
      recentActivities: [
        {
          id: 1,
          message: "New Geography quiz set added",
          time: "2 hours ago",
          icon: "fas fa-globe-americas",
        },
        {
          id: 2,
          message: "Science & Technology article published",
          time: "5 hours ago",
          icon: "fas fa-flask",
        },
        {
          id: 3,
          message: "History quiz completed by 15 participants",
          time: "1 day ago",
          icon: "fas fa-history",
        },
        {
          id: 4,
          message: "New World Facts article available",
          time: "2 days ago",
          icon: "fas fa-book-open",
        }
      ]
    }
  },
  computed: {
    themeClass() {
      // Use the theme from props, fallback to isDark for backward compatibility
      const colorScheme = this.theme?.colorScheme || (this.isDark ? 'dark' : 'light');
      return `${colorScheme}-theme primary-${this.theme?.primaryColor || 'blue'}`;
    },
    averageScore() {
      const arr = this.recentAttempts
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
    },
    completionRate() {
      const participants = this.displayStats.total_participants || 0
      const attempts = this.displayStats.total_attempts || 0
      if (participants === 0 || attempts === 0) return 0
      return Math.min(100, Math.round((attempts / participants) * 100))
    },
    topBucketCounts() {
      const arr = this.recentAttempts
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
    }
  },
  watch: {
    theme: {
      handler(newTheme) {
        // Re-render charts when theme changes
        this.$nextTick(() => {
          this.renderCharts();
        });
      },
      deep: true
    }
  },
  mounted() {
    this.fetchDashboardData();
  },
  beforeUnmount() {
    this.cleanupCharts();
  },
  methods: {
    async fetchDashboardData() {
      try {
        this.loading = true;
        this.error = null;
        this.refreshing = true;

        // Clear existing data
        this.displayStats = {};
        this.quizSetDistribution = [];
        this.weeklyParticipants = { labels: [], data: [] };
        this.topScorers = [];
        this.recentAttempts = [];

        // Use the correct API endpoint - try multiple possible endpoints
        let response;
        try {
          // First try the main dashboard data endpoint
          response = await axios.get('/admin/dashboard-data');
        } catch (firstError) {
          console.log('First endpoint failed, trying alternative...');
          // Fallback to alternative endpoint
          response = await axios.get('/admin/dashboard/data');
        }
        
        if (response.data) {
          const data = response.data;
          
          // Update component data with API response
          this.displayStats = data.stats || {};
          this.quizSetDistribution = data.quiz_set_distribution || [];
          this.weeklyParticipants = data.weekly_participants || { labels: [], data: [] };
          this.topScorers = data.top_scorers || [];
          this.recentAttempts = data.recent_attempts || [];

          // Render charts after data is loaded
          this.$nextTick(() => {
            this.renderCharts();
          });

        } else {
          throw new Error('Invalid response format');
        }

      } catch (error) {
        console.error('Error fetching dashboard data:', error);
        this.error = this.getErrorMessage(error);
        
        // Fallback to demo data if API fails
        this.loadDemoData();
      } finally {
        this.loading = false;
        this.refreshing = false;
      }
    },

    getErrorMessage(error) {
      if (error.response) {
        // Server responded with error status
        switch (error.response.status) {
          case 401:
            return 'Unauthorized. Please login again.';
          case 403:
            return 'Access denied.';
          case 404:
            return 'Dashboard API endpoint not found. Please check your routes.';
          case 500:
            return 'Server error. Please try again later.';
          default:
            return error.response.data?.message || 'Failed to fetch data from server.';
        }
      } else if (error.request) {
        // Request was made but no response received
        return 'Network error. Please check your connection.';
      } else {
        // Something else happened
        return error.message || 'An unexpected error occurred.';
      }
    },

    loadDemoData() {
      console.log('Loading demo data as fallback...');
      
      this.quizSetDistribution = [
        { quiz_set_name: 'Geography', participant_count: 40 },
        { quiz_set_name: 'Science', participant_count: 35 },
        { quiz_set_name: 'Sports', participant_count: 25 },
        { quiz_set_name: 'Movies', participant_count: 30 },
        { quiz_set_name: 'Mixed', participant_count: 20 }
      ];

      this.weeklyParticipants = {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        data: [12, 18, 9, 15, 22, 11, 19]
      };

      this.topScorers = [
        {
          quiz_set_id: 1,
          quiz_set_name: 'World Geography',
          category: 'Geography',
          total_attempts: 15,
          top_participants: [
            { participant_name: 'John Smith', score: 18, total_questions: 20, percentage: 90, created_at: new Date() },
            { participant_name: 'Sarah Johnson', score: 17, total_questions: 20, percentage: 85, created_at: new Date(Date.now() - 86400000) },
            { participant_name: 'Mike Davis', score: 16, total_questions: 20, percentage: 80, created_at: new Date(Date.now() - 172800000) }
          ]
        }
      ];

      this.recentAttempts = [
        {
          id: 1,
          participant_name: 'John Smith',
          quiz_set_name: 'World Geography',
          category: 'Geography',
          score: 18,
          total_questions: 20,
          percentage: 90,
          created_at: new Date()
        }
      ];

      this.displayStats = {
        total_participants: 31,
        total_quiz_sets: 6,
        total_attempts: 32
      };

      // Render charts with demo data
      this.$nextTick(() => {
        this.renderCharts();
      });
    },

    toggleTheme() {
      this.isDark = !this.isDark;
      // Re-render charts when theme changes
      this.$nextTick(() => {
        this.renderCharts();
      });
    },

    toggleMobileSidebar() {
      this.mobileSidebar = !this.mobileSidebar;
    },

    handleLogout() {
      this.$inertia.post('/admin/logout');
    },

    renderCharts() {
      this.$nextTick(() => {
        this.renderPieChart();
        this.renderBarChart();
      });
    },

    renderPieChart() {
      const canvas = document.getElementById('pieChart');
      if (!canvas) return;

      // Destroy existing instance safely
      if (this.chartInstances.pie) {
        this.chartInstances.pie.destroy();
      }

      const labels = this.quizSetDistribution.length
        ? this.quizSetDistribution.map(s => s.quiz_set_name)
        : ['Geography', 'Science', 'Sports', 'Movies', 'History'];

      const data = this.quizSetDistribution.length
        ? this.quizSetDistribution.map(s => s.participant_count)
        : [45, 32, 28, 36, 22];

      // Use theme colors for chart
      const themeColors = this.getThemeColors();

      this.chartInstances.pie = new Chart(canvas.getContext('2d'), {
        type: 'pie',
        data: {
          labels,
          datasets: [{
            data,
            backgroundColor: themeColors.chartColors
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { 
                usePointStyle: true,
                color: this.getTextColor()
              }
            }
          }
        }
      });
    },

    renderBarChart() {
      const canvas = document.getElementById('barChart');
      if (!canvas) return;

      if (this.chartInstances.bar) {
        this.chartInstances.bar.destroy();
      }

      const labels = this.weeklyParticipants.labels.length
        ? this.weeklyParticipants.labels
        : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

      const data = this.weeklyParticipants.data.length
        ? this.weeklyParticipants.data
        : [15, 22, 18, 25, 30, 12, 8];

      const themeColors = this.getThemeColors();

      this.chartInstances.bar = new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Participants',
            data,
            backgroundColor: themeColors.primary,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: { 
                stepSize: 5,
                color: this.getTextColor()
              },
              grid: {
                color: this.getBorderColor()
              }
            },
            x: {
              ticks: {
                color: this.getTextColor()
              },
              grid: {
                color: this.getBorderColor()
              }
            }
          },
          plugins: { 
            legend: { 
              display: false 
            } 
          }
        }
      });
    },

    getThemeColors() {
      const primaryColor = this.theme?.primaryColor || 'blue';
      
      const colorMap = {
        blue: {
          primary: '#3b82f6',
          light: '#dbeafe',
          dark: '#1e40af',
          chartColors: ['#6366F1', '#8B5CF6', '#EC4899', '#10B981', '#F59E0B']
        },
        green: {
          primary: '#10b981',
          light: '#dcfce7',
          dark: '#047857',
          chartColors: ['#10b981', '#059669', '#047857', '#065f46', '#064e3b']
        },
        purple: {
          primary: '#8b5cf6',
          light: '#f3e8ff',
          dark: '#6d28d9',
          chartColors: ['#8b5cf6', '#7c3aed', '#6d28d9', '#5b21b6', '#4c1d95']
        },
        red: {
          primary: '#ef4444',
          light: '#fee2e2',
          dark: '#b91c1c',
          chartColors: ['#ef4444', '#dc2626', '#b91c1c', '#991b1b', '#7f1d1d']
        },
        orange: {
          primary: '#f59e0b',
          light: '#fef3c7',
          dark: '#d97706',
          chartColors: ['#f59e0b', '#d97706', '#b45309', '#92400e', '#78350f']
        }
      };

      return colorMap[primaryColor] || colorMap.blue;
    },

    getTextColor() {
      return this.theme?.colorScheme === 'dark' ? '#e5e7eb' : '#374151';
    },

    getBorderColor() {
      return this.theme?.colorScheme === 'dark' ? '#374151' : '#e5e7eb';
    },

    cleanupCharts() {
      if (this.chartInstances.pie) {
        this.chartInstances.pie.destroy();
      }
      if (this.chartInstances.bar) {
        this.chartInstances.bar.destroy();
      }
    },

    formatDate(dateString) {
      if (!dateString) return '';
      try {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        if (minutes < 60) return `${minutes}m ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours}h ago`;
        const days = Math.floor(hours / 24);
        return `${days}d ago`;
      } catch (error) {
        return 'Invalid date';
      }
    },

    progressBadgeClass(percentage) {
      const perc = Math.round(parseFloat(percentage)) || 0;
      
      if (perc >= 80) return 'progress-badge-high';
      if (perc >= 60) return 'progress-badge-medium';
      return 'progress-badge-low';
    }
  }
}
</script>

<style>
/* Import Font Awesome */

/* Theme Base Classes */
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

/* Primary Color Classes */
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
  color: var(--primary-color, #2563eb);
  margin-bottom: 1rem;
}

.loading-text {
  color: var(--text-muted);
}

/* Error State */
.error-state {
  text-align: center;
  padding: 3rem 0;
}

.error-icon {
  font-size: 1.875rem;
  color: #dc2626;
  margin-bottom: 1rem;
}

.error-text {
  color: var(--text-primary);
  margin-bottom: 1.5rem;
}

.retry-btn {
  background-color: var(--primary-color, #2563eb);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}
.retry-btn:hover {
  background-color: var(--primary-dark, #1d4ed8);
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
}
.refresh-btn:hover:not(:disabled) {
  background-color: var(--hover-bg);
}
.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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
  background-color: var(--primary-light);
  transition: all 0.3s ease;
}
.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.stat-number {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: var(--primary-dark);
}

.stat-label {
  color: var(--text-muted);
}

/* Charts Section */
.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}

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
  position: relative;
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
  margin-bottom: 3rem;
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
  gap: 1.5rem;
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
  transition: background-color 0.2s;
}
.scorer-card:hover {
  background-color: var(--hover-bg);
}

.scorer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.scorer-info {
  min-width: 0;
  flex: 1;
}

.scorer-title {
  font-weight: 600;
  color: var(--text-primary);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.scorer-category {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.attempts-badge {
  font-size: 0.875rem;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  background-color: var(--primary-light);
  color: var(--primary-dark);
  flex-shrink: 0;
  margin-left: 0.5rem;
}

.scorer-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.scorer-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem;
  border-radius: 0.375rem;
}
.scorer-item:hover {
  background-color: var(--hover-bg);
}

.scorer-rank {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background-color: var(--primary-light);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary-dark);
  font-size: 0.875rem;
  font-weight: 500;
  margin-right: 0.75rem;
  flex-shrink: 0;
}

.scorer-details {
  min-width: 0;
  flex: 1;
}

.scorer-name {
  font-weight: 500;
  color: var(--text-primary);
  font-size: 0.875rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.scorer-score {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.scorer-performance {
  text-align: right;
  margin-left: 0.75rem;
  flex-shrink: 0;
}

.scorer-percentage {
  font-weight: 700;
  color: #16a34a;
}

.scorer-date {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Attempts List */
.attempts-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.attempt-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-radius: 0.5rem;
  border: 1px solid var(--border-color);
  transition: background-color 0.2s;
}
.attempt-item:hover {
  background-color: var(--hover-bg);
}

.attempt-user {
  display: flex;
  align-items: center;
  min-width: 0;
  flex: 1;
}

.user-avatar-small {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  background-color: var(--primary-light);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary-dark);
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

.quiz-name {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.quiz-category {
  font-size: 0.75rem;
  color: var(--text-muted);
}


.attempt-score {
  text-align: right;
  margin-left: 1rem;
  flex-shrink: 0;
}

.score-value {
  font-weight: 600;
  color: var(--text-primary);
}

.score-date {
  font-size: 0.875rem;
  color: var(--text-muted);
}

.progress-badge {
  margin-left: 1rem;
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

.performance-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.average-score {
  text-align: center;
}

.score-number {
  font-size: 2.25rem;
  font-weight: 700;
  color: #2563eb;
  margin-bottom: 0.5rem;
}
.dark-theme .score-number {
  color: #60a5fa;
}

.score-label {
  color: var(--text-muted);
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

/* Ensure charts are responsive */
canvas {
  width: 100% !important;
  height: 320px !important;
}

/* No Data State */
.no-data {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 200px;
  color: var(--text-muted);
  text-align: center;
}

.no-data-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.chart-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 300px;
  color: var(--text-muted);
}
</style>