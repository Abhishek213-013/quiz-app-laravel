<template>
  <div 
    class="min-h-screen" 
    :class="themeClass"
    :style="themeStyles"
  >
    <AdminNavbar 
      title="System Settings"
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
        current-page="/admin/settings"
        @close-mobile-sidebar="toggleMobileSidebar"
      />

      <!-- Main Content -->
      <div class="main-content">
        <main class="content">
          <!-- Header Section -->
          <div class="content-card mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div>
                <h1 class="card-title-large">System Settings</h1>
                <p class="card-subtitle">Configure application settings and preferences</p>
              </div>
              <div class="flex items-center gap-4 mt-4 md:mt-0">
                <!-- Theme Debug Button -->
                <button @click="debugTheme" class="debug-btn">
                  <span class="mr-2">🐛</span>
                  Debug Theme
                </button>
                <button 
                  @click="saveSettings" 
                  class="btn-primary"
                  :disabled="loading"
                  :style="{ 
                    background: `linear-gradient(135deg, ${primaryColorValue} 0%, ${getPrimaryColorDark()} 100%)` 
                  }"
                >
                  <span class="mr-2">💾</span>
                  {{ loading ? 'Saving...' : 'Save All Settings' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Debug Info Card -->
          <div class="content-card mb-8 bg-yellow-50 border border-yellow-200">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-yellow-800">Theme Debug Info</h3>
                  <p class="text-yellow-600 text-sm">
                    Current Theme: {{ theme?.colorScheme }} | {{ theme?.primaryColor }} | {{ theme?.layout }}
                  </p>
                  <p class="text-yellow-600 text-sm">
                    Primary Color Value: {{ primaryColorValue }}
                  </p>
                </div>
                <button @click="reloadPage" class="btn-primary text-sm">
                  🔄 Reload
                </button>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1">
              <div class="content-card">
                <div class="card-header">
                  <span class="mr-3">⚙️</span>
                  <h2 class="card-title">Settings</h2>
                </div>
                <nav class="settings-nav">
                  <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="settings-nav-item group"
                    :class="{ 'settings-nav-item-active': activeTab === tab.id }"
                    :style="activeTab === tab.id ? { 
                      background: `linear-gradient(135deg, ${primaryColorValue} 0%, ${getPrimaryColorDark()} 100%)` 
                    } : {}"
                  >
                    <div class="flex items-center">
                      <div 
                        class="settings-nav-icon" 
                        :class="activeTab === tab.id ? 'text-white' : 'text-gray-600 group-hover:text-blue-600'"
                        :style="activeTab === tab.id ? { 
                          backgroundColor: 'rgba(255, 255, 255, 0.2)'
                        } : { 
                          backgroundColor: getPrimaryColorLight() 
                        }"
                      >
                        <span>{{ tab.icon }}</span>
                      </div>
                      <span class="settings-nav-text">{{ tab.name }}</span>
                    </div>
                    <span class="settings-nav-arrow" :class="activeTab === tab.id ? 'text-white' : 'text-gray-400 group-hover:text-blue-600'">›</span>
                  </button>
                </nav>
              </div>

              <!-- Quick Stats -->
              <div class="content-card mt-6">
                <div class="card-header">
                  <span class="mr-3">📊</span>
                  <h2 class="card-title">Quick Stats</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-4">
                    <div class="stat-item">
                      <div 
                        class="stat-icon"
                        :style="{ 
                          backgroundColor: getPrimaryColorLight(),
                          color: primaryColorValue 
                        }"
                      >
                        <span>👥</span>
                      </div>
                      <div class="stat-content">
                        <div class="stat-value">1,234</div>
                        <div class="stat-label">Active Users</div>
                      </div>
                    </div>
                    <div class="stat-item">
                      <div 
                        class="stat-icon"
                        :style="{ 
                          backgroundColor: getPrimaryColorLight(),
                          color: primaryColorValue 
                        }"
                      >
                        <span>❓</span>
                      </div>
                      <div class="stat-content">
                        <div class="stat-value">56</div>
                        <div class="stat-label">Quizzes</div>
                      </div>
                    </div>
                    <div class="stat-item">
                      <div 
                        class="stat-icon"
                        :style="{ 
                          backgroundColor: getPrimaryColorLight(),
                          color: primaryColorValue 
                        }"
                      >
                        <span>💾</span>
                      </div>
                      <div class="stat-content">
                        <div class="stat-value">2.5GB</div>
                        <div class="stat-label">Storage Used</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-3">
              <!-- Your existing settings content remains the same -->
              <!-- ... -->
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script>
import AdminNavbar from './AdminNavbar.vue'
import AdminSidebar from './AdminSidebar.vue'

export default {
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
    settings: {
      type: Object,
      default: () => ({})
    },
    // FIXED: Theme is now being passed from controller
    theme: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      // Use the theme from props
      isDark: this.currentTheme?.colorScheme === 'dark',
      mobileSidebar: false,
      loading: false,
      activeTab: 'general',
      tabs: [
        { id: 'general', name: 'General', icon: '⚙️' },
        { id: 'security', name: 'Security', icon: '🛡️' },
        { id: 'notifications', name: 'Notifications', icon: '🔔' }
      ],
      showDebug: true,
      isInitialized: false
    }
  },
  computed: {
    // FIXED: Use the theme from props
    currentTheme() {
      return this.theme || this.$page.props.customization || {
        colorScheme: 'light',
        primaryColor: 'blue', 
        layout: 'sidebar'
      };
    },
    
    themeClass() {
      const colorScheme = this.currentTheme.colorScheme;
      const primaryColor = this.currentTheme.primaryColor;
      const layout = this.currentTheme.layout;
      
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
      return colorMap[this.currentTheme.primaryColor] || '#3b82f6';
    }
  },
  watch: {
    // Watch for theme changes
    theme: {
      handler(newTheme) {
        if (!this.isInitialized) return;
        console.log('Theme prop changed:', newTheme);
        this.updateTheme();
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    updateTheme() {
      this.isDark = this.currentTheme.colorScheme === 'dark';
      this.$nextTick(() => {
        this.$forceUpdate();
      });
    },
    
    debugTheme() {
      console.log('=== SYSTEM SETTINGS THEME DEBUG ===');
      console.log('Theme Prop:', this.theme);
      console.log('Global Customization:', this.$page.props.customization);
      console.log('Current Theme:', this.currentTheme);
      console.log('Computed Theme Class:', this.themeClass);
      
      alert(`SYSTEM SETTINGS THEME DEBUG:
      
Theme Prop: ${JSON.stringify(this.theme)}
Global Customization: ${JSON.stringify(this.$page.props.customization)}
Current Theme: ${JSON.stringify(this.currentTheme)}
Computed Theme Class: ${this.themeClass}
Primary Color Value: ${this.primaryColorValue}

This should now match your current theme!`);
    },
    
    // ... rest of your methods remain the same
    getPrimaryColorLight() {
      const colorMap = {
        blue: '#dbeafe',
        green: '#dcfce7',
        purple: '#f3e8ff',
        red: '#fee2e2',
        orange: '#fef3c7'
      };
      return colorMap[this.currentTheme.primaryColor] || '#dbeafe';
    },
    
    getPrimaryColorDark() {
      const colorMap = {
        blue: '#1e40af',
        green: '#047857',
        purple: '#6d28d9',
        red: '#b91c1c',
        orange: '#d97706'
      };
      return colorMap[this.currentTheme.primaryColor] || '#1e40af';
    },
    
    async saveSettings() {
      this.loading = true;
      try {
        await new Promise(resolve => setTimeout(resolve, 1500));
        this.showSuccess('Settings saved successfully!');
      } catch (error) {
        this.showError('Failed to save settings. Please try again.');
      } finally {
        this.loading = false;
      }
    },
    
    showSuccess(message) {
      alert(message);
    },
    
    showError(message) {
      alert(message);
    }
  },
  mounted() {
    this.isInitialized = true;
    console.log('=== SYSTEM SETTINGS MOUNTED ===');
    console.log('Theme Prop:', this.theme);
    console.log('All props:', this.$props);
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

/* Theme transition for smooth changes */
.min-h-screen,
.content-card,
.settings-nav-item,
.toggle-card,
.notification-type-item,
.btn-primary {
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
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

/* Content Cards */
.content-card {
  background-color: var(--bg-secondary);
  border-radius: 0.75rem;
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  transition: all 0.3s ease;
}

.content-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.dark-theme .content-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
}

.card-header {
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
}

.card-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.card-title-large {
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.card-subtitle {
  font-size: 1rem;
  color: var(--text-muted);
  margin: 0;
}

.card-body {
  padding: 1.5rem;
}

/* Settings Navigation */
.settings-nav {
  padding: 0.5rem;
}

.settings-nav-item {
  width: 100%;
  padding: 1rem;
  text-align: left;
  border-radius: 0.5rem;
  color: var(--text-muted);
  font-weight: 500;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: none;
  background: none;
  cursor: pointer;
  margin-bottom: 0.5rem;
}

.settings-nav-item:hover {
  background-color: var(--hover-bg);
  color: var(--text-primary);
  transform: translateX(4px);
}

.settings-nav-item-active {
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.settings-nav-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  transition: all 0.3s ease;
}

.settings-nav-text {
  font-size: 0.875rem;
  font-weight: 500;
}

.settings-nav-arrow {
  font-size: 0.75rem;
  transition: all 0.3s ease;
}

/* Quick Stats */
.stat-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--border-color);
}

.stat-item:last-child {
  border-bottom: none;
}

.stat-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Settings Sections */
.settings-section {
  padding-bottom: 2rem;
  border-bottom: 1px solid var(--border-color);
}

.settings-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
}

.section-description {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
}

/* Form Styles */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  background-color: var(--bg-primary);
  color: var(--text-primary);
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color, #3b82f6);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  background-color: var(--bg-secondary);
}

.form-help {
  font-size: 0.75rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
}

/* Toggle Cards */
.toggle-card {
  background-color: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 0.75rem;
  padding: 1.25rem;
  transition: all 0.3s ease;
}

.toggle-card:hover {
  border-color: var(--text-muted);
  box-shadow: var(--shadow);
}

.toggle-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.toggle-info {
  flex: 1;
  margin-right: 1rem;
}

.toggle-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  display: flex;
  align-items: center;
}

.toggle-description {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.maintenance-warning {
  background-color: #fef3c7;
  border: 1px solid #f59e0b;
  border-radius: 0.5rem;
  padding: 0.75rem;
  margin-top: 1rem;
  display: flex;
  align-items: center;
  font-size: 0.875rem;
  color: #92400e;
}

.dark-theme .maintenance-warning {
  background-color: #451a03;
  border-color: #d97706;
  color: #fbbf24;
}

/* Toggle Switch */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 3.5rem;
  height: 2rem;
  flex-shrink: 0;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #d1d5db;
  transition: .4s;
  border-radius: 2rem;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 1.5rem;
  width: 1.5rem;
  left: 0.25rem;
  bottom: 0.25rem;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

input:checked + .toggle-slider:before {
  transform: translateX(1.5rem);
}

/* Notification Types */
.notification-types-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 768px) {
  .notification-types-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.notification-type-item {
  border: 1px solid var(--border-color);
  border-radius: 0.75rem;
  overflow: hidden;
  transition: all 0.3s ease;
}

.notification-type-item:hover {
  border-color: var(--text-muted);
  box-shadow: var(--shadow);
}

.notification-type-label {
  display: block;
  cursor: pointer;
  padding: 1.25rem;
}

.notification-type-checkbox {
  display: none;
}

.notification-type-checkbox:checked + .notification-type-content {
  opacity: 1;
}

.notification-type-content {
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
}

.notification-type-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  transition: all 0.3s ease;
}

.notification-type-info {
  flex: 1;
}

.notification-type-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.notification-type-description {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Buttons */
.btn-primary {
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-large {
  padding: 1rem 2rem;
  font-size: 1rem;
}

/* Grid Utilities */
.grid {
  display: grid;
}

.grid-cols-1 {
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

.gap-8 {
  gap: 2rem;
}

.gap-6 {
  gap: 1.5rem;
}

.space-y-8 > * + * {
  margin-top: 2rem;
}

.space-y-4 > * + * {
  margin-top: 1rem;
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

.mt-4 {
  margin-top: 1rem;
}

.mt-6 {
  margin-top: 1.5rem;
}

.mt-8 {
  margin-top: 2rem;
}

.mr-2 {
  margin-right: 0.5rem;
}

.mr-3 {
  margin-right: 0.75rem;
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
  
  .md\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 1024px) {
  .lg\:grid-cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  
  .lg\:col-span-1 {
    grid-column: span 1 / span 1;
  }
  
  .lg\:col-span-3 {
    grid-column: span 3 / span 3;
  }
}

/* Text Colors */
.text-blue-500 {
  color: #3b82f6;
}

.text-green-500 {
  color: #10b981;
}

.text-yellow-500 {
  color: #eab308;
}

.text-purple-500 {
  color: #8b5cf6;
}

.text-orange-500 {
  color: #f59e0b;
}

.text-red-500 {
  color: #ef4444;
}

.text-gray-400 {
  color: #9ca3af;
}

.text-gray-600 {
  color: #4b5563;
}

.text-white {
  color: white;
}

/* Background Colors */
.bg-blue-100 {
  background-color: #dbeafe;
}

.bg-green-100 {
  background-color: #dcfce7;
}

.bg-purple-100 {
  background-color: #f3e8ff;
}

.bg-orange-100 {
  background-color: #ffedd5;
}

.bg-red-100 {
  background-color: #fee2e2;
}

.bg-gray-100 {
  background-color: #f3f4f6;
}

/* Group Hover */
.group:hover .group-hover\:bg-blue-100 {
  background-color: #dbeafe;
}

.group:hover .group-hover\:text-blue-600 {
  color: #2563eb;
}

/* Transitions */
.transition-all {
  transition: all 0.3s ease;
}

.transition-colors {
  transition: background-color 0.3s ease, color 0.3s ease;
}

.group {
  position: relative;
}
</style>