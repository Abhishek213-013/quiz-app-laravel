import './bootstrap';
import { createApp } from 'vue';

// Import components
import App from './components/App.vue';
import Dashboard from './components/Dashboard.vue';
import QuizSets from './components/QuizSets.vue';
import QuizPage from './components/QuizPage.vue';
import ManageQuizzes from './components/ManageQuizzes.vue';
import Records from './components/Records.vue';

const app = createApp(App);

// Register components globally
app.component('Dashboard', Dashboard);
app.component('QuizSets', QuizSets);
app.component('QuizPage', QuizPage);
app.component('ManageQuizzes', ManageQuizzes);
app.component('Records', Records);

app.mount('#app');