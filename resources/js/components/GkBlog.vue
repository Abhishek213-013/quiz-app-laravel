<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-6 sm:py-8">
            <!-- Header -->
            <div class="text-center mb-8 sm:mb-12">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-3 sm:mb-4">GK Blog</h1>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto px-4">
                    Enhance your knowledge with our collection of General Knowledge articles, 
                    facts, and interesting information to boost your quiz performance.
                </p>
            </div>

            <!-- Featured Articles -->
            <div class="max-w-6xl mx-auto mb-8 sm:mb-12">
                <h2 class="text-xl sm:text-2xl font-bold text-center text-gray-800 mb-4 sm:mb-6 md:mb-8">Featured Articles</h2>
                
                <div class="featured-articles-container">
                    <div class="featured-article-card">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <span class="text-white text-lg sm:text-xl md:text-2xl">🌎</span>
                        </div>
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-2 sm:mb-3">World Geography Facts</h3>
                        <p class="text-gray-600 mb-3 sm:mb-4 text-xs sm:text-sm md:text-base leading-relaxed">
                            Discover fascinating facts about countries, capitals, and geographical wonders from around the world.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto text-xs sm:text-sm md:text-base">
                            Read More
                            <span class="ml-1 sm:ml-2">→</span>
                        </button>
                    </div>

                    <div class="featured-article-card">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <span class="text-white text-lg sm:text-xl md:text-2xl">🧪</span>
                        </div>
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-2 sm:mb-3">Science & Technology</h3>
                        <p class="text-gray-600 mb-3 sm:mb-4 text-xs sm:text-sm md:text-base leading-relaxed">
                            Latest scientific discoveries and technological advancements that are shaping our world.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto text-xs sm:text-sm md:text-base">
                            Read More
                            <span class="ml-1 sm:ml-2">→</span>
                        </button>
                    </div>

                    <div class="featured-article-card">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gradient-to-r from-red-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <span class="text-white text-lg sm:text-xl md:text-2xl">📜</span>
                        </div>
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-2 sm:mb-3">Historical Events</h3>
                        <p class="text-gray-600 mb-3 sm:mb-4 text-xs sm:text-sm md:text-base leading-relaxed">
                            Important historical events, dates, and figures that every quiz enthusiast should know.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto text-xs sm:text-sm md:text-base">
                            Read More
                            <span class="ml-1 sm:ml-2">→</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Blog Categories -->
            <div class="mt-12 sm:mt-16 max-w-6xl mx-auto">
                <h2 class="text-xl sm:text-2xl font-bold text-center text-gray-800 mb-6 sm:mb-8">Blog Categories</h2>
                
                <div class="categories-container">
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="category-card"
                    >
                        <div :class="['category-icon', category.bgColor]">
                            <span class="text-white text-sm sm:text-base">{{ category.icon }}</span>
                        </div>
                        <h3 class="category-name">{{ category.name }}</h3>
                        <p class="category-count">{{ category.count }} articles</p>
                    </div>
                </div>
            </div>

            <!-- Latest Articles -->
            <div class="mt-12 sm:mt-16 max-w-6xl mx-auto">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3 sm:gap-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Latest Articles</h2>
                    <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center text-sm sm:text-base">
                        View All Articles
                        <span class="ml-1 sm:ml-2">→</span>
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                    <div class="space-y-4 sm:space-y-6">
                        <article 
                            v-for="article in latestArticles" 
                            :key="article.id"
                            class="border-b border-gray-200 pb-4 sm:pb-6 last:border-b-0 hover:bg-gray-50 rounded-lg p-3 sm:p-4 transition-colors"
                        >
                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <div :class="['w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center flex-shrink-0', article.bgColor]">
                                    <span class="text-white text-xs sm:text-sm">{{ article.icon }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-1 sm:mb-2 line-clamp-2">{{ article.title }}</h3>
                                    <p class="text-gray-600 mb-2 sm:mb-3 text-sm sm:text-base line-clamp-2">{{ article.excerpt }}</p>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0">
                                        <div class="flex items-center space-x-3 sm:space-x-4 text-xs sm:text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <span class="mr-1">📅</span>
                                                {{ article.date }}
                                            </span>
                                            <span class="flex items-center">
                                                <span class="mr-1">⏱️</span>
                                                {{ article.readTime }}
                                            </span>
                                        </div>
                                        <button class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium self-start sm:self-auto">
                                            Read Full Article
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <!-- Blog Overview -->
            <div class="mt-12 sm:mt-16 max-w-6xl mx-auto">
                <h2 class="text-xl sm:text-2xl font-bold text-center text-gray-800 mb-6 sm:mb-8">Blog Overview</h2>
                <div class="stats-container">
                    <div class="stat-card stat-card-blue">
                        <div class="stat-number">{{ totalArticles }}</div>
                        <div class="stat-label">Total Articles</div>
                    </div>
                    <div class="stat-card stat-card-green">
                        <div class="stat-number">{{ totalCategories }}</div>
                        <div class="stat-label">Categories</div>
                    </div>
                    <div class="stat-card stat-card-purple">
                        <div class="stat-number">{{ monthlyReaders }}</div>
                        <div class="stat-label">Monthly Readers</div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="mt-12 sm:mt-16 max-w-4xl mx-auto">
                <div class="bg-gradient-to-r from-purple-500 to-blue-600 rounded-lg shadow-lg p-6 sm:p-8 text-center text-white">
                    <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">Stay Updated with GK Facts</h2>
                    <p class="mb-4 sm:mb-6 max-w-2xl mx-auto text-sm sm:text-base px-2">
                        Subscribe to our newsletter and receive daily general knowledge facts, 
                        quiz tips, and educational content directly in your inbox.
                    </p>
                    <div class="max-w-md mx-auto flex flex-col sm:flex-row gap-2 sm:gap-0">
                        <input 
                            type="email" 
                            placeholder="Enter your email address"
                            class="flex-1 px-4 py-3 rounded-lg sm:rounded-l-lg sm:rounded-r-none text-gray-900 focus:outline-none text-sm sm:text-base"
                        >
                        <button class="bg-orange-500 hover:bg-orange-600 px-4 sm:px-6 py-3 rounded-lg sm:rounded-r-lg sm:rounded-l-none font-semibold transition-colors text-sm sm:text-base flex items-center justify-center gap-2">
                            <span>📧</span> Subscribe
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
                    icon: '🌎', 
                    count: 24,
                    bgColor: 'bg-blue-500'
                },
                { 
                    id: 2, 
                    name: 'Science', 
                    icon: '🧪', 
                    count: 18,
                    bgColor: 'bg-green-500'
                },
                { 
                    id: 3, 
                    name: 'History', 
                    icon: '📜', 
                    count: 32,
                    bgColor: 'bg-purple-500'
                },
                { 
                    id: 4, 
                    name: 'Sports', 
                    icon: '⚽', 
                    count: 15,
                    bgColor: 'bg-red-500'
                },
                { 
                    id: 5, 
                    name: 'Arts', 
                    icon: '🎨', 
                    count: 12,
                    bgColor: 'bg-pink-500'
                },
                { 
                    id: 6, 
                    name: 'Technology', 
                    icon: '💻', 
                    count: 21,
                    bgColor: 'bg-indigo-500'
                },
                { 
                    id: 7, 
                    name: 'Politics', 
                    icon: '🏛️', 
                    count: 16,
                    bgColor: 'bg-yellow-500'
                },
                { 
                    id: 8, 
                    name: 'Culture', 
                    icon: '🗿', 
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
                    icon: '🗿',
                    bgColor: 'bg-blue-500'
                },
                {
                    id: 2,
                    title: 'Understanding Climate Change: Facts and Impacts',
                    excerpt: 'Learn about the science behind climate change, its global impacts, and what we can do to mitigate its effects.',
                    date: 'Oct 12, 2024',
                    readTime: '7 min read',
                    icon: '🌡️',
                    bgColor: 'bg-green-500'
                },
                {
                    id: 3,
                    title: 'Famous Inventors and Their Revolutionary Creations',
                    excerpt: 'Discover the stories behind famous inventors and the groundbreaking inventions that changed the course of history.',
                    date: 'Oct 8, 2024',
                    readTime: '6 min read',
                    icon: '💡',
                    bgColor: 'bg-purple-500'
                }
            ]
        }
    }
}
</script>

<style scoped>
/* Text truncation utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Featured Articles Layout */
.featured-articles-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.featured-article-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    padding: 1.25rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.featured-article-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

.featured-article-card:hover:first-child {
    border-color: #93c5fd;
}

.featured-article-card:hover:nth-child(2) {
    border-color: #86efac;
}

.featured-article-card:hover:last-child {
    border-color: #fca5a5;
}

/* Blog Categories Layout */
.categories-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.category-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

.category-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem auto;
}

.category-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.category-count {
    color: #6b7280;
    font-size: 0.75rem;
}

/* Blog Overview Stats Layout */
.stats-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-card {
    border-radius: 0.75rem;
    padding: 1.25rem;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

.stat-card-blue {
    background-color: #dbeafe;
    border-left: 4px solid #3b82f6;
}

.stat-card-green {
    background-color: #dcfce7;
    border-left: 4px solid #22c55e;
}

.stat-card-purple {
    background-color: #f3e8ff;
    border-left: 4px solid #a855f7;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-card-blue .stat-number {
    color: #1d4ed8;
}

.stat-card-green .stat-number {
    color: #15803d;
}

.stat-card-purple .stat-number {
    color: #7e22ce;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Tablet Styles */
@media (min-width: 640px) {
    .featured-articles-container {
        gap: 1.25rem;
    }
    
    .featured-article-card {
        padding: 1.5rem;
    }
    
    .categories-container {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    
    .category-card {
        padding: 1.25rem;
    }
    
    .category-icon {
        width: 3rem;
        height: 3rem;
        margin-bottom: 0.75rem;
    }
    
    .category-name {
        font-size: 1rem;
    }
    
    .category-count {
        font-size: 0.875rem;
    }
    
    .stats-container {
        flex-direction: row;
        gap: 1.25rem;
    }
    
    .stat-card {
        padding: 1.5rem;
    }
    
    .stat-number {
        font-size: 1.875rem;
    }
    
    .stat-label {
        font-size: 1rem;
    }
}

/* Desktop Styles */
@media (min-width: 768px) {
    .featured-articles-container {
        flex-direction: row;
        gap: 1.5rem;
    }
    
    .featured-article-card {
        flex: 1;
        padding: 1.75rem;
    }
    
    .categories-container {
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }
    
    .category-card {
        padding: 1.5rem;
    }
    
    .category-icon {
        width: 3.5rem;
        height: 3.5rem;
        margin-bottom: 1rem;
    }
    
    .category-name {
        font-size: 1.125rem;
    }
    
    .stat-number {
        font-size: 2.25rem;
    }
}

/* Large Desktop Styles */
@media (min-width: 1024px) {
    .categories-container {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
}

/* Small Mobile Optimization */
@media (max-width: 380px) {
    .categories-container {
        grid-template-columns: 1fr;
    }
    
    .featured-article-card {
        padding: 1rem;
    }
    
    .category-card {
        padding: 0.875rem;
    }
}

/* Custom styles for smooth transitions */
.transform {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}
</style>