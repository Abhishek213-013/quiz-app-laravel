<template>
    <div id="app" class="min-h-screen bg-gray-100 flex flex-col">
        <!-- Navbar -->
        <Navbar :current-view="currentView" @navigate="handleNavigation" />

        <!-- Main Content -->
        <main class="flex-1">
            <Dashboard 
                v-if="currentView === 'dashboard'" 
                @navigate="changeView" 
            />
            <QuizSets 
                v-else-if="currentView === 'quiz-sets'" 
                @select-set="selectQuizSet" 
            />
            <QuizPage 
                v-else-if="currentView === 'quiz-page'" 
                :quiz-set-id="selectedQuizSetId"
                @completed="changeView('dashboard')"
            />
            <GkBlog 
                v-else-if="currentView === 'gk-blog'" 
            />
            <Records 
                v-else-if="currentView === 'records'" 
            />
        </main>

        <!-- Footer -->
        <Footer></Footer>
    </div>
</template>

<script>
import Navbar from './Navbar.vue';
import Dashboard from './Dashboard.vue';
import QuizSets from './QuizSets.vue';
import QuizPage from './QuizPage.vue';
import GkBlog from './GkBlog.vue';
import Records from './Records.vue';
import Footer from './Footer.vue';

export default {
    name: 'App',
    components: {
        Navbar,
        Dashboard,
        QuizSets,
        QuizPage,
        GkBlog,
        Records,
        Footer
    },
    data() {
        return {
            currentView: 'dashboard',
            selectedQuizSetId: null
        }
    },
    methods: {
        changeView(view) {
            this.currentView = view;
        },
        
        handleNavigation(route) {
            this.currentView = route;
        },
        
        selectQuizSet(quizSetId) {
            this.selectedQuizSetId = quizSetId;
            this.currentView = 'quiz-page';
        }
    }
}
</script>