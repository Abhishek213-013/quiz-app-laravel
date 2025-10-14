<template>
  <!-- Desktop Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <i class="fas fa-brain sidebar-logo"></i>
      <span class="sidebar-title">MindSpark Admin</span>
    </div>

    <nav class="sidebar-nav">
      <ul class="nav-list">
        <li>
          <a :href="getLink('/admin/dashboard')" class="nav-item" :class="{ active: isActive('/admin/dashboard') }">
            <i class="fas fa-home nav-icon"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>

        <li>
          <a :href="getLink('/admin/participants')" class="nav-item" :class="{ active: isActive('/admin/participants') }">
            <i class="fas fa-users nav-icon"></i>
            <span class="nav-text">Participants</span>
          </a>
        </li>

        <li>
          <a :href="getLink('/admin/quizzes')" class="nav-item" :class="{ active: isActive('/admin/quizzes') }">
            <i class="fas fa-clipboard-list nav-icon"></i>
            <span class="nav-text">Manage Quizzes</span>
          </a>
        </li>

        <li>
          <a :href="getLink('/admin/records')" class="nav-item" :class="{ active: isActive('/admin/records') }">
            <i class="fas fa-chart-bar nav-icon"></i>
            <span class="nav-text">Records</span>
          </a>
        </li>

        <!-- Admin Management Dropdown -->
        <li class="nav-dropdown">
          <div 
            class="nav-item dropdown-trigger" 
            :class="{ active: isAdminManagementActive() }"
            @click="toggleAdminManagement"
          >
            <i class="fas fa-cogs nav-icon"></i>
            <span class="nav-text">Admin Management</span>
            <i class="fas fa-chevron-down dropdown-arrow" :class="{ rotated: adminManagementOpen }"></i>
          </div>
          <transition name="dropdown">
            <ul v-if="adminManagementOpen" class="dropdown-menu">
              <li>
                <a 
                  :href="getLink('/admin/profile')" 
                  class="dropdown-item" 
                  :class="{ active: isActive('/admin/profile') }"
                  @click="closeDropdown"
                >
                  <i class="fas fa-user dropdown-icon"></i>
                  <span>Profile</span>
                </a>
              </li>
              <li>
                <a 
                  :href="getLink('/admin/settings')" 
                  class="dropdown-item" 
                  :class="{ active: isActive('/admin/settings') }"
                  @click="closeDropdown"
                >
                  <i class="fas fa-cog dropdown-icon"></i>
                  <span>Settings</span>
                </a>
              </li>
              <li>
                <a 
                  :href="getLink('/admin/customize')" 
                  class="dropdown-item" 
                  :class="{ active: isActive('/admin/customize') }"
                  @click="closeDropdown"
                >
                  <i class="fas fa-palette dropdown-icon"></i>
                  <span>Customize</span>
                </a>
              </li>
            </ul>
          </transition>
        </li>
      </ul>
    </nav>

    <div class="sidebar-footer">
      <div class="logged-in-as">Logged in as</div>
      <div class="user-info">
        <div class="user-avatar">
          <template v-if="profile.avatar">
            <img :src="profile.avatar" alt="Profile" class="w-full h-full rounded-full object-cover">
          </template>
          <template v-else>
            <i class="fas fa-user"></i>
          </template>
        </div>
        <div>
          <div class="user-name">{{ profile.firstName }} {{ profile.lastName }}</div>
          <div class="user-role">{{ profile.role }}</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- Mobile Sidebar -->
  <div v-if="mobileSidebar" class="mobile-sidebar-overlay">
    <div class="overlay-bg" @click="$emit('close-mobile-sidebar')"></div>
    <aside class="mobile-sidebar">
      <div class="mobile-sidebar-header">
        <div class="flex items-center gap-2">
          <i class="fas fa-brain"></i>
          <span class="font-semibold">Quiz Admin</span>
        </div>
        <button @click="$emit('close-mobile-sidebar')" class="close-btn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <nav class="mobile-nav">
        <a :href="getLink('/admin/dashboard')" class="mobile-nav-item" :class="{ active: isActive('/admin/dashboard') }" @click="$emit('close-mobile-sidebar')">Dashboard</a>
        <a :href="getLink('/admin/participants')" class="mobile-nav-item" :class="{ active: isActive('/admin/participants') }" @click="$emit('close-mobile-sidebar')">Participants</a>
        <a :href="getLink('/admin/quizzes')" class="mobile-nav-item" :class="{ active: isActive('/admin/quizzes') }" @click="$emit('close-mobile-sidebar')">Manage Quizzes</a>
        <a :href="getLink('/admin/records')" class="mobile-nav-item" :class="{ active: isActive('/admin/records') }" @click="$emit('close-mobile-sidebar')">Records</a>
        
        <!-- Mobile Admin Management Section -->
        <div class="mobile-section-divider">Admin Management</div>
        <a :href="getLink('/admin/profile')" class="mobile-nav-item" :class="{ active: isActive('/admin/profile') }" @click="$emit('close-mobile-sidebar')">Profile</a>
        <a :href="getLink('/admin/settings')" class="mobile-nav-item" :class="{ active: isActive('/admin/settings') }" @click="$emit('close-mobile-sidebar')">Settings</a>
        <a :href="getLink('/admin/customize')" class="mobile-nav-item" :class="{ active: isActive('/admin/customize') }" @click="$emit('close-mobile-sidebar')">Customize</a>
      </nav>
    </aside>
  </div>
</template>

<script>
export default {
  name: 'AdminSidebar',
  props: {
    mobileSidebar: {
      type: Boolean,
      default: false
    },
    currentPage: {
      type: String,
      default: ''
    },
    profile: {
      type: Object,
      default: () => ({
        firstName: 'Admin',
        lastName: 'User',
        role: 'Admin',
        avatar: null
      })
    }
  },
  emits: ['close-mobile-sidebar'],
  data() {
    return {
      adminManagementOpen: false
    }
  },
  methods: {
    isActive(path) {
      return this.currentPage === path || window.location.pathname === path;
    },
    isAdminManagementActive() {
      return this.isActive('/admin/profile') || 
             this.isActive('/admin/settings') || 
             this.isActive('/admin/customize');
    },
    getLink(path) {
      return path;
    },
    toggleAdminManagement() {
      this.adminManagementOpen = !this.adminManagementOpen;
    },
    closeDropdown() {
      this.adminManagementOpen = false;
    }
  },
  mounted() {
    // Auto-open dropdown if current page is in admin management
    if (this.isAdminManagementActive()) {
      this.adminManagementOpen = true;
    }
  }
}
</script>

<style scoped>
/* Add this for avatar image in sidebar */
.user-avatar img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.sidebar {
  width: 16rem;
  background-color: var(--bg-sidebar);
  border-right: 1px solid var(--border-color);
  display: none;
  flex-direction: column;
  position: sticky;
  top: 4rem;
  height: calc(100vh - 4rem);
}
@media (min-width: 768px) {
  .sidebar {
    display: flex;
  }
}

.sidebar-header {
  padding: 1.5rem 1.5rem 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border-bottom: 1px solid var(--border-color);
}

.sidebar-logo {
  color: #2563eb;
  font-size: 1.5rem;
}
.dark-theme .sidebar-logo {
  color: #60a5fa;
}

.sidebar-title {
  font-weight: 700;
  font-size: 1.125rem;
  color: var(--text-primary);
}

.sidebar-nav {
  flex: 1;
  overflow: auto;
  padding: 0.5rem;
}

.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  color: var(--text-secondary);
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
}
.nav-item:hover {
  background-color: var(--hover-bg);
}
.nav-item.active {
  background-color: #dbeafe;
  color: #2563eb;
  font-weight: 600;
}
.dark-theme .nav-item.active {
  background-color: #1e3a8a;
  color: #60a5fa;
}

.nav-icon {
  width: 1.25rem;
  flex-shrink: 0;
}

.nav-text {
  color: inherit;
  flex: 1;
}

/* Dropdown Styles */
.nav-dropdown {
  position: relative;
}

.dropdown-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
}

.dropdown-arrow {
  font-size: 0.75rem;
  transition: transform 0.2s ease;
  color: var(--text-muted);
}

.dropdown-arrow.rotated {
  transform: rotate(180deg);
}

.dropdown-menu {
  list-style: none;
  padding: 0.25rem 0;
  margin: 0.25rem 0 0 0;
  background-color: var(--bg-secondary);
  border-radius: 0.375rem;
  border: 1px solid var(--border-color);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  position: relative;
  z-index: 10;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 1rem 0.5rem 2.5rem;
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
}

.dropdown-item:hover {
  background-color: var(--hover-bg);
  color: var(--text-primary);
}

.dropdown-item.active {
  background-color: #dbeafe;
  color: #2563eb;
  border-left-color: #2563eb;
  font-weight: 500;
}
.dark-theme .dropdown-item.active {
  background-color: #1e3a8a;
  color: #60a5fa;
  border-left-color: #60a5fa;
}

.dropdown-icon {
  width: 1rem;
  font-size: 0.875rem;
  flex-shrink: 0;
}

/* Dropdown transition */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
  max-height: 200px;
  overflow: hidden;
}

.dropdown-enter-from,
.dropdown-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-10px);
}

/* Mobile Section Divider */
.mobile-section-divider {
  padding: 0.75rem 0.75rem 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-top: 1px solid var(--border-color);
  margin-top: 0.5rem;
  padding-top: 1rem;
}

.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid var(--border-color);
}

.logged-in-as {
  font-size: 0.75rem;
  color: var(--text-muted);
  margin-bottom: 0.5rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
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
.dark-theme .user-avatar {
  background-color: #1e3a8a;
  color: #60a5fa;
}

.user-name {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-primary);
}

.user-role {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.mobile-sidebar-overlay {
  position: fixed;
  inset: 0;
  z-index: 20;
}
@media (min-width: 768px) {
  .mobile-sidebar-overlay {
    display: none;
  }
}

.overlay-bg {
  position: fixed;
  inset: 0;
  background-color: black;
  opacity: 0.5;
}

.mobile-sidebar {
  position: fixed;
  inset: 0;
  left: 0;
  width: 16rem;
  background-color: var(--bg-sidebar);
  z-index: 30;
  border-right: 1px solid var(--border-color);
  padding: 1rem;
  overflow: auto;
}

.mobile-sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}
.mobile-sidebar-header i {
  color: #2563eb;
}
.dark-theme .mobile-sidebar-header i {
  color: #60a5fa;
}
.mobile-sidebar-header span {
  font-weight: 600;
  color: var(--text-primary);
}

.close-btn {
  padding: 0.5rem;
  border-radius: 0.375rem;
  color: var(--text-secondary);
}
.close-btn:hover {
  background-color: var(--hover-bg);
}

.mobile-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.mobile-nav-item {
  display: block;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  color: var(--text-secondary);
  text-decoration: none;
}
.mobile-nav-item:hover {
  background-color: var(--hover-bg);
}
.mobile-nav-item.active {
  background-color: #dbeafe;
  color: #2563eb;
}
.dark-theme .mobile-nav-item.active {
  background-color: #1e3a8a;
  color: #60a5fa;
}

/* Utility Classes */
.flex { display: flex; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.fixed { position: fixed; }
.sticky { position: sticky; }
.inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
.w-64 { width: 16rem; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.p-2 { padding: 0.5rem; }
.p-4 { padding: 1rem; }
.px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
.py-5 { padding-top: 1.25rem; padding-bottom: 1.25rem; }
.rounded { border-radius: 0.25rem; }
.rounded-md { border-radius: 0.375rem; }
.text-sm { font-size: 0.875rem; }
.text-xs { font-size: 0.75rem; }
.text-lg { font-size: 1.125rem; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.font-bold { font-weight: 700; }
.hidden { display: none; }
@media (min-width: 768px) {
  .md\\:flex { display: flex; }
  .md\\:hidden { display: none; }
}
.flex-1 { flex: 1; }
.overflow-auto { overflow: auto; }
.z-20 { z-index: 20; }
.z-30 { z-index: 30; }
</style>