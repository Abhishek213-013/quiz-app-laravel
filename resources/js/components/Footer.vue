<template>
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <img 
                        src="/images/logo.png" 
                        alt="Quiz App Logo"
                        class="h-6 w-6 mr-2"
                    />
                    <span class="text-lg font-semibold">MindSpark</span>
                </div>
                <p class="text-gray-300 text-sm">
                    &copy; {{ currentYear }} MindSpark. Test your knowledge, challenge your mind.
                </p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors">Privacy</a>
                    <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors">Terms</a>
                    <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors">Contact</a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    name: 'Footer',
    computed: {
        currentYear() {
            return new Date().getFullYear();
        }
    }
}
</script>