<template>
  <div 
    class="min-h-screen" 
    :class="themeClass"
    :style="themeStyles"
  >
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
            <span class="loading-icon">⏳</span>
            <p class="loading-text">Loading dashboard data...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <span class="error-icon">⚠️</span>
            <p class="error-text">{{ error }}</p>
            <button @click="fetchDashboardData" class="retry-btn">
              <span class="mr-2">🔄</span>
              Try Again
            </button>
          </div>

          <!-- Main Content -->
          <div v-else>
            <!-- Quick Stats Section -->
            <div class="stats-section">
              <div class="flex justify-between items-center mb-8">
                <h2 class="section-title">Quick Overview</h2>
                <div class="flex items-center gap-4">
                 
                  <button @click="fetchDashboardData" class="refresh-btn" :disabled="refreshing">
                    <span class="mr-2" :class="{ 'animate-spin': refreshing }">🔄</span>
                    {{ refreshing ? 'Refreshing...' : 'Refresh Data' }}
                  </button>
                </div>
              </div>
              <div class="stats-grid">
                <div 
                  class="stat-card" 
                  :style="{ 
                    backgroundColor: getPrimaryColorLight(),
                    borderLeft: `4px solid ${primaryColorValue}` 
                  }"
                >
                  <div class="stat-number" :style="{ color: primaryColorValue }">
                    {{ displayStats.total_participants || 0 }}
                  </div>
                  <div class="stat-label">Total Participants</div>
                </div>
                <div 
                  class="stat-card" 
                  :style="{ 
                    backgroundColor: getPrimaryColorLight(),
                    borderLeft: `4px solid ${primaryColorValue}` 
                  }"
                >
                  <div class="stat-number" :style="{ color: primaryColorValue }">
                    {{ displayStats.total_quiz_sets || 0 }}
                  </div>
                  <div class="stat-label">Active Quiz Sets</div>
                </div>
                <div 
                  class="stat-card" 
                  :style="{ 
                    backgroundColor: getPrimaryColorLight(),
                    borderLeft: `4px solid ${primaryColorValue}` 
                  }"
                >
                  <div class="stat-number" :style="{ color: primaryColorValue }">
                    {{ displayStats.total_attempts || 0 }}
                  </div>
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
                  <span class="no-data-icon">📊</span>
                  <p>No participation data available</p>
                </div>
                <div v-else class="chart-container">
                  <canvas 
                    :id="`pieChart-${componentId}`" 
                    :key="`pie-${componentId}`"
                    height="300"
                  ></canvas>
                </div>
              </div>

              <!-- Bar Chart -->
              <div class="chart-card">
                <h2 class="chart-title">Participants per Day (This Week)</h2>
                <div v-if="weeklyParticipants.data.length === 0 || weeklyParticipants.data.every(val => val === 0)" class="no-data">
                  <span class="no-data-icon">📈</span>
                  <p>No weekly data available</p>
                </div>
                <div v-else class="chart-container">
                  <canvas 
                    :id="`barChart-${componentId}`" 
                    :key="`bar-${componentId}`"
                    height="300"
                  ></canvas>
                </div>
              </div>
            </div>

            <!-- Top Scorers Section -->
            <div class="content-card mb-12" v-if="topScorers.length > 0">
              <h2 class="card-title-large">🏆 Top Scorers This Week</h2>
              <p class="card-subtitle">Highest scores per quiz set</p>
              <div class="scorers-grid">
                <div 
                  v-for="scorer in topScorers" 
                  :key="scorer.quiz_set_id" 
                  class="scorer-card"
                  :style="{ borderLeft: `3px solid ${primaryColorValue}` }"
                >
                  <div class="scorer-header">
                    <div class="scorer-info">
                      <h3 class="scorer-title">{{ scorer.quiz_set_name }}</h3>
                      <p class="scorer-category">{{ scorer.category }}</p>
                    </div>
                    <div 
                      class="attempts-badge" 
                      :style="{ 
                        backgroundColor: getPrimaryColorLight(),
                        color: primaryColorValue 
                      }"
                    >
                      {{ scorer.total_attempts }} attempts
                    </div>
                  </div>
                  <div class="scorer-list">
                    <div 
                      v-for="(participant, index) in scorer.top_participants" 
                      :key="participant.participant_name" 
                      class="scorer-item"
                    >
                      <div 
                        class="scorer-rank" 
                        :style="{ 
                          backgroundColor: getPrimaryColorLight(),
                          color: primaryColorValue 
                        }"
                      >
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
              <h2 class="card-title-large">📝 Recent Quiz Attempts</h2>
              <p class="card-subtitle">Latest quiz completions</p>
              <div class="attempts-list">
                <div 
                  v-for="attempt in recentAttempts" 
                  :key="attempt.id" 
                  class="attempt-item"
                >
                  <div class="attempt-user">
                    <div 
                      class="user-avatar-small" 
                      :style="{ 
                        backgroundColor: getPrimaryColorLight(),
                        color: primaryColorValue 
                      }"
                    >
                      <span>👤</span>
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
                <h2 class="card-title-large">📋 Recent Activity</h2>
                <div class="activity-list">
                  <div 
                    v-for="activity in recentActivities" 
                    :key="activity.id" 
                    class="activity-item"
                  >
                    <div 
                      class="activity-icon" 
                      :style="{ backgroundColor: primaryColorValue }"
                    >
                      <span class="text-white text-sm">{{ activity.icon }}</span>
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
                <h2 class="card-title-large">📊 Performance Overview</h2>
                <div class="performance-content">
                  <div class="average-score">
                    <div class="score-number" :style="{ color: primaryColorValue }">
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
                    <div class="rate-number" :style="{ color: primaryColorValue }">{{ completionRate }}%</div>
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
          icon: "🌎",
        },
        {
          id: 2,
          message: "Science & Technology article published",
          time: "5 hours ago",
          icon: "🧪",
        },
        {
          id: 3,
          message: "History quiz completed by 15 participants",
          time: "1 day ago",
          icon: "📜",
        },
        {
          id: 4,
          message: "New World Facts article available",
          time: "2 days ago",
          icon: "📚",
        }
      ],
      showDebug: process.env.NODE_ENV === 'development',
      _isMounted: false,
      resizeTimeout: null,
      chartRenderTimeout: null,
      componentId: Math.random().toString(36).substr(2, 9), // Unique ID for canvas elements
      chartAnimationFrame: null
    }
  },
  computed: {
    themeClass() {
      const colorScheme = this.theme?.colorScheme || (this.isDark ? 'dark' : 'light');
      const primaryColor = this.theme?.primaryColor || 'blue';
      const layout = this.theme?.layout || 'sidebar';
      
      return `${colorScheme}-theme primary-${primaryColor} layout-${layout}`;
    },
    
    themeStyles() {
      return {
        '--primary-color': this.primaryColorValue,
        '--primary-light': this.getPrimaryColorLight(),
        '--primary-dark': this.getPrimaryColorDark()
      };
    },
    
    primaryColorValue() {
      const colorMap = {
        blue: '#3b82f6',
        green: '#10b981', 
        purple: '#8b5cf6',
        red: '#ef4444',
        orange: '#f59e0b'
      };
      return colorMap[this.theme?.primaryColor] || colorMap.blue;
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
      handler(newTheme, oldTheme) {
        if (JSON.stringify(newTheme) !== JSON.stringify(oldTheme)) {
          console.log('Theme changed, re-rendering components...');
          this.handleThemeChange();
        }
      },
      deep: true,
      immediate: true
    }
  },
  mounted() {
    this._isMounted = true;
    this.fetchDashboardData();
    window.addEventListener('resize', this.handleResize);
  },
  beforeUnmount() {
    this._isMounted = false;
    
    // Cancel any pending animation frame
    if (this.chartAnimationFrame) {
      cancelAnimationFrame(this.chartAnimationFrame);
      this.chartAnimationFrame = null;
    }
    
    // Clear any pending timeouts
    clearTimeout(this.resizeTimeout);
    clearTimeout(this.chartRenderTimeout);
    
    // Destroy charts immediately
    this.destroyChartsImmediately();
    
    window.removeEventListener('resize', this.handleResize);
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

        let response;
        try {
          response = await axios.get('/admin/dashboard-data');
        } catch (firstError) {
          console.log('First endpoint failed, trying alternative...');
          response = await axios.get('/admin/dashboard/data');
        }
        
        if (response.data) {
          const data = response.data;
          
          this.displayStats = data.stats || {};
          this.quizSetDistribution = data.quiz_set_distribution || [];
          this.weeklyParticipants = data.weekly_participants || { labels: [], data: [] };
          this.topScorers = data.top_scorers || [];
          this.recentAttempts = data.recent_attempts || [];

          this.$nextTick(() => {
            this.safeChartRender();
          });

        } else {
          throw new Error('Invalid response format');
        }

      } catch (error) {
        console.error('Error fetching dashboard data:', error);
        this.error = this.getErrorMessage(error);
        this.loadDemoData();
      } finally {
        this.loading = false;
        this.refreshing = false;
      }
    },

    getErrorMessage(error) {
      if (error.response) {
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
        return 'Network error. Please check your connection.';
      } else {
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
        },
        {
          quiz_set_id: 2,
          quiz_set_name: 'Science Fundamentals',
          category: 'Science',
          total_attempts: 12,
          top_participants: [
            { participant_name: 'Emily Wilson', score: 19, total_questions: 20, percentage: 95, created_at: new Date() },
            { participant_name: 'David Brown', score: 18, total_questions: 20, percentage: 90, created_at: new Date(Date.now() - 86400000) },
            { participant_name: 'Lisa Taylor', score: 17, total_questions: 20, percentage: 85, created_at: new Date(Date.now() - 259200000) }
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
        },
        {
          id: 2,
          participant_name: 'Emily Wilson',
          quiz_set_name: 'Science Fundamentals',
          category: 'Science',
          score: 19,
          total_questions: 20,
          percentage: 95,
          created_at: new Date(Date.now() - 3600000)
        },
        {
          id: 3,
          participant_name: 'Mike Davis',
          quiz_set_name: 'World Geography',
          category: 'Geography',
          score: 16,
          total_questions: 20,
          percentage: 80,
          created_at: new Date(Date.now() - 7200000)
        }
      ];

      this.displayStats = {
        total_participants: 31,
        total_quiz_sets: 6,
        total_attempts: 32
      };

      this.$nextTick(() => {
        this.safeChartRender();
      });
    },

    toggleTheme() {
      this.isDark = !this.isDark;
      this.handleThemeChange();
    },

    toggleMobileSidebar() {
      this.mobileSidebar = !this.mobileSidebar;
    },

    handleLogout() {
      this.$inertia.post('/admin/logout');
    },

    handleThemeChange() {
      if (!this._isMounted) return;
      
      clearTimeout(this.chartRenderTimeout);
      this.chartRenderTimeout = setTimeout(() => {
        if (this._isMounted) {
          this.safeChartRender();
        }
      }, 100);
    },

    handleResize() {
      if (!this._isMounted) return;
      
      clearTimeout(this.resizeTimeout);
      this.resizeTimeout = setTimeout(() => {
        if (this._isMounted) {
          this.safeChartRender();
        }
      }, 250);
    },

    safeChartRender() {
      if (!this._isMounted) return;
      
      try {
        // Cancel any pending animation frame
        if (this.chartAnimationFrame) {
          cancelAnimationFrame(this.chartAnimationFrame);
        }
        
        this.chartAnimationFrame = requestAnimationFrame(() => {
          if (!this._isMounted) return;
          this.renderCharts();
        });
      } catch (error) {
        console.warn('Chart rendering failed:', error);
      }
    },

    renderCharts() {
      if (!this._isMounted) return;
      
      try {
        this.cleanupCharts();
        
        // Small delay to ensure DOM is ready
        setTimeout(() => {
          if (!this._isMounted) return;
          this.renderPieChart();
          this.renderBarChart();
        }, 50);
      } catch (error) {
        console.error('Error in renderCharts:', error);
      }
    },

    renderPieChart() {
      if (!this._isMounted) return;
      
      const canvasId = `pieChart-${this.componentId}`;
      const canvas = document.getElementById(canvasId);
      
      if (!canvas) {
        console.warn('Pie chart canvas not found');
        return;
      }

      // Additional safety check
      let context;
      try {
        context = canvas.getContext('2d');
        if (!context) {
          console.warn('Cannot get canvas context for pie chart');
          return;
        }
      } catch (error) {
        console.warn('Error getting canvas context for pie chart:', error);
        return;
      }

      const themeColors = this.getThemeColors();
      const isDark = this.theme?.colorScheme === 'dark';
      
      const labels = this.quizSetDistribution.length
        ? this.quizSetDistribution.map(s => s.quiz_set_name)
        : ['Geography', 'Science', 'Sports', 'Movies', 'History'];

      const data = this.quizSetDistribution.length
        ? this.quizSetDistribution.map(s => s.participant_count)
        : [45, 32, 28, 36, 22];

      try {
        this.chartInstances.pie = new Chart(context, {
          type: 'pie',
          data: {
            labels,
            datasets: [{
              data,
              backgroundColor: themeColors.chartColors,
              borderColor: isDark ? '#374151' : '#ffffff',
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
              duration: 0 // Disable animations to prevent ctx issues
            },
            plugins: {
              legend: {
                position: 'bottom',
                labels: { 
                  usePointStyle: true,
                  color: this.getTextColor(),
                  font: {
                    size: 11
                  }
                }
              },
              tooltip: {
                backgroundColor: isDark ? '#1f2937' : '#ffffff',
                titleColor: isDark ? '#e5e7eb' : '#374151',
                bodyColor: isDark ? '#e5e7eb' : '#374151',
                borderColor: isDark ? '#374151' : '#e5e7eb',
                borderWidth: 1
              }
            }
          }
        });
      } catch (error) {
        console.error('Error creating pie chart:', error);
        this.chartInstances.pie = null;
      }
    },

    renderBarChart() {
      if (!this._isMounted) return;
      
      const canvasId = `barChart-${this.componentId}`;
      const canvas = document.getElementById(canvasId);
      
      if (!canvas) {
        console.warn('Bar chart canvas not found');
        return;
      }

      let context;
      try {
        context = canvas.getContext('2d');
        if (!context) {
          console.warn('Cannot get canvas context for bar chart');
          return;
        }
      } catch (error) {
        console.warn('Error getting canvas context for bar chart:', error);
        return;
      }

      const themeColors = this.getThemeColors();
      const isDark = this.theme?.colorScheme === 'dark';

      const labels = this.weeklyParticipants.labels.length
        ? this.weeklyParticipants.labels
        : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

      const data = this.weeklyParticipants.data.length
        ? this.weeklyParticipants.data
        : [15, 22, 18, 25, 30, 12, 8];

      try {
        this.chartInstances.bar = new Chart(context, {
          type: 'bar',
          data: {
            labels,
            datasets: [{
              label: 'Participants',
              data,
              backgroundColor: themeColors.primary,
              borderRadius: 6,
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
              duration: 0 // Disable animations to prevent ctx issues
            },
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
                  color: this.getBorderColor(),
                  display: false
                }
              }
            },
            plugins: { 
              legend: { 
                display: false 
              },
              tooltip: {
                backgroundColor: isDark ? '#1f2937' : '#ffffff',
                titleColor: isDark ? '#e5e7eb' : '#374151',
                bodyColor: isDark ? '#e5e7eb' : '#374151',
                borderColor: isDark ? '#374151' : '#e5e7eb',
                borderWidth: 1
              }
            }
          }
        });
      } catch (error) {
        console.error('Error creating bar chart:', error);
        this.chartInstances.bar = null;
      }
    },

    cleanupCharts() {
      Object.entries(this.chartInstances).forEach(([key, chart]) => {
        if (chart) {
          try {
            // Stop any ongoing animations first
            if (chart.stop) {
              chart.stop();
            }
            // Then destroy the chart
            chart.destroy();
          } catch (error) {
            console.warn(`Error destroying ${key} chart:`, error);
          }
          this.chartInstances[key] = null;
        }
      });
    },

    destroyChartsImmediately() {
      // Immediate destruction without safety checks for unmount
      Object.entries(this.chartInstances).forEach(([key, chart]) => {
        if (chart) {
          try {
            // Stop animations immediately
            if (chart.stop) {
              chart.stop();
            }
            // Clear the canvas
            if (chart.ctx && chart.canvas) {
              chart.ctx.clearRect(0, 0, chart.canvas.width, chart.canvas.height);
            }
            // Destroy chart
            chart.destroy();
          } catch (error) {
            // Silent fail during unmount
          }
          this.chartInstances[key] = null;
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
          chartColors: ['#3b82f6', '#60a5fa', '#93c5fd', '#1d4ed8', '#1e40af', '#2563eb']
        },
        green: {
          primary: '#10b981',
          light: '#dcfce7',
          dark: '#047857',
          chartColors: ['#10b981', '#34d399', '#6ee7b7', '#059669', '#047857', '#065f46']
        },
        purple: {
          primary: '#8b5cf6',
          light: '#f3e8ff',
          dark: '#6d28d9',
          chartColors: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#7c3aed', '#6d28d9', '#5b21b6']
        },
        red: {
          primary: '#ef4444',
          light: '#fee2e2',
          dark: '#b91c1c',
          chartColors: ['#ef4444', '#f87171', '#fca5a5', '#dc2626', '#b91c1c', '#991b1b']
        },
        orange: {
          primary: '#f59e0b',
          light: '#fef3c7',
          dark: '#d97706',
          chartColors: ['#f59e0b', '#fbbf24', '#fcd34d', '#d97706', '#b45309', '#92400e']
        }
      };

      return colorMap[primaryColor] || colorMap.blue;
    },

    getPrimaryColorLight() {
      const colorMap = {
        blue: '#dbeafe',
        green: '#dcfce7',
        purple: '#f3e8ff',
        red: '#fee2e2',
        orange: '#fef3c7'
      };
      return colorMap[this.theme?.primaryColor] || colorMap.blue;
    },

    getPrimaryColorDark() {
      const colorMap = {
        blue: '#1e40af',
        green: '#047857',
        purple: '#6d28d9',
        red: '#b91c1c',
        orange: '#d97706'
      };
      return colorMap[this.theme?.primaryColor] || colorMap.blue;
    },

    getTextColor() {
      return this.theme?.colorScheme === 'dark' ? '#e5e7eb' : '#374151';
    },

    getBorderColor() {
      return this.theme?.colorScheme === 'dark' ? '#374151' : '#e5e7eb';
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
    },

    debugTheme() {
      console.log('=== THEME DEBUG INFO ===');
      console.log('Theme Props:', this.theme);
      console.log('Computed Theme Class:', this.themeClass);
      console.log('Primary Color Value:', this.primaryColorValue);
      console.log('Is Dark Mode:', this.theme?.colorScheme === 'dark');
      console.log('Current Layout:', this.theme?.layout);
      console.log('Component Mounted:', this._isMounted);
      console.log('Chart Instances:', this.chartInstances);
      console.log('Component ID:', this.componentId);
      console.log('========================');
    }
  }
}
</script>

<style>
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



.layout-topbar .main-content {
  margin-left: 0;
}

.layout-topbar .content {
  padding-top: 5rem;
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

/* Theme transition for smooth changes */
.min-h-screen,
.content-card,
.stat-card,
.chart-card,
.scorer-card,
.attempt-item,
.activity-item {
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
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
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.retry-btn:hover {
  background-color: var(--primary-dark, #1d4ed8);
}

/* Debug Button */
.debug-btn {
  background-color: #6b7280;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  border: none;
  cursor: pointer;
  font-size: 0.875rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
}

.debug-btn:hover {
  background-color: #4b5563;
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
  display: inline-flex;
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
  border-radius: 0.75rem;
  padding: 1.5rem;
  text-align: center;
  background-color: var(--bg-secondary);
  box-shadow: var(--shadow-lg);
  transition: all 0.3s ease;
  border-left: 4px solid;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.dark-theme .stat-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
}

.stat-number {
  font-size: 2.25rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.stat-label {
  color: var(--text-muted);
  font-size: 0.875rem;
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
  color: var(--text-primary);
  margin-bottom: 1rem;
}

/* Content Cards */
.content-card {
  background-color: var(--bg-secondary);
  border-radius: 0.75rem;
  box-shadow: var(--shadow-lg);
  padding: 1.5rem;
  margin-bottom: 3rem;
  transition: all 0.3s ease;
}

.content-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.dark-theme .content-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
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
  border-radius: 0.75rem;
  padding: 1.25rem;
  transition: all 0.3s ease;
  background-color: var(--bg-primary);
  border-left: 3px solid;
}

.scorer-card:hover {
  background-color: var(--hover-bg);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.scorer-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1rem;
  gap: 1rem;
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
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.scorer-category {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.attempts-badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 9999px;
  font-weight: 500;
  flex-shrink: 0;
}

.scorer-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.scorer-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem;
  border-radius: 0.5rem;
  transition: background-color 0.2s;
}

.scorer-item:hover {
  background-color: var(--hover-bg);
}

.scorer-rank {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  font-weight: 600;
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
  margin-bottom: 0.125rem;
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
  font-size: 0.875rem;
}

.scorer-date {
  font-size: 0.75rem;
  color: var(--text-muted);
  margin-top: 0.125rem;
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
  border-radius: 0.75rem;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  background-color: var(--bg-primary);
}

.attempt-item:hover {
  background-color: var(--hover-bg);
  transform: translateX(4px);
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
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-right: 1rem;
  font-size: 1rem;
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
  margin-bottom: 0.25rem;
}

.quiz-name {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin-bottom: 0.125rem;
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
  font-size: 1rem;
}

.score-date {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
}

.progress-badge {
  margin-left: 1rem;
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  flex-shrink: 0;
  min-width: 4rem;
  text-align: center;
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
  padding: 1rem;
  border-radius: 0.75rem;
  transition: all 0.3s ease;
}

.activity-item:hover {
  background-color: var(--hover-bg);
  transform: translateX(4px);
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
  font-size: 0.875rem;
}

.activity-time {
  font-size: 0.75rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
}

.performance-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.average-score {
  text-align: center;
  padding: 1.5rem;
  background-color: var(--bg-primary);
  border-radius: 0.75rem;
}

.score-number {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.score-label {
  color: var(--text-muted);
  font-size: 0.875rem;
}

.score-distribution {
  margin-top: 1rem;
}

.distribution-label {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 1rem;
  text-align: center;
}

.distribution-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem;
}

.distribution-item {
  text-align: center;
  padding: 1rem 0.5rem;
  border-radius: 0.75rem;
  transition: transform 0.2s;
}

.distribution-item:hover {
  transform: scale(1.05);
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
  font-weight: 700;
  font-size: 1.25rem;
  margin-bottom: 0.25rem;
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
  padding: 1.5rem;
  background-color: var(--bg-primary);
  border-radius: 0.75rem;
}

.rate-label {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 0.5rem;
}

.rate-number {
  font-size: 1.875rem;
  font-weight: 700;
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

/* Flex Utilities */
.flex {
  display: flex;
}

.flex-col {
  flex-direction: column;
}

.items-center {
  align-items: center;
}

.justify-between {
  justify-content: space-between;
}

.justify-end {
  justify-content: flex-end;
}

/* Spacing */
.mb-8 {
  margin-bottom: 2rem;
}

.mb-12 {
  margin-bottom: 3rem;
}

.mr-2 {
  margin-right: 0.5rem;
}

.mr-3 {
  margin-right: 0.75rem;
}

.mt-4 {
  margin-top: 1rem;
}

/* Gap Utilities */
.gap-4 {
  gap: 1rem;
}

.gap-6 {
  gap: 1.5rem;
}

/* Text Colors */
.text-white {
  color: white;
}

.text-sm {
  font-size: 0.875rem;
}

/* Capitalize */
.capitalize {
  text-transform: capitalize;
}

/* Hidden */
.hidden {
  display: none;
}

/* Responsive Design */
@media (min-width: 768px) {
  .md\:flex-row {
    flex-direction: row;
  }
  
  .md\:items-center {
    align-items: center;
  }
  
  .md\:justify-between {
    justify-content: space-between;
  }
  
  .md\:mt-0 {
    margin-top: 0;
  }
}

@media (min-width: 1024px) {
  .lg\:grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  
  .lg\:col-span-2 {
    grid-column: span 2 / span 2;
  }
}

/* Chart loading state */
.chart-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 300px;
  color: var(--text-muted);
}

/* Animation for refresh icon */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>