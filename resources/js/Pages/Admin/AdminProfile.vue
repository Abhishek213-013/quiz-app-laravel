<template>
  <div class="min-h-screen" :class="isDark ? 'dark-theme' : 'light-theme'">
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
                          <i class="fas fa-user text-gray-400 mr-2"></i>
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
                        <i class="fas fa-user-tag text-gray-400 mr-2"></i>
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
                  <i class="fas fa-key text-green-500 mr-3"></i>
                  <h2 class="card-title">Update Password</h2>
                </div>
                <div class="card-body">
                  <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="form-group">
                      <label class="form-label">
                        <i class="fas fa-lock text-gray-400 mr-2"></i>
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
                          <i class="fas fa-lock text-gray-400 mr-2"></i>
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
                          <i class="fas fa-lock text-gray-400 mr-2"></i>
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
                        :disabled="passwordForm.processing"
                      >
                        <i class="fas fa-key mr-2"></i>
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
                  <i class="fas fa-camera text-purple-500 mr-3"></i>
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
                          <i class="fas fa-user text-3xl"></i>
                        </template>
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
                    <button 
                      @click="removeAvatar" 
                      class="btn-outline w-full"
                      :disabled="!profile.avatar"
                    >
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
                        <span class="badge-primary">{{ profile.role }}</span>
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
                    <button class="action-btn text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" @click="showDeleteConfirmation = true">
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

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirmation" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <div class="flex items-center mb-4">
          <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
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
            <i class="fas fa-trash-alt mr-2"></i>
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
    profile: {  // Add profile prop
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
      
      // CORRECT URL: /admin/profile (no /update)
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
      
      // CORRECT URL: /admin/profile/password
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
      isDark: false,
      mobileSidebar: false,
      showDeleteConfirmation: false
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
    },
    fullName() {
      return `${this.profile.firstName} ${this.profile.lastName}`.trim()
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
        
        // CORRECT URL: /admin/profile/avatar
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
        
        // CORRECT URL: /admin/profile/avatar
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
      
      // CORRECT URL: /admin/profile
      this.$inertia.delete('/admin/profile', {
        preserveScroll: false,
      });
    }
  },
  mounted() {
    console.log('🎯 Profile component mounted successfully!');
    console.log('📝 Available profile routes:');
    console.log('   PUT    /admin/profile        - Update profile');
    console.log('   POST   /admin/profile/avatar - Upload avatar');
    console.log('   DELETE /admin/profile/avatar - Remove avatar');
    console.log('   PUT    /admin/profile/password - Update password');
    console.log('   DELETE /admin/profile        - Delete account');
  }
}
</script>