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
              <span v-if="unreadNotifications > 0" class="notification-badge">
                {{ unreadNotifications }}
              </span>
            </button>
            
            <!-- Notifications Dropdown -->
            <div v-if="showNotifications" class="notification-dropdown">
              <div class="notification-header">
                <h3>Notifications</h3>
                <span @click="markAllAsRead" class="mark-read">Mark all as read</span>
              </div>
              <div class="notification-list">
                <div v-for="notification in notifications" :key="notification.id" 
                     class="notification-item">
                  <div class="flex items-start">
                    <div class="notification-icon" :class="notification.bgColor">
                      <i :class="notification.icon + ' text-white text-sm'"></i>
                    </div>
                    <div class="flex-1">
                      <p class="notification-message">{{ notification.message }}</p>
                      <p class="notification-time">{{ notification.time }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="notification-footer">
                <a href="#" class="view-all">View all notifications</a>
              </div>
            </div>
          </div>

          <!-- Profile Dropdown -->
          <div class="relative">
            <button @click="toggleProfile" class="profile-btn">
              <div class="profile-avatar">
                <i class="fas fa-user"></i>
              </div>
              <span class="profile-name">Administrator</span>
              <i class="fas fa-chevron-down profile-arrow"></i>
            </button>
            
            <!-- Profile Dropdown Menu -->
            <div v-if="showProfile" class="profile-dropdown">
              <div class="profile-info">
                <div class="profile-name-main">Administrator</div>
                <div class="profile-email">admin@quiz.com</div>
              </div>
              <div class="profile-menu">
                <a href="#" class="profile-menu-item">Profile</a>
                <a href="#" class="profile-menu-item">Settings</a>
                <a href="#" class="profile-menu-item">Customize</a>
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
    }
  },
  data() {
    return {
      showNotifications: false,
      showProfile: false,
      unreadNotifications: 3,
      notifications: [
        {
          id: 1,
          message: "New participant registered for Geography quiz",
          time: "10 minutes ago",
          icon: "fas fa-user-plus",
          bgColor: "bg-blue"
        },
        {
          id: 2,
          message: "Quiz attempt scored 95% in Science category",
          time: "1 hour ago",
          icon: "fas fa-trophy",
          bgColor: "bg-green"
        },
        {
          id: 3,
          message: "New quiz set 'World Capitals' has been published",
          time: "2 hours ago",
          icon: "fas fa-globe",
          bgColor: "bg-purple"
        }
      ]
    }
  },
  emits: ['toggle-theme', 'toggle-mobile-sidebar', 'logout'],
  mounted() {
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    toggleNotifications() {
      this.showNotifications = !this.showNotifications
      this.showProfile = false
    },
    toggleProfile() {
      this.showProfile = !this.showProfile
      this.showNotifications = false
    },
    markAllAsRead() {
      this.unreadNotifications = 0
      this.showNotifications = false
    },
    handleClickOutside(event) {
      if (!event.target.closest('.relative')) {
        this.showNotifications = false
        this.showProfile = false
      }
    },
    handleLogout() {
      // Close the profile dropdown
      this.showProfile = false
      
      // Emit the logout event to parent component
      this.$emit('logout')
      
      // You can also directly redirect here if preferred:
      // window.location.href = '/admin/login'
    }
  }
}
</script>

<style scoped>
/* Import Font Awesome */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

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
.view-all {
  font-size: 0.875rem;
  color: #3b82f6;
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
.profile-menu-item {
  display: block;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  color: var(--text-primary);
  border-radius: 0.375rem;
}
.profile-menu-item:hover {
  background-color: var(--hover-bg);
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