<template>
  <div class="min-h-screen" :class="isDark ? 'dark-theme' : 'light-theme'">
    <AdminNavbar 
      title="Profile Settings"
      :is-dark="isDark"
      @toggle-theme="toggleTheme"
      @toggle-mobile-sidebar="toggleMobileSidebar"
      @logout="handleLogout"
    />

    <div class="flex">
      <AdminSidebar 
        :mobile-sidebar="mobileSidebar"
        current-page="/admin/profile"
        @close-mobile-sidebar="toggleMobileSidebar"
      />

      <!-- Main Content -->
      <div class="main-content">
        <main class="content">
          <!-- Header Section -->
          <div class="content-card mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div>
                <h1 class="card-title-large">Profile Settings</h1>
                <p class="card-subtitle">Manage your account information and preferences</p>
              </div>
              <div class="mt-4 md:mt-0">
                <button 
                  @click="saveProfile" 
                  class="btn-primary"
                  :disabled="form.processing"
                >
                  <i class="fas fa-save mr-2"></i>
                  {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Profile Information -->
            <div class="lg:col-span-2 space-y-8">
              <!-- Personal Information Card -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-user-circle text-blue-500 mr-3"></i>
                  <h2 class="card-title">Personal Information</h2>
                </div>
                <div class="card-body">
                  <form @submit.prevent="saveProfile" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div class="form-group">
                        <label class="form-label">
                          <i class="fas fa-user text-gray-400 mr-2"></i>
                          First Name
                        </label>
                        <input
                          type="text"
                          v-model="form.firstName"
                          class="form-input"
                          placeholder="Enter your first name"
                          :class="{ 'form-input-error': form.errors.firstName }"
                        />
                        <div v-if="form.errors.firstName" class="form-error">
                          {{ form.errors.firstName }}
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="form-label">
                          <i class="fas fa-user text-gray-400 mr-2"></i>
                          Last Name
                        </label>
                        <input
                          type="text"
                          v-model="form.lastName"
                          class="form-input"
                          placeholder="Enter your last name"
                          :class="{ 'form-input-error': form.errors.lastName }"
                        />
                        <div v-if="form.errors.lastName" class="form-error">
                          {{ form.errors.lastName }}
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label">
                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                        Email Address
                      </label>
                      <input
                        type="email"
                        v-model="form.email"
                        class="form-input"
                        placeholder="Enter your email address"
                        :class="{ 'form-input-error': form.errors.email }"
                      />
                      <div v-if="form.errors.email" class="form-error">
                        {{ form.errors.email }}
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label">
                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                        Phone Number
                      </label>
                      <input
                        type="tel"
                        v-model="form.phone"
                        class="form-input"
                        placeholder="Enter your phone number"
                        :class="{ 'form-input-error': form.errors.phone }"
                      />
                      <div v-if="form.errors.phone" class="form-error">
                        {{ form.errors.phone }}
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label">
                        <i class="fas fa-edit text-gray-400 mr-2"></i>
                        Bio
                      </label>
                      <textarea
                        v-model="form.bio"
                        rows="4"
                        class="form-input"
                        placeholder="Tell us about yourself, your role, and your interests..."
                        :class="{ 'form-input-error': form.errors.bio }"
                      ></textarea>
                      <div class="flex justify-between items-center mt-1">
                        <div v-if="form.errors.bio" class="form-error">
                          {{ form.errors.bio }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ form.bio ? form.bio.length : 0 }}/500
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Security Settings Card -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-shield-alt text-green-500 mr-3"></i>
                  <h2 class="card-title">Security Settings</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-6">
                    <div class="security-item">
                      <div class="flex items-center justify-between">
                        <div>
                          <h3 class="security-title">Two-Factor Authentication</h3>
                          <p class="security-description">Add an extra layer of security to your account</p>
                        </div>
                        <label class="toggle-switch">
                          <input type="checkbox" v-model="security.twoFactor" />
                          <span class="toggle-slider"></span>
                        </label>
                      </div>
                    </div>

                    <div class="security-item">
                      <div class="flex items-center justify-between">
                        <div>
                          <h3 class="security-title">Password</h3>
                          <p class="security-description">Last changed 3 months ago</p>
                        </div>
                        <button class="btn-secondary">
                          <i class="fas fa-key mr-2"></i>
                          Change
                        </button>
                      </div>
                    </div>

                    <div class="security-item">
                      <div class="flex items-center justify-between">
                        <div>
                          <h3 class="security-title">Session Timeout</h3>
                          <p class="security-description">Automatically log out after 30 minutes of inactivity</p>
                        </div>
                        <select class="form-input w-32">
                          <option>30 min</option>
                          <option>1 hour</option>
                          <option>2 hours</option>
                          <option>4 hours</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-8">
              <!-- Profile Picture Card -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-camera text-purple-500 mr-3"></i>
                  <h2 class="card-title">Profile Picture</h2>
                </div>
                <div class="card-body">
                  <div class="flex flex-col items-center space-y-4">
                    <div class="relative">
                      <div class="profile-avatar-large">
                        <i class="fas fa-user text-3xl"></i>
                      </div>
                      <button
                        @click="triggerFileInput"
                        class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors"
                      >
                        <i class="fas fa-camera text-sm"></i>
                      </button>
                    </div>
                    <input
                      type="file"
                      ref="fileInput"
                      @change="handleImageUpload"
                      accept="image/*"
                      class="hidden"
                    />
                    <p class="text-sm text-center text-gray-500">
                      JPG, PNG or GIF. Max size 2MB
                    </p>
                    <button class="btn-outline w-full">
                      <i class="fas fa-sync-alt mr-2"></i>
                      Remove Photo
                    </button>
                  </div>
                </div>
              </div>

              <!-- Account Status Card -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-chart-bar text-orange-500 mr-3"></i>
                  <h2 class="card-title">Account Status</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-4">
                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Email Verified</span>
                        <span class="badge-success">
                          <i class="fas fa-check-circle mr-1"></i>
                          Verified
                        </span>
                      </div>
                    </div>

                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Account Created</span>
                        <span class="status-value">{{ profile.createdAt }}</span>
                      </div>
                    </div>

                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Last Login</span>
                        <span class="status-value">{{ profile.lastLogin }}</span>
                      </div>
                    </div>

                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Role</span>
                        <span class="badge-primary">Administrator</span>
                      </div>
                    </div>

                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Status</span>
                        <span class="badge-success">Active</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quick Actions Card -->
              <div class="content-card">
                <div class="card-header">
                  <i class="fas fa-bolt text-yellow-500 mr-3"></i>
                  <h2 class="card-title">Quick Actions</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-3">
                    <button class="action-btn">
                      <i class="fas fa-download mr-3"></i>
                      Export Profile Data
                    </button>
                    <button class="action-btn">
                      <i class="fas fa-print mr-3"></i>
                      Print Profile
                    </button>
                    <button class="action-btn">
                      <i class="fas fa-share-alt mr-3"></i>
                      Share Profile
                    </button>
                    <button class="action-btn text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                      <i class="fas fa-trash-alt mr-3"></i>
                      Delete Account
                    </button>
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
    profile: Object
  },
  setup(props) {
    const form = useForm({
      firstName: props.profile.firstName,
      lastName: props.profile.lastName,
      email: props.profile.email,
      phone: props.profile.phone,
      bio: props.profile.bio
    })

    const saveProfile = () => {
      form.put(route('admin.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
          // Show success message
          console.log('Profile updated successfully')
        },
      })
    }

    return { form, saveProfile }
  },
  data() {
    return {
      isDark: false,
      mobileSidebar: false,
      security: {
        twoFactor: true,
        sessionTimeout: '30 min'
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
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    handleImageUpload(event) {
      const file = event.target.files[0]
      if (file) {
        const formData = new FormData()
        formData.append('avatar', file)
        
        this.$inertia.post(route('admin.profile.avatar'), formData, {
          preserveScroll: true,
          onSuccess: () => {
            console.log('Avatar updated successfully')
          }
        })
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

.form-input-error {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-error {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

/* Buttons */
.btn-primary {
  background-color: #3b82f6;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary:hover:not(:disabled) {
  background-color: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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

.btn-outline {
  background-color: transparent;
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
  width: 100%;
}

.btn-outline:hover {
  background-color: var(--hover-bg);
  border-color: var(--text-muted);
}

/* Profile Avatar */
.profile-avatar-large {
  width: 8rem;
  height: 8rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2rem;
  box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
  position: relative;
}

/* Badges */
.badge-success {
  background-color: #dcfce7;
  color: #16a34a;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
}

.dark-theme .badge-success {
  background-color: #14532d;
  color: #4ade80;
}

.badge-primary {
  background-color: #dbeafe;
  color: #2563eb;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
}

.dark-theme .badge-primary {
  background-color: #1e3a8a;
  color: #60a5fa;
}

/* Status Items */
.status-item {
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--border-color);
}

.status-item:last-child {
  border-bottom: none;
}

.status-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.status-value {
  font-size: 0.875rem;
  color: var(--text-primary);
  font-weight: 500;
}

/* Security Items */
.security-item {
  padding: 1rem;
  border-radius: 0.5rem;
  border: 1px solid var(--border-color);
  transition: all 0.2s ease;
}

.security-item:hover {
  background-color: var(--hover-bg);
  border-color: var(--text-muted);
}

.security-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.security-description {
  font-size: 0.75rem;
  color: var(--text-muted);
}

/* Toggle Switch */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 3.5rem;
  height: 2rem;
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
}

input:checked + .toggle-slider {
  background-color: #10b981;
}

input:checked + .toggle-slider:before {
  transform: translateX(1.5rem);
}

/* Action Buttons */
.action-btn {
  width: 100%;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  border: 1px solid var(--border-color);
  background-color: transparent;
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
  display: flex;
  align-items: center;
  font-size: 0.875rem;
}

.action-btn:hover {
  background-color: var(--hover-bg);
  border-color: var(--text-muted);
  transform: translateX(4px);
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

.space-y-8 > * + * {
  margin-top: 2rem;
}

.space-y-6 > * + * {
  margin-top: 1.5rem;
}

.space-y-4 > * + * {
  margin-top: 1rem;
}

.space-y-3 > * + * {
  margin-top: 0.75rem;
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

.justify-center {
  justify-content: center;
}

/* Spacing */
.mb-8 {
  margin-bottom: 2rem;
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

.mt-1 {
  margin-top: 0.25rem;
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

.text-orange-500 {
  color: #f59e0b;
}

.text-yellow-500 {
  color: #eab308;
}

.text-gray-400 {
  color: #9ca3af;
}

.text-red-500 {
  color: #ef4444;
}

/* Background Colors */
.bg-blue-600 {
  background-color: #2563eb;
}

.hover\:bg-blue-700:hover {
  background-color: #1d4ed8;
}

.hover\:bg-red-50:hover {
  background-color: #fef2f2;
}

.dark .hover\:bg-red-900\/20:hover {
  background-color: rgba(127, 29, 29, 0.2);
}

/* Transitions */
.transition-colors {
  transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.transition-all {
  transition: all 0.2s ease;
}

/* Hidden */
.hidden {
  display: none;
}

/* Text Alignment */
.text-center {
  text-align: center;
}

.text-sm {
  font-size: 0.875rem;
}

.text-3xl {
  font-size: 1.875rem;
}

/* Positioning */
.relative {
  position: relative;
}

.absolute {
  position: absolute;
}

.-bottom-2 {
  bottom: -0.5rem;
}

.-right-2 {
  right: -0.5rem;
}

/* Width */
.w-full {
  width: 100%;
}

.w-32 {
  width: 8rem;
}
</style>