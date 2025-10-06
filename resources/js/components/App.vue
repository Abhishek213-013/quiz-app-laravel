<template>
    <div id="app" class="min-h-screen bg-gray-100">
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
            <ManageQuizzes 
                v-else-if="currentView === 'manage-quizzes'" 
            />
            <Records 
                v-else-if="currentView === 'records'" 
            />
        </main>
    </div>
</template>

<script>
import Navbar from './Navbar.vue';
import Dashboard from './Dashboard.vue';
import QuizSets from './QuizSets.vue';
import QuizPage from './QuizPage.vue';
import ManageQuizzes from './ManageQuizzes.vue';
import Records from './Records.vue';

export default {
    name: 'App',
    components: {
        Navbar,
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
        
        handleNavigation(route) {
            if (route === 'manage-quizzes') {
                // For manage quizzes, we need to check secret key first
                this.promptForSecretKey();
            } else {
                this.currentView = route;
            }
        },
        
        selectQuizSet(quizSetId) {
            this.selectedQuizSetId = quizSetId;
            this.currentView = 'quiz-page';
        },
        
        async promptForSecretKey() {
            const secretKey = prompt('Please enter the secret key to access Manage Quizzes:');
            if (secretKey) {
                try {
                    const response = await fetch('/api/admin/verify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            secret_key: secretKey
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.currentView = 'manage-quizzes';
                    } else {
                        alert('Invalid secret key!');
                    }
                } catch (error) {
                    console.error('Error verifying secret key:', error);
                    alert('Error verifying secret key');
                }
            }
        }
    }
}
</script>