<template>
  <div 
    class="min-h-screen" 
    :class="themeClass"
    :style="themeStyles"
  >
    <AdminNavbar 
      title="Profile Settings"
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
              <div class="flex items-center gap-4 mt-4 md:mt-0">
                <!-- Theme Debug Button (remove in production) -->
                <button v-if="showDebug" @click="debugTheme" class="debug-btn">
                  <span class="mr-2">🐛</span>
                  Debug Theme
                </button>
                <button 
                  @click="saveProfile" 
                  class="btn-primary"
                  :style="{ 
                    background: `linear-gradient(135deg, ${primaryColorValue} 0%, ${getPrimaryColorDark()} 100%)` 
                  }"
                  :disabled="form.processing"
                >
                  <span class="mr-2">💾</span>
                  {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Success Message -->
          <div v-if="$page.props.flash.message" class="content-card mb-6" :class="flashMessageClass">
            <div class="flex items-center p-4">
              <span class="mr-3 text-xl" :class="flashIconClass">✅</span>
              <div>
                <h3 class="font-semibold" :class="flashTextClass">{{ $page.props.flash.message }}</h3>
              </div>
              <button @click="$page.props.flash.message = null" class="ml-auto" :class="flashIconClass">
                <span>✕</span>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Profile Information -->
            <div class="lg:col-span-2 space-y-8">
              <!-- Personal Information Card -->
              <div class="content-card">
                <div class="card-header">
                  <span class="mr-3" :style="{ color: primaryColorValue }">👤</span>
                  <h2 class="card-title">Personal Information</h2>
                </div>
                <div class="card-body">
                  <form @submit.prevent="saveProfile" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div class="form-group">
                        <label class="form-label">
                          <span class="mr-2">👤</span>
                          First Name
                        </label>
                        <input
                          type="text"
                          v-model="form.first_name"
                          class="form-input"
                          placeholder="Enter your first name"
                          :class="{ 'form-input-error': form.errors.first_name }"
                        />
                        <div v-if="form.errors.first_name" class="form-error">
                          {{ form.errors.first_name }}
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="form-label">
                          <span class="mr-2">👤</span>
                          Last Name
                        </label>
                        <input
                          type="text"
                          v-model="form.last_name"
                          class="form-input"
                          placeholder="Enter your last name"
                          :class="{ 'form-input-error': form.errors.last_name }"
                        />
                        <div v-if="form.errors.last_name" class="form-error">
                          {{ form.errors.last_name }}
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label">
                        <span class="mr-2">🏷️</span>
                        Username
                      </label>
                      <input
                        type="text"
                        v-model="form.username"
                        class="form-input"
                        placeholder="Enter your username"
                        :class="{ 'form-input-error': form.errors.username }"
                      />
                      <div v-if="form.errors.username" class="form-error">
                        {{ form.errors.username }}
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label">
                        <span class="mr-2">📧</span>
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
                        <span class="mr-2">📞</span>
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
                        <span class="mr-2">📝</span>
                        Bio
                      </label>
                      <textarea
                        v-model="form.bio"
                        rows="4"
                        class="form-input"
                        placeholder="Tell us about yourself, your role, and your interests..."
                        :class="{ 'form-input-error': form.errors.bio }"
                        maxlength="500"
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

              <!-- Password Update Card -->
              <div class="content-card">
                <div class="card-header">
                  <span class="mr-3" :style="{ color: primaryColorValue }">🔑</span>
                  <h2 class="card-title">Update Password</h2>
                </div>
                <div class="card-body">
                  <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="form-group">
                      <label class="form-label">
                        <span class="mr-2">🔒</span>
                        Current Password
                      </label>
                      <input
                        type="password"
                        v-model="passwordForm.current_password"
                        class="form-input"
                        placeholder="Enter your current password"
                        :class="{ 'form-input-error': passwordForm.errors.current_password }"
                      />
                      <div v-if="passwordForm.errors.current_password" class="form-error">
                        {{ passwordForm.errors.current_password }}
                      </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div class="form-group">
                        <label class="form-label">
                          <span class="mr-2">🔒</span>
                          New Password
                        </label>
                        <input
                          type="password"
                          v-model="passwordForm.password"
                          class="form-input"
                          placeholder="Enter new password"
                          :class="{ 'form-input-error': passwordForm.errors.password }"
                        />
                        <div v-if="passwordForm.errors.password" class="form-error">
                          {{ passwordForm.errors.password }}
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="form-label">
                          <span class="mr-2">🔒</span>
                          Confirm Password
                        </label>
                        <input
                          type="password"
                          v-model="passwordForm.password_confirmation"
                          class="form-input"
                          placeholder="Confirm new password"
                          :class="{ 'form-input-error': passwordForm.errors.password_confirmation }"
                        />
                        <div v-if="passwordForm.errors.password_confirmation" class="form-error">
                          {{ passwordForm.errors.password_confirmation }}
                        </div>
                      </div>
                    </div>

                    <div class="flex justify-end">
                      <button 
                        type="submit" 
                        class="btn-primary"
                        :style="{ 
                          background: `linear-gradient(135deg, ${primaryColorValue} 0%, ${getPrimaryColorDark()} 100%)` 
                        }"
                        :disabled="passwordForm.processing"
                      >
                        <span class="mr-2">🔑</span>
                        {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-8">
              <!-- Profile Picture Card -->
              <div class="content-card">
                <div class="card-header">
                  <span class="mr-3" :style="{ color: primaryColorValue }">📷</span>
                  <h2 class="card-title">Profile Picture</h2>
                </div>
                <div class="card-body">
                  <div class="flex flex-col items-center space-y-4">
                    <div class="relative">
                      <div class="profile-avatar-large">
                        <template v-if="profile.avatar">
                          <img :src="profile.avatar" alt="Profile" class="w-full h-full rounded-full object-cover">
                        </template>
                        <template v-else>
                          <span class="text-3xl" :style="{ color: primaryColorValue }">👤</span>
                        </template>
                      </div>
                      <button
                        @click="triggerFileInput"
                        class="absolute -bottom-2 -right-2 text-white p-3 rounded-full shadow-lg transition-colors"
                        :style="{ backgroundColor: primaryColorValue }"
                      >
                        <span class="text-sm">📷</span>
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
                    <button 
                      @click="removeAvatar" 
                      class="btn-outline w-full"
                      :disabled="!profile.avatar"
                    >
                      <span class="mr-2">🔄</span>
                      Remove Photo
                    </button>
                  </div>
                </div>
              </div>

              <!-- Account Status Card -->
              <div class="content-card">
                <div class="card-header">
                  <span class="mr-3" :style="{ color: primaryColorValue }">📊</span>
                  <h2 class="card-title">Account Status</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-4">
                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Email Verified</span>
                        <span 
                          class="badge-success"
                          :style="{ 
                            backgroundColor: getPrimaryColorLight(),
                            color: primaryColorValue 
                          }"
                        >
                          <span class="mr-1">✅</span>
                          Verified
                        </span>
                      </div>
                    </div>

                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Account Created</span>
                        <span class="status-value">{{ formatDate(profile.created_at) }}</span>
                      </div>
                    </div>

                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Last Login</span>
                        <span class="status-value">{{ formatDate(profile.last_login_at) }}</span>
                      </div>
                    </div>

                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Role</span>
                        <span 
                          class="badge-primary"
                          :style="{ 
                            backgroundColor: getPrimaryColorLight(),
                            color: primaryColorValue 
                          }"
                        >
                          {{ profile.role }}
                        </span>
                      </div>
                    </div>

                    <div class="status-item">
                      <div class="flex justify-between items-center">
                        <span class="status-label">Status</span>
                        <span 
                          class="badge-success"
                          :style="{ 
                            backgroundColor: getPrimaryColorLight(),
                            color: primaryColorValue 
                          }"
                        >
                          Active
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quick Actions Card -->
              <div class="content-card">
                <div class="card-header">
                  <span class="mr-3" :style="{ color: primaryColorValue }">⚡</span>
                  <h2 class="card-title">Quick Actions</h2>
                </div>
                <div class="card-body">
                  <div class="space-y-3">
                    <button 
                      class="action-btn text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" 
                      @click="showDeleteConfirmation = true"
                    >
                      <span class="mr-3">🗑️</span>
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

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirmation" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <div class="flex items-center mb-4">
          <span class="text-red-500 text-xl mr-3">⚠️</span>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Account</h3>
        </div>
        <p class="text-gray-600 dark:text-gray-300 mb-6">
          Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently lost.
        </p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteConfirmation = false" class="btn-secondary">
            Cancel
          </button>
          <button @click="deleteAccount" class="btn-danger">
            <span class="mr-2">🗑️</span>
            Delete Account
          </button>
        </div>
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
        role: 'admin',
        created_at: new Date().toISOString(),
        last_login_at: new Date().toISOString()
      })
    },
    theme: {
      type: Object,
      default: () => ({
        colorScheme: 'dark',
        primaryColor: 'red',
        layout: 'sidebar'
      })
    }
  },
  setup(props) {
    console.log('Profile props received:', props.profile);
    
    // Main profile form
    const form = useForm({
      first_name: props.profile.firstName || '',
      last_name: props.profile.lastName || '',
      username: props.profile.username || '',
      email: props.profile.email || '',
      phone: props.profile.phone || '',
      bio: props.profile.bio || '',
    })

    // Password update form
    const passwordForm = useForm({
      current_password: '',
      password: '',
      password_confirmation: ''
    })

    const saveProfile = () => {
      console.log('Saving profile with data:', form.data());
      console.log('Making PUT request to: /admin/profile');
      
      form.put('/admin/profile', {
        preserveScroll: true,
        onSuccess: () => {
          console.log('✅ Profile update successful');
        },
        onError: (errors) => {
          console.log('❌ Profile update errors:', errors);
        },
      })
    }

    const updatePassword = () => {
      console.log('Updating password with data:', passwordForm.data());
      console.log('Making PUT request to: /admin/profile/password');
      
      passwordForm.put('/admin/profile/password', {
        preserveScroll: true,
        onSuccess: () => {
          console.log('✅ Password update successful');
          passwordForm.reset()
        },
        onError: (errors) => {
          console.log('❌ Password update errors:', errors);
        },
      })
    }

    return { 
      form, 
      passwordForm, 
      saveProfile, 
      updatePassword 
    }
  },
  data() {
    return {
      isDark: this.theme?.colorScheme === 'dark',
      mobileSidebar: false,
      showDeleteConfirmation: false,
      showDebug: process.env.NODE_ENV === 'development',
      isInitialized: false
    }
  },
  computed: {
    themeClass() {
      const colorScheme = this.theme?.colorScheme || 'dark';
      const primaryColor = this.theme?.primaryColor || 'red';
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
      return colorMap[this.theme?.primaryColor] || colorMap.red;
    },
    
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
    },
    fullName() {
      return `${this.profile.firstName} ${this.profile.lastName}`.trim()
    }
  },
  watch: {
    theme: {
      handler(newTheme, oldTheme) {
        if (!this.isInitialized) return;
        
        console.log('Theme prop changed:', newTheme);
        
        // Update isDark based on theme prop
        this.isDark = newTheme?.colorScheme === 'dark';
        
        if (JSON.stringify(newTheme) !== JSON.stringify(oldTheme)) {
          console.log('Theme changed, re-rendering components...');
          this.handleThemeChange();
        }
      },
      deep: true,
      immediate: true
    }
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
      this.$nextTick(() => {
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
      console.log('Local isDark:', this.isDark);
      console.log('========================');
      
      alert(`Current Theme:
Color Scheme: ${this.theme?.colorScheme}
Primary Color: ${this.theme?.primaryColor}
Layout: ${this.theme?.layout}
Primary Color Value: ${this.primaryColorValue}
Local isDark: ${this.isDark}`);
    },
    getPrimaryColorLight() {
      const colorMap = {
        blue: '#dbeafe',
        green: '#dcfce7',
        purple: '#f3e8ff',
        red: '#fee2e2',
        orange: '#fef3c7'
      };
      return colorMap[this.theme?.primaryColor] || colorMap.red;
    },
    getPrimaryColorDark() {
      const colorMap = {
        blue: '#1e40af',
        green: '#047857',
        purple: '#6d28d9',
        red: '#b91c1c',
        orange: '#d97706'
      };
      return colorMap[this.theme?.primaryColor] || colorMap.red;
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      } catch (error) {
        return 'Invalid date';
      }
    },
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      console.log('File selected:', file);
      console.log('Making POST request to: /admin/profile/avatar');
      
      if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
          alert('File size must be less than 2MB');
          return;
        }

        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!validTypes.includes(file.type)) {
          alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
          return;
        }

        console.log('File validation passed, preparing to upload...');

        const formData = new FormData();
        formData.append('avatar', file);
        
        this.$inertia.post('/admin/profile/avatar', formData, {
          preserveScroll: true,
          onSuccess: () => {
            console.log('✅ Avatar upload successful');
            // Clear the file input
            event.target.value = '';
          },
          onError: (errors) => {
            console.log('❌ Avatar upload errors:', errors);
            alert('Failed to upload avatar. Please try again.');
          },
        });
      } else {
        console.log('No file selected');
      }
    },
    removeAvatar() {
      if (confirm('Are you sure you want to remove your profile picture?')) {
        console.log('Making DELETE request to: /admin/profile/avatar');
        
        this.$inertia.delete('/admin/profile/avatar', {
          preserveScroll: true,
          onSuccess: () => {
            console.log('✅ Avatar removed successfully');
          },
          onError: (errors) => {
            console.log('❌ Avatar removal errors:', errors);
            alert('Failed to remove avatar. Please try again.');
          },
        });
      }
    },
    deleteAccount() {
      console.log('Making DELETE request to: /admin/profile');
      
      this.$inertia.delete('/admin/profile', {
        preserveScroll: false,
      });
    }
  },
  mounted() {
    this.isInitialized = true;
    
    console.log('🎯 Profile component mounted successfully!');
    console.log('📝 Available profile routes:');
    console.log('   PUT    /admin/profile        - Update profile');
    console.log('   POST   /admin/profile/avatar - Upload avatar');
    console.log('   DELETE /admin/profile/avatar - Remove avatar');
    console.log('   PUT    /admin/profile/password - Update password');
    console.log('   DELETE /admin/profile        - Delete account');
    console.log('Current theme:', this.theme);
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
.btn-primary,
.btn-outline,
.action-btn,
.status-item,
.badge-primary,
.badge-success,
.profile-avatar-large {
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

.btn-outline:hover:not(:disabled) {
  background-color: var(--hover-bg);
  border-color: var(--text-muted);
  transform: translateY(-1px);
}

.btn-outline:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: var(--bg-primary);
  color: var(--text-primary);
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: 1px solid var(--border-color);
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background-color: var(--hover-bg);
}

.btn-danger {
  background-color: #ef4444;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
}

.btn-danger:hover {
  background-color: #dc2626;
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

.form-input-error {
  border-color: #ef4444;
}

.form-input-error:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-error {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

/* Profile Avatar */
.profile-avatar-large {
  width: 8rem;
  height: 8rem;
  border-radius: 50%;
  background-color: var(--hover-bg);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid var(--border-color);
  overflow: hidden;
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
  font-weight: 500;
  color: var(--text-primary);
}

/* Badges */
.badge-primary,
.badge-success {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
}

/* Action Buttons */
.action-btn {
  width: 100%;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  background: transparent;
}

.action-btn:hover {
  background-color: var(--hover-bg);
}

/* Grid Utilities */
.grid {
  display: grid;
}

.grid-cols-1 {
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

.grid-cols-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
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

.justify-end {
  justify-content: flex-end;
}

/* Spacing */
.mb-8 {
  margin-bottom: 2rem;
}

.mb-6 {
  margin-bottom: 1.5rem;
}

.mb-4 {
  margin-bottom: 1rem;
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

.ml-auto {
  margin-left: auto;
}

/* Text Colors */
.text-gray-400 {
  color: #9ca3af;
}

.text-gray-500 {
  color: #6b7280;
}

.text-gray-900 {
  color: #111827;
}

.text-white {
  color: white;
}

.text-red-500 {
  color: #ef4444;
}

.text-green-500 {
  color: #10b981;
}

.text-red-800 {
  color: #991b1b;
}

.text-green-800 {
  color: #065f46;
}

.text-xl {
  font-size: 1.25rem;
}

.text-sm {
  font-size: 0.875rem;
}

.text-lg {
  font-size: 1.125rem;
}

.text-3xl {
  font-size: 1.875rem;
}

/* Font Weights */
.font-semibold {
  font-weight: 600;
}

.font-medium {
  font-weight: 500;
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

/* Dark mode specific styles */
.dark .text-gray-300 {
  color: #d1d5db;
}

.dark .text-gray-600 {
  color: #4b5563;
}

.dark .bg-gray-800 {
  background-color: #1f2937;
}

.dark .hover\:bg-red-900\/20:hover {
  background-color: rgba(127, 29, 29, 0.2);
}

/* Hidden */
.hidden {
  display: none;
}

/* Fixed positioning */
.fixed {
  position: fixed;
}

.inset-0 {
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}

.absolute {
  position: absolute;
}

.relative {
  position: relative;
}

.-bottom-2 {
  bottom: -0.5rem;
}

.-right-2 {
  right: -0.5rem;
}

/* Z-index */
.z-50 {
  z-index: 50;
}

/* Opacity */
.bg-opacity-50 {
  --tw-bg-opacity: 0.5;
}

/* Max width */
.max-w-md {
  max-width: 28rem;
}

/* Shadow */
.shadow-lg {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Rounded corners */
.rounded-lg {
  border-radius: 0.5rem;
}

.rounded-full {
  border-radius: 9999px;
}

/* Object fit */
.object-cover {
  object-fit: cover;
}

/* Overflow */
.overflow-hidden {
  overflow: hidden;
}

/* Cursor */
.cursor-pointer {
  cursor: pointer;
}
</style>