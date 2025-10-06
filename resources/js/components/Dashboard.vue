<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Quiz App Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <!-- Take Quiz Tile -->
            <div 
                class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl"
                @click="$emit('navigate', 'quiz-sets')"
            >
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Take Quiz</h3>
                <p class="text-gray-600">Start a new quiz and test your knowledge</p>
            </div>

            <!-- Manage Quizzes Tile -->
            <div 
                class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl"
                @click="openManageQuizzes"
            >
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Manage Quizzes</h3>
                <p class="text-gray-600">Add, edit or delete quiz questions</p>
            </div>

            <!-- Previous Records Tile -->
            <div 
                class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl"
                @click="$emit('navigate', 'records')"
            >
                <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Previous Records</h3>
                <p class="text-gray-600">View and filter previous quiz results</p>
            </div>
        </div>

        <!-- Secret Key Modal -->
        <div v-if="showSecretModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">Enter Secret Key</h3>
                <input 
                    v-model="secretKey" 
                    type="password" 
                    placeholder="Enter secret key"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @keyup.enter="verifySecretKey"
                />
                <div class="flex justify-end space-x-3 mt-4">
                    <button 
                        @click="showSecretModal = false; secretKey = ''"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="verifySecretKey"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                    >
                        Verify
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Dashboard',
    data() {
        return {
            showSecretModal: false,
            secretKey: ''
        }
    },
    methods: {
        openManageQuizzes() {
            this.showSecretModal = true;
        },

        async verifySecretKey() {
            try {
                const response = await fetch('/api/admin/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        secret_key: this.secretKey
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showSecretModal = false;
                    this.secretKey = '';
                    this.$emit('navigate', 'manage-quizzes');
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
</script>