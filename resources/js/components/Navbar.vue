<template>
    <nav style="background: white; color: black; padding: 15px; position: relative; z-index: 9999;">

        
        
        <!-- Simple Navbar Content -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <!-- Logo -->
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">🧠</span>
                <h1 style="margin: 0; font-size: 20px; font-weight: bold;">MindSpark</h1>
            </div>

            <!-- Navigation Links -->
            <div style="display: flex; gap: 15px;">
                <button 
                    v-for="item in navigation" 
                    :key="item.route"
                    @click="handleNavigation(item.route)"
                    :style="{
                        padding: '8px 16px',
                        background: currentView === item.route ? 'gray' : 'transparent',
                        color: 'black',
                        border: '1px solid white',
                        borderRadius: '4px',
                        cursor: 'pointer'
                    }"
                >
                    {{ item.label }}
                </button>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'Navbar',
    props: {
        currentView: String
    },
    data() {
        return {
            navigation: [
                { route: 'dashboard', label: 'Dashboard' },
                { route: 'quiz-sets', label: 'Take Quiz' },
                { route: 'gk-blog', label: 'GK Blog' },
                { route: 'records', label: 'Previous Records' }
            ]
        }
    },
    methods: {
        handleNavigation(route) {
            console.log('🔼 Navbar: Emitting navigate event with route:', route);
            this.$emit('navigate', route);
        }
    },
    mounted() {
        console.log('🧭 Simplified Navbar mounted');
        console.log('🧭 Navbar element:', this.$el);
    }
}
</script>