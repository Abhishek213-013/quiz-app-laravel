<template>
    <div id="app" class="min-h-screen bg-gray-100 flex flex-col">
        
        <!-- Navbar -->
        <Navbar :current-view="currentView" @navigate="handleNavigation" />

        <!-- Main Content -->
        <main class="flex-1">
            <Dashboard 
                v-if="currentView === 'dashboard'" 
                @navigate="handleNavigation" 
            />
            <QuizSets 
                v-else-if="currentView === 'quiz-sets'" 
                @select-set="selectQuizSet" 
            />
            <QuizPage 
                v-else-if="currentView === 'quiz-page'" 
                :quiz-set-id="selectedQuizSetId"
                @completed="handleNavigation('dashboard')"
            />
            <GkBlog 
                v-else-if="currentView === 'gk-blog'" 
            />
            <Records 
                v-else-if="currentView === 'records'" 
            />
            
            <!-- Fallback if no view matches -->
            <div v-else class="container mx-auto p-8 text-center">
                <h2 class="text-2xl font-bold text-red-600">Unknown View: {{ currentView }}</h2>
                <button @click="handleNavigation('dashboard')" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
                    Go to Dashboard
                </button>
            </div>
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
            selectedQuizSetId: null,
            debug: true
        }
    },
    methods: {
        handleNavigation(route) {
            console.log('🔄 App.vue: handleNavigation called with route:', route);
            this.currentView = route;
        },
        
        selectQuizSet(quizSetId) {
            console.log('🔄 App.vue: selectQuizSet called with id:', quizSetId);
            this.selectedQuizSetId = quizSetId;
            this.currentView = 'quiz-page';
        }
    },
    mounted() {
        console.log('🚀 App.vue mounted successfully');
        console.log('📍 Current view:', this.currentView);
        console.log('🔧 Registered components:', Object.keys(this.$options.components));
    }
}
</script>