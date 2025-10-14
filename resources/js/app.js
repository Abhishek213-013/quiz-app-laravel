import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

// Import Chart.js globally
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import main App component for non-admin pages
import App from './components/App.vue';

console.log('🎯 Starting Vue app initialization...');

// Wait for DOM to be ready before mounting
const initApp = () => {
    if (window.location.pathname.startsWith('/admin')) {
        console.log('🔧 Using Inertia.js for admin pages');
        createInertiaApp({
            resolve: name => {
                const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
                return pages[`./Pages/${name}.vue`]
            },
            setup({ el, App, props, plugin }) {
                createApp({ render: () => h(App, props) })
                    .use(plugin)
                    .mount(el)
            },
        });
    } else {
        console.log('🔧 Using regular Vue app for main site');
        
        // Check if mount element exists
        const appElement = document.getElementById('app');
        if (!appElement) {
            console.error('❌ Cannot find #app element to mount Vue!');
            return;
        }
        
        console.log('✅ Found #app element, mounting Vue...');
        
        // Just mount App - components are already imported in App.vue
        const app = createApp(App);
        app.mount('#app');
        
        console.log('✅ Vue app mounted successfully!');
    }
};

// Wait for both DOM and Vite to be ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    // Small delay to ensure Vite styles are processed
    setTimeout(initApp, 50);
}