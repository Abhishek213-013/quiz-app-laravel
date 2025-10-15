<template>
  <div 
    class="min-h-screen" 
    :class="themeClass"
    :style="themeStyles"
  >
    <AdminNavbar 
      title="Records & Analytics"
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
        current-page="/admin/records"
        @close-mobile-sidebar="toggleMobileSidebar"
      />

      <!-- Main Content -->
      <div class="main-content">
        <!-- Content -->
        <main class="content">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <span class="loading-icon">⏳</span>
            <p class="loading-text">Loading records data...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <span class="error-icon">⚠️</span>
            <p class="error-text">{{ error }}</p>
            <button @click="fetchRecordsData" class="retry-button">
              <span class="mr-2">🔄</span>
              Try Again
            </button>
          </div>

          <!-- Main Content -->
          <div v-else>
            <!-- Quick Stats Section -->
            <div class="stats-section">
              <div class="flex justify-between items-center mb-8">
                <h2 class="section-title">Performance Records</h2>
                <div class="flex items-center gap-4">
                  <!-- Theme Debug Button (remove in production) -->
                  <button v-if="showDebug" @click="debugTheme" class="debug-btn">
                    <span class="mr-2">🐛</span>
                    Debug Theme
                  </button>
                  <button @click="refreshData" class="refresh-btn" :disabled="refreshing">
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
                    {{ overallStats.highestScore }}%
                  </div>
                  <div class="stat-label">Highest Score</div>
                </div>
                <div 
                  class="stat-card" 
                  :style="{ 
                    backgroundColor: getPrimaryColorLight(),
                    borderLeft: `4px solid ${primaryColorValue}` 
                  }"
                >
                  <div class="stat-number" :style="{ color: primaryColorValue }">
                    {{ overallStats.averageScore }}%
                  </div>
                  <div class="stat-label">Average Score</div>
                </div>
                <div 
                  class="stat-card" 
                  :style="{ 
                    backgroundColor: getPrimaryColorLight(),
                    borderLeft: `4px solid ${primaryColorValue}` 
                  }"
                >
                  <div class="stat-number" :style="{ color: primaryColorValue }">
                    {{ overallStats.totalParticipants }}
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
                    {{ overallStats.totalAttempts }}
                  </div>
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
                    :style="{ borderLeft: `3px solid ${primaryColorValue}` }"
                  >
                    <div class="scorer-header">
                      <div class="scorer-info">
                        <div 
                          class="scorer-rank"
                          :style="{ 
                            background: `linear-gradient(135deg, ${primaryColorValue} 0%, ${getPrimaryColorDark()} 100%)` 
                          }"
                        >
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
                          <div 
                            :class="getRankClass(index + 1)" 
                            class="rank-badge"
                            :style="getRankStyle(index + 1)"
                          >
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
                      <tr v-if="weeklyLeaderboard.length === 0">
                        <td colspan="7" class="empty-state">
                          <div class="empty-content">
                            <span class="empty-icon">🏆</span>
                            <p class="empty-title">No weekly records found</p>
                            <p class="empty-subtitle">No quiz attempts this week</p>
                          </div>
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
                      <div v-if="quizSetPerformance.highest.length === 0" class="distribution-item high">
                        <div class="distribution-count">0%</div>
                        <div class="distribution-range">No data available</div>
                        <div class="distribution-label">No quiz sets found</div>
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
                      <div v-if="quizSetPerformance.lowest.length === 0" class="distribution-item low">
                        <div class="distribution-count">0%</div>
                        <div class="distribution-range">No data available</div>
                        <div class="distribution-label">No quiz sets found</div>
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
                  <span class="search-icon">🔍</span>
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
                          <span :class="getSortIcon('participant_name')"></span>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('quiz_set_name')">
                        <div class="flex items-center space-x-2">
                          <span>Quiz Set</span>
                          <span :class="getSortIcon('quiz_set_name')"></span>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('score')">
                        <div class="flex items-center space-x-2">
                          <span>Score</span>
                          <span :class="getSortIcon('score')"></span>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('percentage')">
                        <div class="flex items-center space-x-2">
                          <span>Percentage</span>
                          <span :class="getSortIcon('percentage')"></span>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('time_taken')">
                        <div class="flex items-center space-x-2">
                          <span>Time Taken</span>
                          <span :class="getSortIcon('time_taken')"></span>
                        </div>
                      </th>
                      <th class="table-header" @click="sortRecords('created_at')">
                        <div class="flex items-center space-x-2">
                          <span>Date</span>
                          <span :class="getSortIcon('created_at')"></span>
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
                          <span class="empty-icon">📊</span>
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
      searchParticipant: '',
      selectedQuizSet: '',
      sortField: 'created_at',
      sortDirection: 'desc',
      overallStats: {
        highestScore: 0,
        averageScore: 0,
        totalParticipants: 0,
        totalAttempts: 0
      },
      topPerformers: { allTime: [] },
      weeklyLeaderboard: [],
      quizSetPerformance: { highest: [], lowest: [] },
      allRecords: [],
      quizSets: [],
      showDebug: process.env.NODE_ENV === 'development',
      isInitialized: false
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
  watch: {
    theme: {
      handler(newTheme, oldTheme) {
        // Only process theme changes after component is initialized
        if (!this.isInitialized) return;
        
        // Only re-render if theme actually changed
        if (JSON.stringify(newTheme) !== JSON.stringify(oldTheme)) {
          console.log('Theme changed, re-rendering components...');
          this.handleThemeChange();
        }
      },
      deep: true,
      immediate: false // Don't run immediately on component creation
    }
  },
  mounted() {
    // Mark component as initialized
    this.isInitialized = true;
    this.fetchRecordsData();
  },
  beforeUnmount() {
    this.cleanupCharts();
  },
  methods: {
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
      // Use nextTick to ensure DOM is ready
      this.$nextTick(() => {
        // Force update to ensure CSS variables are applied
        this.$forceUpdate();
      });
    },
    debugTheme() {
      console.log('=== THEME DEBUG INFO ===');
      console.log('Theme Props:', this.theme);
      console.log('Computed Theme Class:', this.themeClass);
      console.log('Primary Color Value:', this.primaryColorValue);
      console.log('Is Dark Mode:', this.theme?.colorScheme === 'dark');
      console.log('Current Layout:', this.theme?.layout);
      console.log('Theme Styles:', this.themeStyles);
      console.log('========================');
      
      alert(`Current Theme:
Color Scheme: ${this.theme?.colorScheme}
Primary Color: ${this.theme?.primaryColor}
Layout: ${this.theme?.layout}
Primary Color Value: ${this.primaryColorValue}`);
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
    getRankStyle(rank) {
      if (rank === 1) {
        return {
          background: `linear-gradient(135deg, ${this.primaryColorValue} 0%, ${this.getPrimaryColorDark()} 100%)`,
          color: 'white'
        };
      }
      if (rank === 2) {
        return {
          background: 'linear-gradient(135deg, #9ca3af 0%, #6b7280 100%)',
          color: 'white'
        };
      }
      if (rank === 3) {
        return {
          background: 'linear-gradient(135deg, #b45309 0%, #92400e 100%)',
          color: 'white'
        };
      }
      return {
        backgroundColor: this.getPrimaryColorLight(),
        color: this.primaryColorValue
      };
    },
    async refreshData() {
      this.refreshing = true;
      
      try {
        this.error = null;
        await this.fetchRecordsData();
      } catch (error) {
        this.error = 'Failed to refresh data. Please try again.';
      } finally {
        this.refreshing = false;
      }
    },
    async fetchRecordsData() {
      try {
        this.loading = true;
        this.error = null;
        
        // Fetch all records from your API
        const response = await fetch('/api/quiz-results');
        
        if (!response.ok) {
          throw new Error(`Failed to fetch records: ${response.status}`);
        }
        
        const allRecords = await response.json();
        
        // Transform the data to match your component structure
        this.allRecords = allRecords.map(record => ({
          id: record.id,
          participant_name: record.participant_name,
          quiz_set_name: record.quiz_set?.name || 'Unknown Quiz Set',
          category: record.quiz_set?.category || 'General',
          quiz_set_id: record.quiz_set_id,
          score: record.score,
          total_questions: record.total_questions,
          percentage: parseFloat(record.percentage),
          time_taken: record.time_taken,
          created_at: new Date(record.created_at)
        }));

        // Fetch quiz sets for the filter dropdown
        await this.fetchQuizSets();
        
        // Calculate statistics
        this.calculateOverallStats();
        this.calculateTopPerformers();
        this.calculateWeeklyLeaderboard();
        this.calculateQuizSetPerformance();

        // Render charts
        setTimeout(this.renderCharts, 100);

      } catch (error) {
        console.error('Error fetching records data:', error);
        this.error = 'Failed to load records data. Please check your connection and try again.';
        // Fallback to demo data if API fails
        await this.loadDemoData();
      } finally {
        this.loading = false;
      }
    },

    async fetchQuizSets() {
      try {
        const response = await fetch('/api/quiz-sets');
        
        if (!response.ok) {
          throw new Error(`Failed to fetch quiz sets: ${response.status}`);
        }
        
        this.quizSets = await response.json();
      } catch (error) {
        console.error('Error fetching quiz sets:', error);
        // Fallback to extracting from records
        const uniqueSets = {};
        this.allRecords.forEach(record => {
          if (record.quiz_set_id && !uniqueSets[record.quiz_set_id]) {
            uniqueSets[record.quiz_set_id] = {
              id: record.quiz_set_id,
              name: record.quiz_set_name
            };
          }
        });
        this.quizSets = Object.values(uniqueSets);
      }
    },

    loadDemoData() {
      console.warn('Using demo data - API might be unavailable');
      
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
      ];

      this.quizSets = [
        { id: 1, name: 'World Geography' },
        { id: 2, name: 'Basic Science' },
        { id: 3, name: 'Movie Trivia' }
      ];
    },

    calculateOverallStats() {
      const records = this.allRecords;
      
      if (records.length === 0) {
        this.overallStats = {
          highestScore: 0,
          averageScore: 0,
          totalParticipants: 0,
          totalAttempts: 0
        };
        return;
      }

      const percentages = records.map(record => parseFloat(record.percentage));
      const uniqueParticipants = new Set(records.map(r => r.participant_name));
      
      this.overallStats = {
        highestScore: Math.max(...percentages),
        averageScore: Math.round(percentages.reduce((sum, percentage) => sum + percentage, 0) / percentages.length),
        totalParticipants: uniqueParticipants.size,
        totalAttempts: records.length
      };
    },

    calculateTopPerformers() {
      const records = this.allRecords;
      
      if (records.length === 0) {
        this.topPerformers.allTime = [];
        return;
      }

      this.topPerformers.allTime = records
        .sort((a, b) => parseFloat(b.percentage) - parseFloat(a.percentage))
        .slice(0, 6);
    },

    calculateWeeklyLeaderboard() {
      const oneWeekAgo = new Date();
      oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
      
      const weeklyRecords = this.allRecords
        .filter(record => new Date(record.created_at) >= oneWeekAgo);
        
      this.weeklyLeaderboard = weeklyRecords
        .sort((a, b) => parseFloat(b.percentage) - parseFloat(a.percentage))
        .slice(0, 10);
    },

    calculateQuizSetPerformance() {
      const records = this.allRecords;
      
      if (records.length === 0) {
        this.quizSetPerformance = { highest: [], lowest: [] };
        return;
      }

      const setPerformance = {};
      
      // Group records by quiz set
      records.forEach(record => {
        if (!setPerformance[record.quiz_set_id]) {
          setPerformance[record.quiz_set_id] = {
            quiz_set_id: record.quiz_set_id,
            quiz_set_name: record.quiz_set_name || `Quiz Set ${record.quiz_set_id}`,
            category: record.category || 'General',
            percentages: []
          };
        }
        setPerformance[record.quiz_set_id].percentages.push({
          percentage: parseFloat(record.percentage),
          participant_name: record.participant_name
        });
      });
      
      // Calculate highest and lowest for each set
      const highest = [];
      const lowest = [];
      
      Object.values(setPerformance).forEach(set => {
        if (set.percentages.length > 0) {
          const percentages = set.percentages.map(p => p.percentage);
          const highestScore = Math.max(...percentages);
          const lowestScore = Math.min(...percentages);
          
          highest.push({
            ...set,
            highest_percentage: highestScore,
            top_performer: set.percentages.find(p => p.percentage === highestScore)?.participant_name || 'Unknown'
          });
          
          lowest.push({
            ...set,
            lowest_percentage: lowestScore,
            lowest_performer: set.percentages.find(p => p.percentage === lowestScore)?.participant_name || 'Unknown'
          });
        }
      });
      
      this.quizSetPerformance.highest = highest.sort((a, b) => b.highest_percentage - a.highest_percentage);
      this.quizSetPerformance.lowest = lowest.sort((a, b) => a.lowest_percentage - b.lowest_percentage);
    },

    renderCharts() {
      this.renderScoreDistributionChart();
      this.renderWeeklyTrendChart();
    },

    renderScoreDistributionChart() {
      const ctx = document.getElementById('scoreDistributionChart');
      if (!ctx) return;

      const records = this.allRecords;
      
      if (records.length === 0) return;

      const scoreRanges = [
        { range: '90-100%', min: 90, max: 100 },
        { range: '80-89%', min: 80, max: 89 },
        { range: '70-79%', min: 70, max: 79 },
        { range: '60-69%', min: 60, max: 69 },
        { range: '50-59%', min: 50, max: 59 },
        { range: '0-49%', min: 0, max: 49 }
      ];

      const data = scoreRanges.map(range => {
        return records.filter(record => {
          const percentage = parseFloat(record.percentage);
          return percentage >= range.min && percentage <= range.max;
        }).length;
      });

      // Destroy existing chart if it exists
      if (ctx.chartInstance) {
        ctx.chartInstance.destroy();
      }

      ctx.chartInstance = new Chart(ctx, {
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
      });
    },

    renderWeeklyTrendChart() {
      const ctx = document.getElementById('weeklyTrendChart');
      if (!ctx) return;

      // Calculate actual weekly data from records
      const last7Days = Array.from({ length: 7 }, (_, i) => {
        const date = new Date();
        date.setDate(date.getDate() - i);
        return date.toLocaleDateString('en-US', { weekday: 'short' });
      }).reverse();

      const averageScores = [];
      const attemptCounts = [];

      last7Days.forEach((day, index) => {
        const targetDate = new Date();
        targetDate.setDate(targetDate.getDate() - (6 - index));
        
        const dayRecords = this.allRecords.filter(record => {
          const recordDate = new Date(record.created_at);
          return recordDate.toDateString() === targetDate.toDateString();
        });

        attemptCounts.push(dayRecords.length);

        if (dayRecords.length > 0) {
          const avgScore = dayRecords.reduce((sum, record) => sum + parseFloat(record.percentage), 0) / dayRecords.length;
          averageScores.push(Math.round(avgScore));
        } else {
          averageScores.push(0);
        }
      });

      // Destroy existing chart if it exists
      if (ctx.chartInstance) {
        ctx.chartInstance.destroy();
      }

      ctx.chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: last7Days,
          datasets: [
            {
              label: 'Average Score (%)',
              data: averageScores,
              borderColor: this.primaryColorValue,
              backgroundColor: this.hexToRgba(this.primaryColorValue, 0.1),
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
      });
    },

    hexToRgba(hex, alpha) {
      const r = parseInt(hex.slice(1, 3), 16);
      const g = parseInt(hex.slice(3, 5), 16);
      const b = parseInt(hex.slice(5, 7), 16);
      return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    },

    cleanupCharts() {
      const charts = ['scoreDistributionChart', 'weeklyTrendChart'];
      charts.forEach(chartId => {
        const canvas = document.getElementById(chartId);
        if (canvas && canvas.chartInstance) {
          canvas.chartInstance.destroy();
        }
      });
    },

    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },

    progressBadgeClass(percentage) {
      const perc = parseFloat(percentage);
      if (isNaN(perc)) return 'progress-badge-low';
      
      if (perc >= 80) return 'progress-badge-high';
      if (perc >= 60) return 'progress-badge-medium';
      return 'progress-badge-low';
    },

    getRankClass(rank) {
      if (rank === 1) return 'rank-gold';
      if (rank === 2) return 'rank-silver';
      if (rank === 3) return 'rank-bronze';
      return 'rank-other';
    },

    getSortIcon(field) {
      if (this.sortField !== field) return 'text-gray-400';
      return this.sortDirection === 'asc' ? 'text-blue-600' : 'text-blue-600';
    },

    sortRecords(field) {
      if (this.sortField === field) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortField = field;
        this.sortDirection = 'desc';
      }
    }
  }
}
</script>

<style scoped>
/* All existing styles remain exactly the same */

/* Only adding the refresh button and debug button styles */
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

/* Add spin animation for refresh */
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

/* Theme transition for smooth changes */
.min-h-screen,
.content-card,
.stat-card,
.chart-card,
.scorer-card,
.table-row,
.distribution-item {
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
}

/* All other existing styles remain unchanged */
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
  font-size: 3rem;
  color: #ef4444;
  margin-bottom: 1rem;
}

.error-text {
  color: var(--text-secondary);
  margin-bottom: 1.5rem;
  font-size: 1.125rem;
}

.retry-button {
  background-color: var(--primary-color, #3b82f6);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  border: none;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.retry-button:hover {
  background-color: var(--primary-dark, #2563eb);
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
  border-radius: 0.75rem;
  box-shadow: var(--shadow-lg);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
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
  border-radius: 0.75rem;
  padding: 1rem;
  transition: all 0.2s ease;
  background-color: var(--bg-primary);
  border-left: 3px solid;
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
  border-color: var(--primary-color, #3b82f6);
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
  color: var(--primary-color, #3b82f6);
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

/* Utility classes */
.mb-8 {
  margin-bottom: 2rem;
}

.mb-4 {
  margin-bottom: 1rem;
}

.flex {
  display: flex;
}

.items-center {
  align-items: center;
}

.justify-between {
  justify-content: space-between;
}

.space-x-2 > * + * {
  margin-left: 0.5rem;
}

.gap-4 {
  gap: 1rem;
}

/* Text Colors */
.text-gray-400 {
  color: #9ca3af;
}

.text-blue-600 {
  color: #2563eb;
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
}
</style>