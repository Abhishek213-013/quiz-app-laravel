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
      </ul>
    </nav>

    <div class="sidebar-footer">
      <div class="logged-in-as">Logged in as</div>
      <div class="user-info">
        <div class="user-avatar">
          <i class="fas fa-user"></i>
        </div>
        <div>
          <div class="user-name">Administrator</div>
          <div class="user-role">Super Admin</div>
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
    }
  },
  emits: ['close-mobile-sidebar'],
  methods: {
    isActive(path) {
      return this.currentPage === path || window.location.pathname === path;
    },
    getLink(path) {
      // You can modify this based on your routing setup
      return path;
    }
  }
}
</script>

<style scoped>
/* Import Font Awesome */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

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
}

.nav-text {
  color: inherit;
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