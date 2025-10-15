import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

// Import Chart.js globally
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import main App component for non-admin pages
import App from './components/App.vue';

console.log('🎯 Starting Vue app initialization...');

// Simple CSS readiness check
function waitForCSS() {
    return new Promise((resolve) => {
        // Check if Font Awesome is loaded
        const testIcon = document.createElement('i');
        testIcon.className = 'fas fa-check';
        document.body.appendChild(testIcon);
        
        const checkReady = () => {
            const style = window.getComputedStyle(testIcon);
            const isReady = style.fontFamily.includes('Font Awesome');
            document.body.removeChild(testIcon);
            
            if (isReady) {
                resolve(true);
            } else {
                setTimeout(() => resolve(true), 100);
            }
        };
        
        checkReady();
    });
}

const initApp = async () => {
    try {
        // Wait for CSS to be ready
        await waitForCSS();
        console.log('✅ CSS and fonts are ready');
        
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
                        .mount(el);
                    console.log('✅ Inertia app mounted');
                },
            });
        } else {
            console.log('🔧 Using regular Vue app for main site');
            
            const appElement = document.getElementById('app');
            if (!appElement) {
                console.error('❌ Cannot find #app element to mount Vue!');
                return;
            }
            
            console.log('✅ Found #app element, mounting Vue...');
            
            // Add v-cloak to prevent FOUC
            appElement.setAttribute('v-cloak', '');
            
            const app = createApp(App);
            app.mount('#app');
            
            // Remove v-cloak after mount
            setTimeout(() => {
                appElement.removeAttribute('v-cloak');
            }, 50);
            
            console.log('✅ Vue app mounted successfully!');
        }
    } catch (error) {
        console.error('❌ Error initializing app:', error);
    }
};

// Start app with proper timing
const startApp = () => {
    // Use double requestAnimationFrame for reliable timing
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            initApp();
        });
    });
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startApp);
} else {
    startApp();
}

// Vite HMR support
if (import.meta.hot) {
    import.meta.hot.accept();
}