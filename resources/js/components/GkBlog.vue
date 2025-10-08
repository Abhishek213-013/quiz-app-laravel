<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">GK Blog</h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Enhance your knowledge with our collection of General Knowledge articles, 
                    facts, and interesting information to boost your quiz performance.
                </p>
            </div>

            <!-- Featured Articles -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Featured Article 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-globe-americas text-white text-6xl"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">World Geography Facts</h3>
                        <p class="text-gray-600 mb-4">
                            Discover fascinating facts about countries, capitals, and geographical wonders 
                            from around the world.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                            Read More
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Featured Article 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-48 bg-gradient-to-r from-green-500 to-teal-600 flex items-center justify-center">
                        <i class="fas fa-flask text-white text-6xl"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Science & Technology</h3>
                        <p class="text-gray-600 mb-4">
                            Latest scientific discoveries and technological advancements that are 
                            shaping our world.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                            Read More
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Featured Article 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-48 bg-gradient-to-r from-red-500 to-orange-600 flex items-center justify-center">
                        <i class="fas fa-history text-white text-6xl"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Historical Events</h3>
                        <p class="text-gray-600 mb-4">
                            Important historical events, dates, and figures that every quiz enthusiast 
                            should know.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                            Read More
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Blog Categories -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Blog Categories</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="bg-gray-50 rounded-lg p-4 text-center hover:bg-gray-100 transition-colors cursor-pointer"
                    >
                        <i :class="[category.icon, 'text-2xl text-blue-600 mb-2']"></i>
                        <h3 class="font-semibold text-gray-900">{{ category.name }}</h3>
                        <p class="text-sm text-gray-600">{{ category.count }} articles</p>
                    </div>
                </div>
            </div>

            <!-- Latest Articles -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Latest Articles</h2>
                    <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                        View All Articles
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>

                <div class="space-y-6">
                    <article 
                        v-for="article in latestArticles" 
                        :key="article.id"
                        class="border-b border-gray-200 pb-6 last:border-b-0"
                    >
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i :class="[article.icon, 'text-blue-600 text-xl']"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ article.title }}</h3>
                                <p class="text-gray-600 mb-3">{{ article.excerpt }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ article.date }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ article.readTime }}
                                        </span>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Read Full Article
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg p-8 mt-12 text-center text-white">
                <h2 class="text-2xl font-bold mb-4">Stay Updated with GK Facts</h2>
                <p class="mb-6 max-w-2xl mx-auto">
                    Subscribe to our newsletter and receive daily general knowledge facts, 
                    quiz tips, and educational content directly in your inbox.
                </p>
                <div class="max-w-md mx-auto flex">
                    <input 
                        type="email" 
                        placeholder="Enter your email address"
                        class="flex-1 px-4 py-3 rounded-l-lg text-gray-900 focus:outline-none"
                    >
                    <button class="bg-orange-500 hover:bg-orange-600 px-6 py-3 rounded-r-lg font-semibold transition-colors">
                        Subscribe
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'GkBlog',
    data() {
        return {
            categories: [
                { id: 1, name: 'Geography', icon: 'fas fa-globe-americas', count: 24 },
                { id: 2, name: 'Science', icon: 'fas fa-flask', count: 18 },
                { id: 3, name: 'History', icon: 'fas fa-history', count: 32 },
                { id: 4, name: 'Sports', icon: 'fas fa-running', count: 15 },
                { id: 5, name: 'Arts', icon: 'fas fa-palette', count: 12 },
                { id: 6, name: 'Technology', icon: 'fas fa-laptop-code', count: 21 },
                { id: 7, name: 'Politics', icon: 'fas fa-landmark', count: 16 },
                { id: 8, name: 'Culture', icon: 'fas fa-monument', count: 14 }
            ],
            latestArticles: [
                {
                    id: 1,
                    title: 'The Seven Wonders of the Modern World',
                    excerpt: 'Explore the architectural marvels that have been designated as the New Seven Wonders of the World, from the Great Wall of China to Chichen Itza.',
                    date: 'Oct 15, 2024',
                    readTime: '5 min read',
                    icon: 'fas fa-monument'
                },
                {
                    id: 2,
                    title: 'Understanding Climate Change: Facts and Impacts',
                    excerpt: 'Learn about the science behind climate change, its global impacts, and what we can do to mitigate its effects.',
                    date: 'Oct 12, 2024',
                    readTime: '7 min read',
                    icon: 'fas fa-temperature-high'
                },
                {
                    id: 3,
                    title: 'Famous Inventors and Their Revolutionary Creations',
                    excerpt: 'Discover the stories behind famous inventors and the groundbreaking inventions that changed the course of history.',
                    date: 'Oct 8, 2024',
                    readTime: '6 min read',
                    icon: 'fas fa-lightbulb'
                }
            ]
        }
    },
    methods: {
        // Methods for blog functionality can be added here
        // For example: fetch articles from API, handle subscriptions, etc.
    }
}
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>