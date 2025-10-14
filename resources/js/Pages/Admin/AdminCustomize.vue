<template>
  <div class="min-h-screen" :class="isDark ? 'dark-theme' : 'light-theme'">
    <AdminNavbar 
      title="Customize Theme"
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
        current-page="/admin/customize"
        @close-mobile-sidebar="toggleMobileSidebar"
      />

      <!-- Main Content -->
      <div class="main-content">
        <main class="content">
          <!-- Header Section -->
          <div class="content-card mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div>
                <h1 class="card-title-large">Customize Theme</h1>
                <p class="card-subtitle">Change the appearance of your admin dashboard</p>
              </div>
              <div class="mt-4 md:mt-0">
                <button 
                  @click="saveCustomization" 
                  class="btn-primary"
                  :disabled="form.processing"
                >
                  <i class="fas fa-palette mr-2"></i>
                  {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Success Message -->
          <div v-if="$page.props.flash.message" class="content-card mb-6" :class="flashMessageClass">
            <div class="flex items-center p-4">
              <i class="fas fa-check-circle mr-3 text-xl" :class="flashIconClass"></i>
              <div>
                <h3 class="font-semibold" :class="flashTextClass">{{ $page.props.flash.message }}</h3>
              </div>
              <button @click="$page.props.flash.message = null" class="ml-auto" :class="flashIconClass">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Customization Options -->
            <div class="lg:col-span-2 space-y-8">
              <!-- Theme Settings -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-palette text-purple-500 mr-3"></i>
                  <h2 class="card-title">Theme Settings</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-8">
                    <!-- Color Scheme -->
                    <div class="customization-section">
                      <h3 class="section-title">
                        <i class="fas fa-fill-drip text-blue-500 mr-2"></i>
                        Color Scheme
                      </h3>
                      <p class="section-description">Choose the overall color scheme for your application</p>
                      <div class="color-schemes-grid">
                        <div
                          v-for="scheme in colorSchemes"
                          :key="scheme.id"
                          @click="form.colorScheme = scheme.id"
                          class="color-scheme-card group"
                          :class="{ 'color-scheme-card-active': form.colorScheme === scheme.id }"
                        >
                          <div class="color-preview" :style="scheme.previewStyle">
                            <div class="color-preview-content">
                              <i class="fas fa-check color-check-icon" v-if="form.colorScheme === scheme.id"></i>
                              <div class="color-preview-dots">
                                <div class="dot dot-1" :style="scheme.dot1Style"></div>
                                <div class="dot dot-2" :style="scheme.dot2Style"></div>
                                <div class="dot dot-3" :style="scheme.dot3Style"></div>
                              </div>
                            </div>
                          </div>
                          <div class="color-scheme-info">
                            <h4 class="color-scheme-name">{{ scheme.name }}</h4>
                            <p class="color-scheme-description">{{ scheme.description }}</p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Primary Color -->
                    <div class="customization-section">
                      <h3 class="section-title">
                        <i class="fas fa-tint text-red-500 mr-2"></i>
                        Primary Color
                      </h3>
                      <p class="section-description">Select the main accent color for buttons and interactive elements</p>
                      <div class="primary-colors-grid">
                        <div
                          v-for="color in primaryColors"
                          :key="color.id"
                          @click="form.primaryColor = color.id"
                          class="primary-color-card group"
                          :class="{ 'primary-color-card-active': form.primaryColor === color.id }"
                        >
                          <div class="color-circle" :style="{ backgroundColor: color.value }">
                            <i class="fas fa-check text-white" v-if="form.primaryColor === color.id"></i>
                          </div>
                          <span class="color-name">{{ color.name }}</span>
                          <div class="color-palette">
                            <div class="palette-shade" :style="{ backgroundColor: color.light }"></div>
                            <div class="palette-shade" :style="{ backgroundColor: color.value }"></div>
                            <div class="palette-shade" :style="{ backgroundColor: color.dark }"></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Layout Options -->
                    <div class="customization-section">
                      <h3 class="section-title">
                        <i class="fas fa-layer-group text-green-500 mr-2"></i>
                        Layout Options
                      </h3>
                      <p class="section-description">Choose how your application navigation is organized</p>
                      <div class="layout-options-grid">
                        <label class="layout-option-card" :class="{ 'layout-option-card-active': form.layout === 'sidebar' }">
                          <input
                            type="radio"
                            v-model="form.layout"
                            value="sidebar"
                            class="hidden"
                          />
                          <div class="layout-preview sidebar-layout">
                            <div class="layout-sidebar"></div>
                            <div class="layout-content">
                              <div class="layout-header"></div>
                              <div class="layout-main">
                                <div class="content-line"></div>
                                <div class="content-line short"></div>
                                <div class="content-line medium"></div>
                              </div>
                            </div>
                          </div>
                          <div class="layout-info">
                            <h4 class="layout-name">Sidebar Layout</h4>
                            <p class="layout-description">Traditional navigation with sidebar menu</p>
                          </div>
                        </label>

                        <label class="layout-option-card" :class="{ 'layout-option-card-active': form.layout === 'topbar' }">
                          <input
                            type="radio"
                            v-model="form.layout"
                            value="topbar"
                            class="hidden"
                          />
                          <div class="layout-preview topbar-layout">
                            <div class="layout-topbar"></div>
                            <div class="layout-content">
                              <div class="layout-main">
                                <div class="content-line"></div>
                                <div class="content-line short"></div>
                                <div class="content-line medium"></div>
                              </div>
                            </div>
                          </div>
                          <div class="layout-info">
                            <h4 class="layout-name">Topbar Layout</h4>
                            <p class="layout-description">Modern navigation with top bar</p>
                          </div>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column - Preview & Actions -->
            <div class="space-y-8">
              <!-- Live Preview -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-eye text-blue-500 mr-3"></i>
                  <h2 class="card-title">Live Preview</h2>
                </div>
                <div class="card-body">
                  <div class="preview-container">
                    <div class="preview-window">
                      <div class="preview-header">
                        <div class="preview-brand">
                          <div class="preview-logo" :style="{ backgroundColor: getPrimaryColorValue() }">
                            <span class="preview-logo-text">Q</span>
                          </div>
                          <span class="preview-app-name">Quiz Admin</span>
                        </div>
                        <div class="preview-actions">
                          <div class="preview-dot"></div>
                          <div class="preview-dot"></div>
                          <div class="preview-dot"></div>
                        </div>
                      </div>
                      <div class="preview-content">
                        <div class="preview-sidebar" v-if="form.layout === 'sidebar'">
                          <div class="sidebar-item active">
                            <i class="fas fa-home sidebar-icon"></i>
                            <span>Dashboard</span>
                          </div>
                          <div class="sidebar-item">
                            <i class="fas fa-chart-bar sidebar-icon"></i>
                            <span>Analytics</span>
                          </div>
                          <div class="sidebar-item">
                            <i class="fas fa-users sidebar-icon"></i>
                            <span>Users</span>
                          </div>
                        </div>
                        <div class="preview-main">
                          <div class="preview-welcome">
                            <h3 class="preview-welcome-title">Welcome!</h3>
                            <p class="preview-welcome-message">Preview of your selected theme</p>
                          </div>
                          <div class="preview-stats">
                            <div class="preview-stat">
                              <div class="stat-bar" style="width: 70%"></div>
                            </div>
                            <div class="preview-stat">
                              <div class="stat-bar" style="width: 50%"></div>
                            </div>
                            <div class="preview-stat">
                              <div class="stat-bar" style="width: 85%"></div>
                            </div>
                          </div>
                          <button class="preview-btn" :style="{ backgroundColor: getPrimaryColorValue() }">
                            Get Started
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <p class="preview-note">
                    <i class="fas fa-info-circle mr-2"></i>
                    Real-time preview of your theme customization
                  </p>
                </div>
              </div>

              <!-- Quick Actions -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-bolt text-yellow-500 mr-3"></i>
                  <h2 class="card-title">Quick Actions</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-4">
                    <button @click="saveCustomization" class="action-btn btn-primary" :disabled="form.processing">
                      <i class="fas fa-save mr-3"></i>
                      {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                    <button @click="resetToDefault" class="action-btn btn-outline">
                      <i class="fas fa-undo mr-3"></i>
                      Reset to Default
                    </button>
                  </div>
                </div>
              </div>

              <!-- Current Settings -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-cog text-gray-500 mr-3"></i>
                  <h2 class="card-title">Current Settings</h2>
                </div>
                <div class="card-body">
                  <div class="settings-summary">
                    <div class="setting-item">
                      <div class="setting-label">Color Scheme</div>
                      <div class="setting-value">
                        <div class="setting-color" :style="getColorSchemePreview(form.colorScheme)"></div>
                        {{ getColorSchemeName(form.colorScheme) }}
                      </div>
                    </div>
                    <div class="setting-item">
                      <div class="setting-label">Primary Color</div>
                      <div class="setting-value">
                        <div class="setting-color" :style="{ backgroundColor: getPrimaryColorValue() }"></div>
                        {{ getPrimaryColorName(form.primaryColor) }}
                      </div>
                    </div>
                    <div class="setting-item">
                      <div class="setting-label">Layout</div>
                      <div class="setting-value capitalize">{{ form.layout }}</div>
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
import AdminNavbar from './AdminNavbar.vue'
import AdminSidebar from './AdminSidebar.vue'
import { useForm } from '@inertiajs/vue3'

export default {
  components: {
    AdminNavbar,
    AdminSidebar
  },
  props: {
    customization: {
      type: Object,
      default: () => ({
        colorScheme: 'light',
        primaryColor: 'blue',
        layout: 'sidebar'
      })
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
    }
  },
  setup(props) {
    const form = useForm({
      colorScheme: props.customization.colorScheme,
      primaryColor: props.customization.primaryColor,
      layout: props.customization.layout
    })

    const saveCustomization = () => {
      // FIX: Use the correct URL - /admin/customize (without /update)
      form.put('/admin/customize', {
        preserveScroll: true,
        onSuccess: () => {
          console.log('Theme updated successfully')
        },
        onError: (errors) => {
          console.log('Theme update errors:', errors)
        }
      })
    }

    return { form, saveCustomization }
  },
  data() {
    return {
      isDark: false,
      mobileSidebar: false,
      colorSchemes: [
        {
          id: 'light',
          name: 'Light',
          description: 'Clean and bright interface',
          previewStyle: 'background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);',
          dot1Style: 'background-color: #3b82f6;',
          dot2Style: 'background-color: #10b981;',
          dot3Style: 'background-color: #f59e0b;'
        },
        {
          id: 'dark',
          name: 'Dark',
          description: 'Easy on the eyes in low light',
          previewStyle: 'background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);',
          dot1Style: 'background-color: #60a5fa;',
          dot2Style: 'background-color: #34d399;',
          dot3Style: 'background-color: #fbbf24;'
        },
        {
          id: 'auto',
          name: 'Auto',
          description: 'Adapts to system preference',
          previewStyle: 'background: linear-gradient(135deg, #f8fafc 50%, #1e293b 50%);',
          dot1Style: 'background: linear-gradient(45deg, #3b82f6 50%, #60a5fa 50%);',
          dot2Style: 'background: linear-gradient(45deg, #10b981 50%, #34d399 50%);',
          dot3Style: 'background: linear-gradient(45deg, #f59e0b 50%, #fbbf24 50%);'
        }
      ],
      primaryColors: [
        { id: 'blue', value: '#3b82f6', light: '#dbeafe', dark: '#1e40af', name: 'Blue' },
        { id: 'green', value: '#10b981', light: '#dcfce7', dark: '#047857', name: 'Green' },
        { id: 'purple', value: '#8b5cf6', light: '#f3e8ff', dark: '#6d28d9', name: 'Purple' },
        { id: 'red', value: '#ef4444', light: '#fee2e2', dark: '#b91c1c', name: 'Red' },
        { id: 'orange', value: '#f59e0b', light: '#fef3c7', dark: '#d97706', name: 'Orange' }
      ]
    }
  },
  computed: {
    flashMessageClass() {
      const type = this.$page.props.flash.type || 'success'
      return type === 'success' 
        ? 'bg-green-50 border border-green-200' 
        : 'bg-red-50 border border-red-200'
    },
    flashIconClass() {
      const type = this.$page.props.flash.type || 'success'
      return type === 'success' ? 'text-green-500' : 'text-red-500'
    },
    flashTextClass() {
      const type = this.$page.props.flash.type || 'success'
      return type === 'success' ? 'text-green-800' : 'text-red-800'
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
      this.$inertia.post('/admin/logout')
    },
    resetToDefault() {
      if (confirm('Are you sure you want to reset all customization to default?')) {
        // FIX: Use the correct URL - /admin/customize/reset
        this.$inertia.post('/admin/customize/reset', {
          preserveScroll: true,
          onSuccess: () => {
            this.form.colorScheme = 'light'
            this.form.primaryColor = 'blue'
            this.form.layout = 'sidebar'
          },
          onError: (errors) => {
            console.log('Reset errors:', errors)
          }
        })
      }
    },
    getColorSchemeName(schemeId) {
      const scheme = this.colorSchemes.find(s => s.id === schemeId)
      return scheme ? scheme.name : 'Unknown'
    },
    getPrimaryColorName(colorId) {
      const color = this.primaryColors.find(c => c.id === colorId)
      return color ? color.name : 'Unknown'
    },
    getPrimaryColorValue() {
      const color = this.primaryColors.find(c => c.id === this.form.primaryColor)
      return color ? color.value : '#3b82f6'
    },
    getColorSchemePreview(schemeId) {
      const scheme = this.colorSchemes.find(s => s.id === schemeId)
      return scheme ? scheme.previewStyle : ''
    }
  }
}
</script>


<style>
/* Import Font Awesome */
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

/* Customization Sections */
.customization-section {
  padding-bottom: 2rem;
  border-bottom: 1px solid var(--border-color);
}

.customization-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.section-description {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
}

/* Color Schemes */
.color-schemes-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

@media (min-width: 768px) {
  .color-schemes-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.color-scheme-card {
  border: 2px solid var(--border-color);
  border-radius: 0.75rem;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  background-color: var(--bg-primary);
}

.color-scheme-card:hover {
  border-color: var(--text-muted);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.color-scheme-card-active {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.color-preview {
  height: 80px;
  position: relative;
  overflow: hidden;
}

.color-preview-content {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.color-check-icon {
  font-size: 1.25rem;
  color: white;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.color-preview-dots {
  display: flex;
  gap: 0.5rem;
  position: absolute;
  bottom: 0.75rem;
  left: 0.75rem;
}

.dot {
  width: 0.75rem;
  height: 0.75rem;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.color-scheme-info {
  padding: 1rem;
}

.color-scheme-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.color-scheme-description {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Primary Colors */
.primary-colors-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 1rem;
}

.primary-color-card {
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.primary-color-card:hover {
  transform: translateY(-2px);
}

.primary-color-card-active {
  transform: scale(1.05);
}

.primary-color-card-active .color-circle {
  box-shadow: 0 0 0 3px white, 0 0 0 6px #3b82f6;
}

.color-circle {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  margin: 0 auto 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow);
  transition: all 0.3s ease;
}

.color-name {
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--text-primary);
  display: block;
  margin-bottom: 0.5rem;
}

.color-palette {
  display: flex;
  gap: 0.25rem;
  justify-content: center;
}

.palette-shade {
  width: 0.75rem;
  height: 0.75rem;
  border-radius: 0.125rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Layout Options */
.layout-options-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 768px) {
  .layout-options-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.layout-option-card {
  border: 2px solid var(--border-color);
  border-radius: 0.75rem;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  background-color: var(--bg-primary);
}

.layout-option-card:hover {
  border-color: var(--text-muted);
  transform: translateY(-2px);
}

.layout-option-card-active {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.layout-preview {
  height: 120px;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  margin-bottom: 1rem;
  overflow: hidden;
  background-color: var(--bg-secondary);
}

.sidebar-layout {
  display: flex;
}

.sidebar-layout .layout-sidebar {
  width: 30%;
  background-color: var(--bg-sidebar);
  border-right: 1px solid var(--border-color);
}

.sidebar-layout .layout-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.sidebar-layout .layout-header {
  height: 20%;
  background-color: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
}

.sidebar-layout .layout-main {
  flex: 1;
  padding: 0.5rem;
}

.topbar-layout {
  display: flex;
  flex-direction: column;
}

.topbar-layout .layout-topbar {
  height: 20%;
  background-color: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
}

.topbar-layout .layout-content {
  flex: 1;
}

.topbar-layout .layout-main {
  height: 100%;
  padding: 0.5rem;
}

.content-line {
  height: 0.5rem;
  background-color: var(--border-color);
  border-radius: 0.25rem;
  margin-bottom: 0.5rem;
}

.content-line.short {
  width: 60%;
}

.content-line.medium {
  width: 80%;
}

.layout-info {
  text-align: center;
}

.layout-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.layout-description {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Code Editor */
.code-editor-container {
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  overflow: hidden;
}

.code-editor {
  width: 100%;
  padding: 1rem;
  border: none;
  background-color: #1a1b26;
  color: #c0caf5;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 0.875rem;
  line-height: 1.5;
  resize: vertical;
  outline: none;
}

.code-editor-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  background-color: var(--bg-primary);
  border-top: 1px solid var(--border-color);
  font-size: 0.75rem;
  color: var(--text-muted);
}

.code-info {
  font-style: italic;
}

/* Brand Assets */
.brand-assets-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 768px) {
  .brand-assets-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.brand-asset-card {
  border: 1px solid var(--border-color);
  border-radius: 0.75rem;
  padding: 1.25rem;
  background-color: var(--bg-primary);
}

.brand-asset-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.brand-asset-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  display: flex;
  align-items: center;
}

.brand-asset-preview {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo-preview,
.favicon-preview {
  width: 4rem;
  height: 4rem;
  border: 2px dashed var(--border-color);
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--bg-secondary);
  transition: all 0.3s ease;
}

.logo-preview.has-logo,
.favicon-preview.has-favicon {
  border-style: solid;
  border-color: #10b981;
}

.logo-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.favicon-image {
  width: 2rem;
  height: 2rem;
  object-fit: contain;
}

.logo-placeholder,
.favicon-placeholder {
  font-size: 1.5rem;
  color: var(--text-muted);
}

.brand-asset-info {
  flex: 1;
}

.brand-asset-description {
  font-size: 0.75rem;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.brand-asset-size {
  font-size: 0.7rem;
  color: var(--text-muted);
}

/* Preview */
.preview-container {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1.5rem;
  border-radius: 1rem;
}

.preview-window {
  background-color: white;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
  transform: scale(0.9);
  transform-origin: top center;
}

.preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  background-color: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.preview-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.preview-logo {
  width: 2rem;
  height: 2rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 0.875rem;
}

.preview-app-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
}

.preview-actions {
  display: flex;
  gap: 0.5rem;
}

.preview-dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  background-color: #d1d5db;
}

.preview-content {
  display: flex;
  height: 200px;
}

.preview-sidebar {
  width: 30%;
  background-color: #f8fafc;
  border-right: 1px solid #e5e7eb;
  padding: 0.5rem;
}

.sidebar-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
  transition: all 0.2s ease;
}

.sidebar-item.active {
  background-color: #3b82f6;
  color: white;
}

.sidebar-item:hover:not(.active) {
  background-color: #f3f4f6;
}

.sidebar-icon {
  width: 1rem;
  text-align: center;
}

.preview-main {
  flex: 1;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.preview-welcome-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.preview-welcome-message {
  font-size: 0.75rem;
  color: #6b7280;
  line-height: 1.4;
}

.preview-stats {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin: 1rem 0;
}

.preview-stat {
  height: 0.5rem;
  background-color: #f3f4f6;
  border-radius: 0.25rem;
  overflow: hidden;
}

.stat-bar {
  height: 100%;
  background-color: #3b82f6;
  border-radius: 0.25rem;
}

.preview-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  color: white;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.preview-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.preview-note {
  font-size: 0.75rem;
  color: var(--text-muted);
  text-align: center;
  margin-top: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Settings Summary */
.settings-summary {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.setting-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--border-color);
}

.setting-item:last-child {
  border-bottom: none;
}

.setting-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.setting-value {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.setting-color {
  width: 1rem;
  height: 1rem;
  border-radius: 0.25rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

.btn-secondary {
  background-color: var(--bg-primary);
  color: var(--text-primary);
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  border: 1px solid var(--border-color);
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
}

.btn-secondary:hover {
  background-color: var(--hover-bg);
  border-color: var(--text-muted);
}

.btn-small {
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
}

.btn-outline {
  background-color: transparent;
  color: var(--text-primary);
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: 1px solid var(--border-color);
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.btn-outline:hover {
  background-color: var(--hover-bg);
  border-color: var(--text-muted);
  transform: translateY(-1px);
}

.action-btn {
  width: 100%;
  justify-content: flex-start;
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

.mr-2 {
  margin-right: 0.5rem;
}

.mr-3 {
  margin-right: 0.75rem;
}

/* Text Colors */
.text-blue-500 {
  color: #3b82f6;
}

.text-green-500 {
  color: #10b981;
}

.text-purple-500 {
  color: #8b5cf6;
}

.text-red-500 {
  color: #ef4444;
}

.text-orange-500 {
  color: #f59e0b;
}

.text-yellow-500 {
  color: #eab308;
}

.text-gray-500 {
  color: #6b7280;
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

.bg-yellow-100 {
  background-color: #fef3c7;
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
  .lg\:grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  
  .lg\:col-span-2 {
    grid-column: span 2 / span 2;
  }
}

/* Hidden */
.hidden {
  display: none;
}

/* Capitalize */
.capitalize {
  text-transform: capitalize;
}

/* Group Hover */
.group:hover .group-hover\:bg-blue-100 {
  background-color: #dbeafe;
}

.group {
  position: relative;
}

/* Transitions */
.transition-all {
  transition: all 0.3s ease;
}
</style>