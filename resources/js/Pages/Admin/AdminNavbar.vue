<template>
  <header class="header">
    <div class="header-container">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center gap-4">
          <!-- Mobile menu button -->
          <button @click="$emit('toggle-mobile-sidebar')" class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
          </button>
          <h1 class="header-title">{{ title }}</h1>
        </div>

        <div class="flex items-center gap-2">
          <!-- Theme Toggle -->
          <button @click="$emit('toggle-theme')" class="theme-btn">
            <i class="fas" :class="isDark ? 'fa-sun' : 'fa-moon'"></i>
          </button>

          <!-- Notifications -->
          <div class="relative">
            <button @click="toggleNotifications" class="notification-btn">
              <i class="fas fa-bell"></i>
              <span v-if="unreadNotificationsCount > 0" class="notification-badge">
                {{ unreadNotificationsCount }}
              </span>
            </button>
            
            <!-- Notifications Dropdown -->
            <div v-if="showNotifications" class="notification-dropdown">
              <div class="notification-header">
                <h3>Notifications</h3>
                <span @click="markAllAsRead" class="mark-read">Mark all as read</span>
              </div>
              <div class="notification-list">
                <div v-if="notifications.length === 0" class="notification-empty">
                  <i class="fas fa-bell-slash text-gray-400 text-2xl mb-2"></i>
                  <p class="text-gray-500">No notifications yet</p>
                </div>
                <div v-else v-for="notification in notifications" :key="notification.id" 
                     class="notification-item" :class="{ 'unread': !notification.read }"
                     @click="markAsRead(notification.id)">
                  <div class="flex items-start">
                    <div class="notification-icon" :class="getNotificationIcon(notification.type).bgColor">
                      <i :class="getNotificationIcon(notification.type).icon + ' text-white text-sm'"></i>
                    </div>
                    <div class="flex-1">
                      <p class="notification-message">{{ notification.message }}</p>
                      <p class="notification-time">{{ formatTime(notification.created_at) }}</p>
                    </div>
                    <div v-if="!notification.read" class="unread-dot"></div>
                  </div>
                </div>
              </div>
              <div class="notification-footer">
                <a href="#" class="view-all" @click.prevent="viewAllNotifications">
                  View all notifications
                </a>
              </div>
            </div>
          </div>

          <!-- Profile Dropdown -->
          <div class="relative">
            <button @click="toggleProfile" class="profile-btn">
              <div class="profile-avatar">
                <template v-if="profile.avatar">
                  <img :src="profile.avatar" alt="Profile" class="w-full h-full rounded-full object-cover">
                </template>
                <template v-else>
                  <i class="fas fa-user"></i>
                </template>
              </div>
              <span class="profile-name">{{ profile.firstName }} {{ profile.lastName }}</span>
              <i class="fas fa-chevron-down profile-arrow"></i>
            </button>
            
            <!-- Profile Dropdown Menu -->
            <div v-if="showProfile" class="profile-dropdown">
              <div class="profile-info">
                <div class="profile-name-main">{{ profile.firstName }} {{ profile.lastName }}</div>
                <div class="profile-email">{{ profile.email }}</div>
              </div>
              <div class="profile-menu">
                <a href="/admin/profile" class="profile-menu-item" @click.prevent="navigateTo('/admin/profile')">
                  Profile
                </a>
                <a href="/admin/settings" class="profile-menu-item" @click.prevent="navigateTo('/admin/settings')">
                  Settings
                </a>
                <a href="/admin/customize" class="profile-menu-item" @click.prevent="navigateTo('/admin/customize')">
                  Customize
                </a>
              </div>
              <div class="profile-footer">
                <button @click="handleLogout" class="logout-btn">
                  <i class="fas fa-sign-out-alt"></i>
                  <span>Logout</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { ref, onMounted, onUnmounted, computed } from 'vue'

export default {
  name: 'AdminNavbar',
  props: {
    title: {
      type: String,
      default: 'Admin Dashboard'
    },
    isDark: {
      type: Boolean,
      default: false
    },
    profile: {
      type: Object,
      default: () => ({
        firstName: 'Admin',
        lastName: 'User',
        email: 'admin@quiz.com',
        avatar: null
      })
    }
  },
  emits: ['toggle-theme', 'toggle-mobile-sidebar', 'logout'],
  setup(props, { emit }) {
    const showNotifications = ref(false)
    const showProfile = ref(false)
    const notifications = ref([])
    let notificationInterval = null
    let echo = null

    // Computed property for unread notifications count
    const unreadNotificationsCount = computed(() => {
      return notifications.value.filter(notification => !notification.read).length
    })

    // Initialize notifications
    const initializeNotifications = async () => {
      try {
        // Load existing notifications
        await loadNotifications()
        
        // Start polling for new notifications (fallback if Pusher/Echo is not available)
        startNotificationPolling()
        
        // Initialize real-time notifications (if using Laravel Echo/Pusher)
        initializeRealtimeNotifications()
      } catch (error) {
        console.error('Error initializing notifications:', error)
      }
    }

    // Load notifications from API
    const loadNotifications = async () => {
      try {
        const response = await fetch('/admin/notifications')
        if (response.ok) {
          const data = await response.json()
          notifications.value = data.notifications || []
        }
      } catch (error) {
        console.error('Error loading notifications:', error)
        // Fallback to empty array
        notifications.value = []
      }
    }

    // Poll for new notifications every 30 seconds
    const startNotificationPolling = () => {
      notificationInterval = setInterval(loadNotifications, 30000)
    }

    // Initialize real-time notifications with Laravel Echo
    const initializeRealtimeNotifications = () => {
      // Check if Echo is available (if you're using Laravel Echo with Pusher/Socket.io)
      if (window.Echo) {
        echo = window.Echo
        
        // Listen for new quiz attempts
        echo.channel('quiz-attempts')
          .listen('QuizAttemptCreated', (event) => {
            addNewNotification({
              id: Date.now(), // Temporary ID
              type: 'quiz_attempt',
              message: `${event.participantName} attempted ${event.quizSetName} with ${event.score}% score`,
              created_at: new Date().toISOString(),
              read: false
            })
          })
        
        // Listen for new participants
        echo.channel('participants')
          .listen('NewParticipantRegistered', (event) => {
            addNewNotification({
              id: Date.now(),
              type: 'new_participant',
              message: `New participant registered: ${event.participantName}`,
              created_at: new Date().toISOString(),
              read: false
            })
          })
      }
    }

    // Add new notification to the list
    const addNewNotification = (notification) => {
      notifications.value.unshift(notification)
      
      // Keep only the latest 50 notifications
      if (notifications.value.length > 50) {
        notifications.value = notifications.value.slice(0, 50)
      }
      
      // Play notification sound (optional)
      playNotificationSound()
    }

    // Play notification sound
    const playNotificationSound = () => {
      // You can add a notification sound here
      // const audio = new Audio('/notification-sound.mp3')
      // audio.play().catch(e => console.log('Audio play failed:', e))
    }

    // Get notification icon based on type
    const getNotificationIcon = (type) => {
      const icons = {
        quiz_attempt: { icon: 'fas fa-clipboard-check', bgColor: 'bg-blue' },
        new_participant: { icon: 'fas fa-user-plus', bgColor: 'bg-green' },
        high_score: { icon: 'fas fa-trophy', bgColor: 'bg-yellow' },
        system: { icon: 'fas fa-cog', bgColor: 'bg-purple' }
      }
      return icons[type] || { icon: 'fas fa-bell', bgColor: 'bg-gray' }
    }

    // Format time for display
    const formatTime = (timestamp) => {
      const now = new Date()
      const time = new Date(timestamp)
      const diffInSeconds = Math.floor((now - time) / 1000)
      
      if (diffInSeconds < 60) return 'Just now'
      if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`
      if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`
      return `${Math.floor(diffInSeconds / 86400)}d ago`
    }

    // Toggle notifications dropdown
    const toggleNotifications = () => {
      showNotifications.value = !showNotifications.value
      showProfile.value = false
      
      // Mark all as read when opening notifications
      if (showNotifications.value && unreadNotificationsCount.value > 0) {
        markAllAsRead()
      }
    }

    // Toggle profile dropdown
    const toggleProfile = () => {
      showProfile.value = !showProfile.value
      showNotifications.value = false
    }

    // Mark a single notification as read
    const markAsRead = async (notificationId) => {
      const notification = notifications.value.find(n => n.id === notificationId)
      if (notification && !notification.read) {
        notification.read = true
        
        // Send API request to mark as read
        try {
          await fetch(`/admin/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
          })
        } catch (error) {
          console.error('Error marking notification as read:', error)
        }
      }
    }

    // Mark all notifications as read
    const markAllAsRead = async () => {
      const unreadNotifications = notifications.value.filter(n => !n.read)
      
      if (unreadNotifications.length > 0) {
        unreadNotifications.forEach(notification => {
          notification.read = true
        })
        
        // Send API request to mark all as read
        try {
          await fetch('/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
          })
        } catch (error) {
          console.error('Error marking all notifications as read:', error)
        }
      }
    }

    // View all notifications (navigate to notifications page)
    const viewAllNotifications = () => {
      showNotifications.value = false
      // Navigate to notifications page if you have one
      // window.location.href = '/admin/notifications'
    }

    // Handle click outside dropdowns
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        showNotifications.value = false
        showProfile.value = false
      }
    }

    // Navigate to URL
    const navigateTo = (url) => {
      showProfile.value = false
      if (window.location.pathname !== url) {
        window.location.href = url
      }
    }

    // Handle logout
    const handleLogout = () => {
      showProfile.value = false
      emit('logout')
    }

    // Lifecycle hooks
    onMounted(() => {
      initializeNotifications()
      document.addEventListener('click', handleClickOutside)
    })

    onUnmounted(() => {
      if (notificationInterval) {
        clearInterval(notificationInterval)
      }
      if (echo) {
        echo.leaveChannel('quiz-attempts')
        echo.leaveChannel('participants')
      }
      document.removeEventListener('click', handleClickOutside)
    })

    return {
      showNotifications,
      showProfile,
      notifications,
      unreadNotificationsCount,
      toggleNotifications,
      toggleProfile,
      markAsRead,
      markAllAsRead,
      viewAllNotifications,
      getNotificationIcon,
      formatTime,
      navigateTo,
      handleLogout
    }
  }
}
</script>

<style scoped>
/* Add this style for the avatar image */
.profile-avatar img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

/* Notification styles */
.notification-empty {
  padding: 2rem 1rem;
  text-align: center;
  color: var(--text-muted);
}

.notification-item.unread {
  background-color: var(--hover-bg);
  border-left: 3px solid #3b82f6;
}

.unread-dot {
  width: 8px;
  height: 8px;
  background-color: #3b82f6;
  border-radius: 50%;
  margin-left: 0.5rem;
}

.bg-yellow {
  background-color: #f59e0b;
}

.bg-gray {
  background-color: #6b7280;
}

/* Your existing styles remain the same */
.profile-menu-item {
  display: block;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  color: var(--text-primary);
  border-radius: 0.375rem;
  text-decoration: none;
  cursor: pointer;
}
.profile-menu-item:hover {
  background-color: var(--hover-bg);
}

.view-all {
  font-size: 0.875rem;
  color: #3b82f6;
  text-decoration: none;
}

.header {
  background-color: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
  position: sticky;
  top: 0;
  z-index: 30;
}

.header-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.header-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
}

.mobile-menu-btn {
  padding: 0.5rem;
  border-radius: 0.375rem;
  color: var(--text-secondary);
}
.mobile-menu-btn:hover {
  background-color: var(--hover-bg);
}

.theme-btn {
  padding: 0.5rem;
  border-radius: 0.375rem;
  color: var(--text-secondary);
}
.theme-btn:hover {
  background-color: var(--hover-bg);
}

.notification-btn {
  padding: 0.5rem;
  border-radius: 0.375rem;
  color: var(--text-secondary);
  position: relative;
}
.notification-btn:hover {
  background-color: var(--hover-bg);
}

.notification-badge {
  position: absolute;
  top: -0.25rem;
  right: -0.25rem;
  background-color: #ef4444;
  color: white;
  font-size: 0.75rem;
  border-radius: 50%;
  height: 1.25rem;
  width: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-dropdown {
  position: absolute;
  right: 0;
  margin-top: 0.5rem;
  width: 20rem;
  background-color: var(--bg-secondary);
  border-radius: 0.5rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--border-color);
  z-index: 40;
}

.notification-header {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.notification-header h3 {
  font-weight: 600;
  color: var(--text-primary);
}
.mark-read {
  font-size: 0.75rem;
  color: #3b82f6;
  cursor: pointer;
}

.notification-list {
  max-height: 24rem;
  overflow-y: auto;
}

.notification-item {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
  cursor: pointer;
  transition: background-color 0.2s;
}
.notification-item:hover {
  background-color: var(--hover-bg);
}

.notification-icon {
  border-radius: 50%;
  padding: 0.5rem;
  margin-right: 0.75rem;
  flex-shrink: 0;
}
.bg-blue { background-color: #3b82f6; }
.bg-green { background-color: #10b981; }
.bg-purple { background-color: #8b5cf6; }

.notification-message {
  font-size: 0.875rem;
  color: var(--text-primary);
  font-weight: 500;
}

.notification-time {
  font-size: 0.75rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
}

.notification-footer {
  padding: 0.5rem;
  text-align: center;
  border-top: 1px solid var(--border-color);
}

.profile-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  border-radius: 0.375rem;
  color: var(--text-secondary);
}
.profile-btn:hover {
  background-color: var(--hover-bg);
}

.profile-avatar {
  width: 2rem;
  height: 2rem;
  background-color: #dbeafe;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2563eb;
  font-size: 0.875rem;
}
.dark-theme .profile-avatar {
  background-color: #1e3a8a;
  color: #60a5fa;
}

.profile-name {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-primary);
}

.profile-arrow {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.profile-dropdown {
  position: absolute;
  right: 0;
  margin-top: 0.5rem;
  width: 12rem;
  background-color: var(--bg-secondary);
  border-radius: 0.5rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--border-color);
  z-index: 40;
}

.profile-info {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
}
.profile-name-main {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-primary);
}
.profile-email {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.profile-menu {
  padding: 0.5rem;
}

.profile-footer {
  padding: 0.5rem;
  border-top: 1px solid var(--border-color);
}
.logout-btn {
  width: 100%;
  text-align: left;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  color: var(--text-primary);
  border-radius: 0.375rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  background: none;
  border: none;
  font-family: inherit;
}
.logout-btn:hover {
  background-color: var(--hover-bg);
}

/* Utility Classes */
.flex { display: flex; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.relative { position: relative; }
.absolute { position: absolute; }
.h-16 { height: 4rem; }
.gap-2 { gap: 0.5rem; }
.gap-4 { gap: 1rem; }
.p-2 { padding: 0.5rem; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.rounded-md { border-radius: 0.375rem; }
.text-sm { font-size: 0.875rem; }
.text-xs { font-size: 0.75rem; }
.text-xl { font-size: 1.25rem; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.text-center { text-align: center; }
.text-white { color: white; }
.hidden { display: none; }
@media (min-width: 768px) {
  .md\\:hidden { display: none; }
}
.flex-shrink-0 { flex-shrink: 0; }
.overflow-auto { overflow: auto; }
.overflow-y-auto { overflow-y: auto; }
.z-40 { z-index: 40; }
</style>