<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">GK Blog</h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Enhance your knowledge with our collection of General Knowledge articles, 
                    facts, and interesting information to boost your quiz performance.
                </p>
            </div>

            <!-- Featured Articles - Using similar flex layout as Dashboard -->
            <div class="flex flex-col md:flex-row gap-8 max-w-6xl mx-auto mb-12">
                <!-- Featured Article 1 -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl flex-1">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-globe-americas text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">World Geography Facts</h3>
                    <p class="text-gray-600 mb-4">
                        Discover fascinating facts about countries, capitals, and geographical wonders from around the world.
                    </p>
                    <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto">
                        Read More
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>

                <!-- Featured Article 2 -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl flex-1">
                    <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-flask text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Science & Technology</h3>
                    <p class="text-gray-600 mb-4">
                        Latest scientific discoveries and technological advancements that are shaping our world.
                    </p>
                    <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto">
                        Read More
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>

                <!-- Featured Article 3 -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl flex-1">
                    <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Historical Events</h3>
                    <p class="text-gray-600 mb-4">
                        Important historical events, dates, and figures that every quiz enthusiast should know.
                    </p>
                    <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto">
                        Read More
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Blog Categories - Using similar grid layout as Dashboard stats -->
            <div class="mt-16 max-w-6xl mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Blog Categories</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="bg-white rounded-lg shadow-lg p-6 text-center cursor-pointer transform transition-transform hover:scale-105 hover:shadow-xl"
                    >
                        <div :class="['w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4', category.bgColor]">
                            <i :class="[category.icon, 'text-white text-xl']"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ category.name }}</h3>
                        <p class="text-gray-600">{{ category.count }} articles</p>
                    </div>
                </div>
            </div>

            <!-- Latest Articles -->
            <div class="mt-16 max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-center text-gray-800">Latest Articles</h2>
                    <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                        View All Articles
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="space-y-6">
                        <article 
                            v-for="article in latestArticles" 
                            :key="article.id"
                            class="border-b border-gray-200 pb-6 last:border-b-0 hover:bg-gray-50 rounded-lg p-4 transition-colors"
                        >
                            <div class="flex items-start space-x-4">
                                <div :class="['w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0', article.bgColor]">
                                    <i :class="[article.icon, 'text-white text-sm']"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ article.title }}</h3>
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
            </div>

            <!-- Quick Stats - Similar to Dashboard -->
            <div class="mt-16 max-w-6xl mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Blog Overview</h2>
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="bg-blue-50 rounded-lg p-6 text-center flex-1">
                        <div class="text-3xl font-bold text-blue-600 mb-2">{{ totalArticles }}</div>
                        <div class="text-gray-600">Total Articles</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-6 text-center flex-1">
                        <div class="text-3xl font-bold text-green-600 mb-2">{{ totalCategories }}</div>
                        <div class="text-gray-600">Categories</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-6 text-center flex-1">
                        <div class="text-3xl font-bold text-purple-600 mb-2">{{ monthlyReaders }}</div>
                        <div class="text-gray-600">Monthly Readers</div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="mt-16 max-w-4xl mx-auto">
                <div class="bg-gradient-to-r from-purple-500 to-blue-600 rounded-lg shadow-lg p-8 text-center text-white">
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
    </div>
</template>

<script>
export default {
    name: 'GkBlog',
    data() {
        return {
            totalArticles: 156,
            totalCategories: 8,
            monthlyReaders: '2.4K',
            categories: [
                { 
                    id: 1, 
                    name: 'Geography', 
                    icon: 'fas fa-globe-americas', 
                    count: 24,
                    bgColor: 'bg-blue-500'
                },
                { 
                    id: 2, 
                    name: 'Science', 
                    icon: 'fas fa-flask', 
                    count: 18,
                    bgColor: 'bg-green-500'
                },
                { 
                    id: 3, 
                    name: 'History', 
                    icon: 'fas fa-history', 
                    count: 32,
                    bgColor: 'bg-purple-500'
                },
                { 
                    id: 4, 
                    name: 'Sports', 
                    icon: 'fas fa-running', 
                    count: 15,
                    bgColor: 'bg-red-500'
                },
                { 
                    id: 5, 
                    name: 'Arts', 
                    icon: 'fas fa-palette', 
                    count: 12,
                    bgColor: 'bg-pink-500'
                },
                { 
                    id: 6, 
                    name: 'Technology', 
                    icon: 'fas fa-laptop-code', 
                    count: 21,
                    bgColor: 'bg-indigo-500'
                },
                { 
                    id: 7, 
                    name: 'Politics', 
                    icon: 'fas fa-landmark', 
                    count: 16,
                    bgColor: 'bg-yellow-500'
                },
                { 
                    id: 8, 
                    name: 'Culture', 
                    icon: 'fas fa-monument', 
                    count: 14,
                    bgColor: 'bg-orange-500'
                }
            ],
            latestArticles: [
                {
                    id: 1,
                    title: 'The Seven Wonders of the Modern World',
                    excerpt: 'Explore the architectural marvels that have been designated as the New Seven Wonders of the World, from the Great Wall of China to Chichen Itza.',
                    date: 'Oct 15, 2024',
                    readTime: '5 min read',
                    icon: 'fas fa-monument',
                    bgColor: 'bg-blue-500'
                },
                {
                    id: 2,
                    title: 'Understanding Climate Change: Facts and Impacts',
                    excerpt: 'Learn about the science behind climate change, its global impacts, and what we can do to mitigate its effects.',
                    date: 'Oct 12, 2024',
                    readTime: '7 min read',
                    icon: 'fas fa-temperature-high',
                    bgColor: 'bg-green-500'
                },
                {
                    id: 3,
                    title: 'Famous Inventors and Their Revolutionary Creations',
                    excerpt: 'Discover the stories behind famous inventors and the groundbreaking inventions that changed the course of history.',
                    date: 'Oct 8, 2024',
                    readTime: '6 min read',
                    icon: 'fas fa-lightbulb',
                    bgColor: 'bg-purple-500'
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
/* Custom styles for smooth transitions - matching Dashboard */
.transform {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}

.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>