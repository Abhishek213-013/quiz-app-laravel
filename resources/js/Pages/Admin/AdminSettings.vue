<template>
  <div class="min-h-screen" :class="isDark ? 'dark-theme' : 'light-theme'">
    <AdminNavbar 
      title="System Settings"
      :is-dark="isDark"
      @toggle-theme="toggleTheme"
      @toggle-mobile-sidebar="toggleMobileSidebar"
      @logout="handleLogout"
    />

    <div class="flex">
      <AdminSidebar 
        :mobile-sidebar="mobileSidebar"
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
              <div class="mt-4 md:mt-0">
                <button 
                  @click="saveSettings" 
                  class="btn-primary"
                  :disabled="loading"
                >
                  <i class="fas fa-save mr-2"></i>
                  {{ loading ? 'Saving...' : 'Save All Settings' }}
                </button>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1">
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-sliders-h text-blue-500 mr-3"></i>
                  <h2 class="card-title">Settings</h2>
                </div>
                <nav class="settings-nav">
                  <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="settings-nav-item group"
                    :class="{ 'settings-nav-item-active': activeTab === tab.id }"
                  >
                    <div class="flex items-center">
                      <div class="settings-nav-icon" :class="activeTab === tab.id ? 'bg-white text-blue-600' : 'bg-gray-100 text-gray-600 group-hover:bg-blue-100 group-hover:text-blue-600'">
                        <i :class="tab.icon"></i>
                      </div>
                      <span class="settings-nav-text">{{ tab.name }}</span>
                    </div>
                    <i class="fas fa-chevron-right settings-nav-arrow" :class="activeTab === tab.id ? 'text-white' : 'text-gray-400 group-hover:text-blue-600'"></i>
                  </button>
                </nav>
              </div>

              <!-- Quick Stats -->
              <div class="content-card mt-6">
                <div class="card-header">
                  <i class="fas fa-chart-bar text-green-500 mr-3"></i>
                  <h2 class="card-title">Quick Stats</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-4">
                    <div class="stat-item">
                      <div class="stat-icon bg-blue-100 text-blue-600">
                        <i class="fas fa-users"></i>
                      </div>
                      <div class="stat-content">
                        <div class="stat-value">1,234</div>
                        <div class="stat-label">Active Users</div>
                      </div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-icon bg-green-100 text-green-600">
                        <i class="fas fa-question-circle"></i>
                      </div>
                      <div class="stat-content">
                        <div class="stat-value">56</div>
                        <div class="stat-label">Quizzes</div>
                      </div>
                    </div>
                    <div class="stat-item">
                      <div class="stat-icon bg-purple-100 text-purple-600">
                        <i class="fas fa-database"></i>
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
              <!-- General Settings -->
              <div v-if="activeTab === 'general'" class="content-card">
                <div class="card-header">
                  <i class="fas fa-cog text-blue-500 mr-3"></i>
                  <h2 class="card-title">General Settings</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-8">
                    <!-- Application Info -->
                    <div class="settings-section">
                      <h3 class="section-title">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Application Information
                      </h3>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                          <label class="form-label">
                            <i class="fas fa-tag text-gray-400 mr-2"></i>
                            Application Name
                          </label>
                          <input
                            type="text"
                            v-model="settings.general.appName"
                            class="form-input"
                            placeholder="Enter application name"
                          />
                        </div>
                        <div class="form-group">
                          <label class="form-label">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>
                            Admin Email
                          </label>
                          <input
                            type="email"
                            v-model="settings.general.adminEmail"
                            class="form-input"
                            placeholder="Enter admin email"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- System Configuration -->
                    <div class="settings-section">
                      <h3 class="section-title">
                        <i class="fas fa-wrench text-green-500 mr-2"></i>
                        System Configuration
                      </h3>
                      <div class="form-group">
                        <label class="form-label">
                          <i class="fas fa-globe text-gray-400 mr-2"></i>
                          Timezone
                        </label>
                        <select v-model="settings.general.timezone" class="form-input">
                          <option value="UTC">UTC (Coordinated Universal Time)</option>
                          <option value="EST">Eastern Time (EST/EDT)</option>
                          <option value="PST">Pacific Time (PST/PDT)</option>
                          <option value="CET">Central European Time (CET)</option>
                          <option value="IST">Indian Standard Time (IST)</option>
                        </select>
                      </div>
                    </div>

                    <!-- System Status -->
                    <div class="settings-section">
                      <h3 class="section-title">
                        <i class="fas fa-server text-orange-500 mr-2"></i>
                        System Status
                      </h3>
                      <div class="toggle-card">
                        <div class="toggle-content">
                          <div class="toggle-info">
                            <h4 class="toggle-title">Maintenance Mode</h4>
                            <p class="toggle-description">Put the application in maintenance mode. Users will see a maintenance page.</p>
                          </div>
                          <label class="toggle-switch">
                            <input type="checkbox" v-model="settings.general.maintenanceMode" />
                            <span class="toggle-slider"></span>
                          </label>
                        </div>
                        <div v-if="settings.general.maintenanceMode" class="maintenance-warning">
                          <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                          Application is in maintenance mode
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Security Settings -->
              <div v-if="activeTab === 'security'" class="content-card">
                <div class="card-header">
                  <i class="fas fa-shield-alt text-green-500 mr-3"></i>
                  <h2 class="card-title">Security Settings</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-8">
                    <!-- Authentication -->
                    <div class="settings-section">
                      <h3 class="section-title">
                        <i class="fas fa-lock text-green-500 mr-2"></i>
                        Authentication
                      </h3>
                      <div class="space-y-4">
                        <div class="toggle-card">
                          <div class="toggle-content">
                            <div class="toggle-info">
                              <h4 class="toggle-title">Two-Factor Authentication</h4>
                              <p class="toggle-description">Require 2FA for all admin accounts for enhanced security</p>
                            </div>
                            <label class="toggle-switch">
                              <input type="checkbox" v-model="settings.security.twoFactorAuth" />
                              <span class="toggle-slider"></span>
                            </label>
                          </div>
                        </div>

                        <div class="toggle-card">
                          <div class="toggle-content">
                            <div class="toggle-info">
                              <h4 class="toggle-title">Password Policy</h4>
                              <p class="toggle-description">Enforce strong password requirements (min. 8 characters, mixed case, numbers)</p>
                            </div>
                            <label class="toggle-switch">
                              <input type="checkbox" v-model="settings.security.passwordPolicy" />
                              <span class="toggle-slider"></span>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Session Management -->
                    <div class="settings-section">
                      <h3 class="section-title">
                        <i class="fas fa-clock text-purple-500 mr-2"></i>
                        Session Management
                      </h3>
                      <div class="form-group">
                        <label class="form-label">
                          <i class="fas fa-hourglass-half text-gray-400 mr-2"></i>
                          Session Timeout (minutes)
                        </label>
                        <input
                          type="number"
                          v-model="settings.security.sessionTimeout"
                          class="form-input"
                          min="5"
                          max="480"
                        />
                        <p class="form-help">Automatic logout after period of inactivity (5-480 minutes)</p>
                      </div>
                    </div>

                    <!-- IP Restrictions -->
                    <div class="settings-section">
                      <h3 class="section-title">
                        <i class="fas fa-network-wired text-red-500 mr-2"></i>
                        IP Restrictions
                      </h3>
                      <div class="form-group">
                        <label class="form-label">
                          <i class="fas fa-lock text-gray-400 mr-2"></i>
                          Allowed IP Addresses
                        </label>
                        <textarea
                          v-model="settings.security.allowedIPs"
                          rows="4"
                          class="form-input"
                          placeholder="Enter one IP address per line&#10;Example:&#10;192.168.1.1&#10;10.0.0.50"
                        ></textarea>
                        <p class="form-help">Leave empty to allow access from any IP address. One IP per line.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notification Settings -->
              <div v-if="activeTab === 'notifications'" class="content-card">
                <div class="card-header">
                  <i class="fas fa-bell text-yellow-500 mr-3"></i>
                  <h2 class="card-title">Notification Settings</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-8">
                    <!-- Notification Channels -->
                    <div class="settings-section">
                      <h3 class="section-title">
                        <i class="fas fa-broadcast-tower text-yellow-500 mr-2"></i>
                        Notification Channels
                      </h3>
                      <div class="space-y-4">
                        <div class="toggle-card">
                          <div class="toggle-content">
                            <div class="toggle-info">
                              <h4 class="toggle-title">
                                <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                Email Notifications
                              </h4>
                              <p class="toggle-description">Receive email alerts for important system events and activities</p>
                            </div>
                            <label class="toggle-switch">
                              <input type="checkbox" v-model="settings.notifications.emailEnabled" />
                              <span class="toggle-slider"></span>
                            </label>
                          </div>
                        </div>

                        <div class="toggle-card">
                          <div class="toggle-content">
                            <div class="toggle-info">
                              <h4 class="toggle-title">
                                <i class="fas fa-bell text-green-500 mr-2"></i>
                                Push Notifications
                              </h4>
                              <p class="toggle-description">Receive real-time browser push notifications</p>
                            </div>
                            <label class="toggle-switch">
                              <input type="checkbox" v-model="settings.notifications.pushEnabled" />
                              <span class="toggle-slider"></span>
                            </label>
                          </div>
                        </div>

                        <div class="toggle-card">
                          <div class="toggle-content">
                            <div class="toggle-info">
                              <h4 class="toggle-title">
                                <i class="fas fa-sms text-purple-500 mr-2"></i>
                                SMS Notifications
                              </h4>
                              <p class="toggle-description">Receive SMS alerts for critical system events (requires SMS gateway)</p>
                            </div>
                            <label class="toggle-switch">
                              <input type="checkbox" v-model="settings.notifications.smsEnabled" />
                              <span class="toggle-slider"></span>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Notification Types -->
                    <div class="settings-section">
                      <h3 class="section-title">
                        <i class="fas fa-filter text-purple-500 mr-2"></i>
                        Notification Types
                      </h3>
                      <p class="section-description">Choose which types of events should trigger notifications</p>
                      <div class="notification-types-grid">
                        <div class="notification-type-item">
                          <label class="notification-type-label">
                            <input type="checkbox" v-model="settings.notifications.types.newUser" class="notification-type-checkbox" />
                            <div class="notification-type-content">
                              <div class="notification-type-icon bg-blue-100 text-blue-600">
                                <i class="fas fa-user-plus"></i>
                              </div>
                              <div class="notification-type-info">
                                <h4 class="notification-type-title">New User Registrations</h4>
                                <p class="notification-type-description">When new users register on the platform</p>
                              </div>
                            </div>
                          </label>
                        </div>

                        <div class="notification-type-item">
                          <label class="notification-type-label">
                            <input type="checkbox" v-model="settings.notifications.types.quizAttempt" class="notification-type-checkbox" />
                            <div class="notification-type-content">
                              <div class="notification-type-icon bg-green-100 text-green-600">
                                <i class="fas fa-trophy"></i>
                              </div>
                              <div class="notification-type-info">
                                <h4 class="notification-type-title">Quiz Attempts</h4>
                                <p class="notification-type-description">When users complete quizzes with high scores</p>
                              </div>
                            </div>
                          </label>
                        </div>

                        <div class="notification-type-item">
                          <label class="notification-type-label">
                            <input type="checkbox" v-model="settings.notifications.types.systemAlert" class="notification-type-checkbox" />
                            <div class="notification-type-content">
                              <div class="notification-type-icon bg-orange-100 text-orange-600">
                                <i class="fas fa-exclamation-triangle"></i>
                              </div>
                              <div class="notification-type-info">
                                <h4 class="notification-type-title">System Alerts</h4>
                                <p class="notification-type-description">Important system events and warnings</p>
                              </div>
                            </div>
                          </label>
                        </div>

                        <div class="notification-type-item">
                          <label class="notification-type-label">
                            <input type="checkbox" v-model="settings.notifications.types.securityAlert" class="notification-type-checkbox" />
                            <div class="notification-type-content">
                              <div class="notification-type-icon bg-red-100 text-red-600">
                                <i class="fas fa-shield-alt"></i>
                              </div>
                              <div class="notification-type-info">
                                <h4 class="notification-type-title">Security Alerts</h4>
                                <p class="notification-type-description">Security-related events and potential threats</p>
                              </div>
                            </div>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Save Button -->
              <div class="flex justify-end mt-8">
                <button 
                  @click="saveSettings" 
                  class="btn-primary btn-large"
                  :disabled="loading"
                >
                  <i class="fas fa-save mr-2"></i>
                  {{ loading ? 'Saving Changes...' : 'Save All Settings' }}
                </button>
              </div>
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
  data() {
    return {
      isDark: false,
      mobileSidebar: false,
      loading: false,
      activeTab: 'general',
      tabs: [
        { id: 'general', name: 'General', icon: 'fas fa-cog' },
        { id: 'security', name: 'Security', icon: 'fas fa-shield-alt' },
        { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell' }
      ],
      settings: {
        general: {
          appName: 'Quiz Application',
          adminEmail: 'admin@quiz.com',
          timezone: 'UTC',
          maintenanceMode: false
        },
        security: {
          twoFactorAuth: true,
          passwordPolicy: true,
          sessionTimeout: 30,
          allowedIPs: ''
        },
        notifications: {
          emailEnabled: true,
          pushEnabled: false,
          smsEnabled: false,
          types: {
            newUser: true,
            quizAttempt: true,
            systemAlert: true,
            securityAlert: true
          }
        }
      }
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
      this.$inertia.post(route('admin.logout'))
    },
    async saveSettings() {
      this.loading = true
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1500))
        
        // Show success message (you can replace this with a proper notification system)
        this.showSuccess('Settings saved successfully!')
      } catch (error) {
        this.showError('Failed to save settings. Please try again.')
      } finally {
        this.loading = false
      }
    },
    showSuccess(message) {
      // You can integrate with a notification system here
      alert(message)
    },
    showError(message) {
      // You can integrate with a notification system here
      alert(message)
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
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
  border-color: #3b82f6;
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

input:checked + .toggle-slider {
  background-color: #10b981;
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

.notification-type-checkbox:checked + .notification-type-content .notification-type-icon {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
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
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
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
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
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