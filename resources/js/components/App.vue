<template>
    <div id="app" class="min-h-screen bg-gray-100">
        <Dashboard v-if="currentView === 'dashboard'" @navigate="changeView" />
        <QuizSets v-else-if="currentView === 'quiz-sets'" @back="changeView('dashboard')" @select-set="selectQuizSet" />
        <QuizPage 
            v-else-if="currentView === 'quiz-page'" 
            :quiz-set-id="selectedQuizSetId"
            @back="changeView('quiz-sets')" 
            @completed="changeView('dashboard')"
        />
        <ManageQuizzes 
            v-else-if="currentView === 'manage-quizzes'" 
            @back="changeView('dashboard')" 
        />
        <Records 
            v-else-if="currentView === 'records'" 
            @back="changeView('dashboard')" 
        />
    </div>
</template>

<script>
import Dashboard from './Dashboard.vue';
import QuizSets from './QuizSets.vue';
import QuizPage from './QuizPage.vue';
import ManageQuizzes from './ManageQuizzes.vue';
import Records from './Records.vue';

export default {
    name: 'App',
    components: {
        Dashboard,
        QuizSets,
        QuizPage,
        ManageQuizzes,
        Records
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
        selectQuizSet(quizSetId) {
            this.selectedQuizSetId = quizSetId;
            this.currentView = 'quiz-page';
        }
    }
}
</script>