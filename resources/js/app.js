import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
// Import Chart.js globally
import Chart from 'chart.js/auto';
window.Chart = Chart;
// Import main app components for non-admin pages
import App from './components/App.vue';
import Navbar from './components/Navbar.vue';
import Dashboard from './components/Dashboard.vue';
import QuizSets from './components/QuizSets.vue';
import QuizPage from './components/QuizPage.vue';
import GkBlog from './components/GkBlog.vue';
import Records from './components/Records.vue';
import Footer from './components/Footer.vue';

// Check if we're on admin pages (using Inertia) or regular pages (using existing Vue app)
if (window.location.pathname.startsWith('/admin')) {
    // Use Inertia.js for admin pages
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
    // Use existing Vue app for regular pages
    const app = createApp(App);
    
    // Register components globally
    app.component('Navbar', Navbar);
    app.component('Dashboard', Dashboard);
    app.component('QuizSets', QuizSets);
    app.component('QuizPage', QuizPage);
    app.component('GkBlog', GkBlog);
    app.component('Records', Records);
    app.component('Footer', Footer);
    
    app.mount('#app');
}