<template>
    <div class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">GK Blog</h1>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    Enhance your knowledge with our collection of General Knowledge articles, 
                    facts, and interesting information to boost your quiz performance.
                </p>
            </div>

            <!-- Featured Articles - Using CSS media queries -->
            <div class="max-w-6xl mx-auto mb-12">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6 sm:mb-8">Featured Articles</h2>
                
                <div class="featured-articles-container">
                    <div class="featured-article-card">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white text-xl md:text-2xl">🌎</span>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-3">World Geography Facts</h3>
                        <p class="text-gray-600 mb-4 text-sm md:text-base">
                            Discover fascinating facts about countries, capitals, and geographical wonders from around the world.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto text-sm md:text-base">
                            Read More
                            <span class="ml-1 md:ml-2">→</span>
                        </button>
                    </div>

                    <div class="featured-article-card">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white text-xl md:text-2xl">🧪</span>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-3">Science & Technology</h3>
                        <p class="text-gray-600 mb-4 text-sm md:text-base">
                            Latest scientific discoveries and technological advancements that are shaping our world.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto text-sm md:text-base">
                            Read More
                            <span class="ml-1 md:ml-2">→</span>
                        </button>
                    </div>

                    <div class="featured-article-card">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-r from-red-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white text-xl md:text-2xl">📜</span>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-3">Historical Events</h3>
                        <p class="text-gray-600 mb-4 text-sm md:text-base">
                            Important historical events, dates, and figures that every quiz enthusiast should know.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto text-sm md:text-base">
                            Read More
                            <span class="ml-1 md:ml-2">→</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Blog Categories - Using CSS media queries -->
            <div class="mt-16 max-w-6xl mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Blog Categories</h2>
                
                <div class="categories-container">
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="category-card"
                    >
                        <div :class="['category-icon', category.bgColor]">
                            <span class="text-white">{{ category.icon }}</span>
                        </div>
                        <h3 class="category-name">{{ category.name }}</h3>
                        <p class="category-count">{{ category.count }} articles</p>
                    </div>
                </div>
            </div>

            <!-- Latest Articles -->
            <div class="mt-16 max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Latest Articles</h2>
                    <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                        View All Articles
                        <span class="ml-2">→</span>
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
                                    <span class="text-white text-sm">{{ article.icon }}</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ article.title }}</h3>
                                    <p class="text-gray-600 mb-3">{{ article.excerpt }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <span class="mr-1">📅</span>
                                                {{ article.date }}
                                            </span>
                                            <span class="flex items-center">
                                                <span class="mr-1">⏱️</span>
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

            <!-- Blog Overview - Using CSS media queries -->
            <div class="mt-16 max-w-6xl mx-auto">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Blog Overview</h2>
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
/* Featured Articles Layout */
.featured-articles-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.featured-article-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.featured-article-card:hover {
    transform: scale(1.05);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
    grid-template-columns: 1fr;
    gap: 1rem;
}

.category-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.category-card:hover {
    transform: scale(1.05);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.category-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem auto;
}

.category-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.category-count {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Blog Overview Stats Layout */
.stats-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-card {
    border-radius: 0.5rem;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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
    font-size: 1.875rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
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
    font-size: 1rem;
}

/* Desktop Styles */
@media (min-width: 768px) {
    /* Featured Articles */
    .featured-articles-container {
        flex-direction: row;
        gap: 1.5rem;
    }
    
    .featured-article-card {
        flex: 1;
    }
    
    /* Blog Categories */
    .categories-container {
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }
    
    .category-card {
        padding: 1.5rem;
    }
    
    .category-icon {
        width: 4rem;
        height: 4rem;
        margin-bottom: 1rem;
    }
    
    .category-name {
        font-size: 1.125rem;
    }
    
    .category-count {
        font-size: 1rem;
    }
    
    /* Blog Overview Stats */
    .stats-container {
        flex-direction: row;
        gap: 1.5rem;
    }
    
    .stat-card {
        flex: 1;
        padding: 1.5rem;
    }
    
    .stat-number {
        font-size: 2.25rem;
    }
    
    .stat-label {
        font-size: 1.125rem;
    }
}

/* Custom styles for smooth transitions */
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