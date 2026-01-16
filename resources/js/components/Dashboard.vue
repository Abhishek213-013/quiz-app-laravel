<template>
    <div class="flex-1 overflow-hidden relative" :class="themeClass">
        <!-- Animated Background Elements -->
        <div class="animated-bg-elements">
            <div class="floating-particle" v-for="(particle, index) in particles" :key="index"
                 :style="particle.style"></div>
            <div class="circuit-board-bg"></div>
        </div>

        <!-- Easter Egg Trigger -->
        <div class="easter-egg-trigger" @click="toggleNerdMode"
             @mouseenter="showEasterTooltip = true"
             @mouseleave="showEasterTooltip = false"
             :class="{ 'nerd-mode-active': nerdMode }">
            <span class="text-2xl">🧠</span>
            <div v-if="showEasterTooltip && !nerdMode" class="easter-tooltip">
                Click for NERD MODE
            </div>
        </div>

        <div class="container mx-auto px-4 py-6 sm:py-8 relative z-10">
            <!-- Konami Code Progress -->
            <div v-if="konamiProgress > 0" class="konami-hint">
                Konami Code: {{ konamiSequence.slice(0, konamiProgress).join(' ') }}
            </div>

            <!-- Header -->
            <div class="text-center mb-8 sm:mb-12 animate-fade-in">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-3 sm:mb-4 animate-slide-up">
                    <span class="text-gradient animate-gradient-shift">GK Blog</span>
                    <span class="sparkle">✨</span>
                </h1>
                <p class="text-base sm:text-lg md:text-xl max-w-2xl mx-auto px-4 blog-description animate-slide-up" 
                   :style="{ animationDelay: '0.1s' }">
                    Enhance your knowledge with our collection of General Knowledge articles, 
                    facts, and interesting information to boost your quiz performance.
                </p>
            </div>

            <!-- Featured Articles -->
            <div class="max-w-6xl mx-auto mb-8 sm:mb-12 animate-slide-up" :style="{ animationDelay: '0.2s' }">
                <h2 class="text-xl sm:text-2xl font-bold text-center mb-4 sm:mb-6 md:mb-8 section-title">
                    Featured Articles
                </h2>
                
                <div class="featured-articles-container">
                    <div 
                        class="featured-article-card interactive-card cursor-pointer transform transition-all duration-300 hover:scale-105"
                        @click="navigateToArticle('world-geography')"
                        @mouseenter="playHoverSound('click')"
                    >
                        <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-pulse-slow">
                            <span class="text-white text-lg sm:text-xl md:text-2xl floating-icon">🌎</span>
                        </div>
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold mb-2 sm:mb-3 tile-title">World Geography Facts</h3>
                        <p class="mb-3 sm:mb-4 text-xs sm:text-sm md:text-base leading-relaxed tile-description">
                            Discover fascinating facts about countries, capitals, and geographical wonders from around the world.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center justify-center mx-auto text-xs sm:text-sm md:text-base interactive-button">
                            Read More
                            <span class="ml-1 sm:ml-2">→</span>
                        </button>
                        <div class="tile-glow"></div>
                    </div>

                    <div 
                        class="featured-article-card interactive-card cursor-pointer transform transition-all duration-300 hover:scale-105"
                        @click="navigateToArticle('science-tech')"
                        @mouseenter="playHoverSound('click')"
                    >
                        <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-pulse-slow">
                            <span class="text-white text-lg sm:text-xl md:text-2xl floating-icon">🧪</span>
                        </div>
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold mb-2 sm:mb-3 tile-title">Science & Technology</h3>
                        <p class="mb-3 sm:mb-4 text-xs sm:text-sm md:text-base leading-relaxed tile-description">
                            Latest scientific discoveries and technological advancements that are shaping our world.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center justify-center mx-auto text-xs sm:text-sm md:text-base interactive-button">
                            Read More
                            <span class="ml-1 sm:ml-2">→</span>
                        </button>
                        <div class="tile-glow"></div>
                    </div>

                    <div 
                        class="featured-article-card interactive-card cursor-pointer transform transition-all duration-300 hover:scale-105"
                        @click="navigateToArticle('historical-events')"
                        @mouseenter="playHoverSound('click')"
                    >
                        <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gradient-to-r from-red-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 animate-pulse-slow">
                            <span class="text-white text-lg sm:text-xl md:text-2xl floating-icon">📜</span>
                        </div>
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold mb-2 sm:mb-3 tile-title">Historical Events</h3>
                        <p class="mb-3 sm:mb-4 text-xs sm:text-sm md:text-base leading-relaxed tile-description">
                            Important historical events, dates, and figures that every quiz enthusiast should know.
                        </p>
                        <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center justify-center mx-auto text-xs sm:text-sm md:text-base interactive-button">
                            Read More
                            <span class="ml-1 sm:ml-2">→</span>
                        </button>
                        <div class="tile-glow"></div>
                    </div>
                </div>
            </div>

            <!-- Blog Categories -->
            <div class="mt-12 sm:mt-16 max-w-6xl mx-auto animate-slide-up" :style="{ animationDelay: '0.3s' }">
                <h2 class="text-xl sm:text-2xl font-bold text-center mb-6 sm:mb-8 section-title">Blog Categories</h2>
                
                <div class="categories-container">
                    <div 
                        v-for="category in categories" 
                        :key="category.id"
                        class="category-card interactive-card cursor-pointer transform transition-all duration-300 hover:scale-105"
                        @click="filterByCategory(category.id)"
                        @mouseenter="playHoverSound('notification')"
                    >
                        <div :class="['category-icon', category.bgColor, 'animate-bounce-slow']">
                            <span class="text-white text-sm sm:text-base">{{ category.icon }}</span>
                        </div>
                        <h3 class="category-name">{{ category.name }}</h3>
                        <p class="category-count">{{ category.count }} articles</p>
                        <div class="category-glow"></div>
                    </div>
                </div>
            </div>

            <!-- Latest Articles -->
            <div class="mt-12 sm:mt-16 max-w-6xl mx-auto animate-slide-up" :style="{ animationDelay: '0.4s' }">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3 sm:gap-0">
                    <h2 class="text-xl sm:text-2xl font-bold section-title">Latest Articles</h2>
                    <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center text-sm sm:text-base interactive-button">
                        View All Articles
                        <span class="ml-1 sm:ml-2">→</span>
                    </button>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6 activity-container">
                    <div class="space-y-4 sm:space-y-6">
                        <article 
                            v-for="(article, index) in latestArticles" 
                            :key="article.id"
                            class="border-b border-gray-200 dark:border-gray-700 pb-4 sm:pb-6 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-750 rounded-lg p-3 sm:p-4 transition-colors interactive-card cursor-pointer"
                            @click="readArticle(article.id)"
                            @mouseenter="playHoverSound('page')"
                            :style="{ animationDelay: `${index * 0.1}s` }"
                        >
                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <div :class="['w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center flex-shrink-0', article.bgColor, 'animate-pulse-slow']">
                                    <span class="text-white text-xs sm:text-sm">{{ article.icon }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base sm:text-lg font-semibold mb-1 sm:mb-2 line-clamp-2 tile-title">{{ article.title }}</h3>
                                    <p class="mb-2 sm:mb-3 text-sm sm:text-base line-clamp-2 tile-description">{{ article.excerpt }}</p>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0">
                                        <div class="flex items-center space-x-3 sm:space-x-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <span class="mr-1">📅</span>
                                                {{ article.date }}
                                            </span>
                                            <span class="flex items-center">
                                                <span class="mr-1">⏱️</span>
                                                {{ article.readTime }}
                                            </span>
                                        </div>
                                        <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs sm:text-sm font-medium self-start sm:self-auto interactive-button">
                                            Read Full Article
                                        </button>
                                    </div>
                                </div>
                                <div class="activity-indicator animate-ping-slow"></div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <!-- Blog Overview -->
            <div class="mt-12 sm:mt-16 max-w-6xl mx-auto animate-slide-up" :style="{ animationDelay: '0.5s' }">
                <h2 class="text-xl sm:text-2xl font-bold text-center mb-6 sm:mb-8 section-title">Blog Overview</h2>
                <div class="stats-container">
                    <div class="stat-card stat-card-blue interactive-card transform transition-all duration-300 hover:scale-105">
                        <div class="stat-number cyber-glow-blue animate-count-up">{{ animatedTotalArticles }}</div>
                        <div class="stat-label">Total Articles</div>
                        <div class="stat-sparkle"></div>
                    </div>
                    <div class="stat-card stat-card-green interactive-card transform transition-all duration-300 hover:scale-105">
                        <div class="stat-number cyber-glow-green animate-count-up">{{ totalCategories }}</div>
                        <div class="stat-label">Categories</div>
                        <div class="stat-sparkle"></div>
                    </div>
                    <div class="stat-card stat-card-purple interactive-card transform transition-all duration-300 hover:scale-105">
                        <div class="stat-number cyber-glow-purple animate-count-up">{{ animatedMonthlyReaders }}</div>
                        <div class="stat-label">Monthly Readers</div>
                        <div class="stat-sparkle"></div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <!-- <div class="mt-12 sm:mt-16 max-w-4xl mx-auto animate-slide-up" :style="{ animationDelay: '0.6s' }">
                <div class="bg-gradient-to-r from-purple-500 to-blue-600 rounded-lg shadow-lg p-6 sm:p-8 text-center text-white newsletter-container">
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
                        <button class="bg-orange-500 hover:bg-orange-600 px-4 sm:px-6 py-3 rounded-lg sm:rounded-r-lg sm:rounded-l-none font-semibold transition-colors text-sm sm:text-base flex items-center justify-center gap-2 interactive-button">
                            <span>📧</span> Subscribe
                        </button>
                    </div>
                </div>
            </div> -->

            <!-- Hidden Easter Egg Messages -->
            <transition name="slide-down">
                <div v-if="showHiddenMessage" class="hidden-message animate-tada">
                    <div class="message-content">
                        <span class="text-3xl mr-3">🎯</span>
                        <div>
                            <h3 class="font-bold">Achievement Unlocked!</h3>
                            <p class="text-sm opacity-75 mt-1">{{ randomFact }}</p>
                        </div>
                        <button @click="showHiddenMessage = false" class="ml-auto text-2xl hover:text-red-500 transition-colors">×</button>
                    </div>
                </div>
            </transition>

            <!-- Nerd Mode Terminal -->
            <transition name="terminal">
                <div v-if="nerdMode" class="nerd-terminal">
                    <div class="terminal-header">
                        <div class="terminal-dots">
                            <span class="dot red"></span>
                            <span class="dot yellow"></span>
                            <span class="dot green"></span>
                        </div>
                        <span class="terminal-title">gk_blog@nerd_mode:~</span>
                        <button @click="toggleNerdMode" class="terminal-close hover:text-white">×</button>
                    </div>
                    <div class="terminal-body">
                        <div class="terminal-line">
                            <span class="prompt">$</span> Welcome to <span class="text-cyan-400">GK Blog Nerd Mode</span> <span class="blink">_</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">$</span> System initialized at {{ new Date().toLocaleTimeString() }}
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Total Articles: <span class="text-green-400">{{ totalArticles }}</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Categories Active: <span class="text-blue-400">{{ totalCategories }}</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Monthly Readers: <span class="text-purple-400">{{ monthlyReaders }}</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">$</span> Latest Article: "{{ latestArticles[0].title }}"
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Particle Container for Effects -->
            <div class="particle-container" ref="particleContainer"></div>
        </div>

        <!-- Matrix Rain Effect (Easter Egg) -->
        <div class="matrix-rain" :class="{ active: nerdMode }"></div>
    </div>
</template>

<script>
export default {
    name: 'GkBlog',
    props: {
        theme: {
            type: String,
            default: 'light'
        }
    },
    data() {
        return {
            totalArticles: 156,
            totalCategories: 8,
            monthlyReaders: '2.4K',
            animatedTotalArticles: 0,
            animatedMonthlyReaders: 0,
            nerdMode: false,
            showEasterTooltip: false,
            showHiddenMessage: false,
            konamiProgress: 0,
            konamiSequence: ['↑', '↑', '↓', '↓', '←', '→', '←', '→', 'B', 'A'],
            randomFact: '',
            particles: [],
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
            ],
            funFacts: [
                "The human brain can store about 2.5 petabytes of information",
                "Honey never spoils - archaeologists have found pots of honey in ancient Egyptian tombs that are over 3,000 years old and still perfectly good to eat",
                "Octopuses have three hearts and blue blood",
                "A day on Venus is longer than a year on Venus",
                "Bananas are berries, but strawberries aren't",
                "The Eiffel Tower can be 15 cm taller during the summer",
                "Your brain uses about 20% of your body's energy",
                "There are more possible iterations of a game of chess than there are atoms in the observable universe"
            ]
        }
    },
    computed: {
        themeClass() {
            return `theme-${this.theme}`;
        }
    },
    async mounted() {
        this.animateNumbers();
        this.initParticles();
        this.initKonamiCode();
        this.initHiddenMessage();
        
        // Add nerd mode class to body when active
        if (this.nerdMode) {
            document.body.classList.add('nerd-mode-active');
        }
    },
    beforeDestroy() {
        document.body.classList.remove('nerd-mode-active');
    },
    methods: {
        animateNumbers() {
            const duration = 1500;
            const steps = 60;
            
            const animate = (target, currentValue, isString = false) => {
                const increment = target / steps;
                let step = 0;
                
                const timer = setInterval(() => {
                    step++;
                    if (isString) {
                        const numValue = parseFloat(target);
                        const current = Math.min(Math.floor(increment * step), numValue);
                        this[currentValue] = current.toFixed(1) + (target.includes('K') ? 'K' : '');
                    } else {
                        this[currentValue] = Math.min(Math.floor(increment * step), target);
                    }
                    
                    if (step >= steps) {
                        clearInterval(timer);
                    }
                }, duration / steps);
            };
            
            animate(this.totalArticles, 'animatedTotalArticles');
            animate(this.monthlyReaders, 'animatedMonthlyReaders', true);
        },
        
        toggleNerdMode() {
            this.nerdMode = !this.nerdMode;
            if (this.nerdMode) {
                document.body.classList.add('nerd-mode-active');
                this.createFireworks();
                this.showNotification("Nerd Mode Activated!", "success");
            } else {
                document.body.classList.remove('nerd-mode-active');
            }
        },
        
        navigateToArticle(articleSlug) {
            this.createClickEffect();
            setTimeout(() => {
                console.log(`Navigating to article: ${articleSlug}`);
                // In real app: this.$router.push(`/article/${articleSlug}`)
            }, 300);
        },
        
        filterByCategory(categoryId) {
            this.createClickEffect();
            const category = this.categories.find(c => c.id === categoryId);
            this.showNotification(`Filtering by ${category.name}`, "info");
        },
        
        readArticle(articleId) {
            this.createClickEffect();
            const article = this.latestArticles.find(a => a.id === articleId);
            this.showNotification(`Opening: ${article.title}`, "info");
        },
        
        playHoverSound(type) {
            console.log(`Playing ${type} sound`);
        },
        
        initKonamiCode() {
            const konamiMap = {
                'ArrowUp': '↑',
                'ArrowDown': '↓',
                'ArrowLeft': '←',
                'ArrowRight': '→',
                'KeyB': 'B',
                'KeyA': 'A'
            };
            
            let sequence = [];
            
            document.addEventListener('keydown', (e) => {
                const key = konamiMap[e.code];
                if (key) {
                    sequence.push(key);
                    
                    if (sequence.length > this.konamiSequence.length) {
                        sequence.shift();
                    }
                    
                    this.konamiProgress = 0;
                    for (let i = 0; i < sequence.length; i++) {
                        if (sequence[i] === this.konamiSequence[i]) {
                            this.konamiProgress = i + 1;
                        } else {
                            sequence = [];
                            this.konamiProgress = 0;
                            break;
                        }
                    }
                    
                    if (this.konamiProgress === this.konamiSequence.length) {
                        this.activateKonami();
                        sequence = [];
                        this.konamiProgress = 0;
                    }
                } else {
                    sequence = [];
                    this.konamiProgress = 0;
                }
            });
        },
        
        activateKonami() {
            this.nerdMode = true;
            this.showHiddenMessage = true;
            this.randomFact = this.funFacts[Math.floor(Math.random() * this.funFacts.length)];
            this.createFireworks();
            
            // Boost stats for fun
            this.totalArticles += 10;
            this.monthlyReaders = '3.2K';
            this.animateNumbers();
            
            this.showNotification("Konami Code Unlocked!", "achievement");
            
            setTimeout(() => {
                this.showHiddenMessage = false;
            }, 8000);
        },
        
        initParticles() {
            for (let i = 0; i < 15; i++) {
                this.particles.push({
                    style: {
                        left: `${Math.random() * 100}%`,
                        top: `${Math.random() * 100}%`,
                        animationDelay: `${Math.random() * 5}s`,
                        width: `${Math.random() * 8 + 4}px`,
                        height: `${Math.random() * 8 + 4}px`,
                        background: `hsl(${Math.random() * 360}, 70%, 60%)`
                    }
                });
            }
        },
        
        createFireworks() {
            for (let i = 0; i < 12; i++) {
                setTimeout(() => {
                    const firework = document.createElement('div');
                    firework.className = 'firework';
                    firework.style.cssText = `
                        left: ${Math.random() * 100}%;
                        top: ${Math.random() * 100}%;
                        --firework-color: hsl(${Math.random() * 360}, 100%, 60%);
                    `;
                    const container = this.$refs.particleContainer;
                    if (container) {
                        container.appendChild(firework);
                    }
                    
                    setTimeout(() => {
                        if (firework.parentNode) {
                            firework.parentNode.removeChild(firework);
                        }
                    }, 1000);
                }, i * 100);
            }
        },
        
        createClickEffect() {
            const container = this.$refs.particleContainer;
            if (!container) return;
            
            for (let i = 0; i < 8; i++) {
                const particle = document.createElement('div');
                particle.className = 'click-particle';
                particle.style.cssText = `
                    --tx: ${(Math.random() - 0.5) * 100}px;
                    --ty: ${(Math.random() - 0.5) * 100}px;
                    background: hsl(${Math.random() * 360}, 100%, 60%);
                `;
                container.appendChild(particle);
                
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.parentNode.removeChild(particle);
                    }
                }, 600);
            }
        },
        
        initHiddenMessage() {
            // Show random fact every 45 seconds
            setInterval(() => {
                if (!this.showHiddenMessage && Math.random() > 0.7) {
                    this.randomFact = this.funFacts[Math.floor(Math.random() * this.funFacts.length)];
                    this.showHiddenMessage = true;
                    
                    setTimeout(() => {
                        this.showHiddenMessage = false;
                    }, 5000);
                }
            }, 45000);
        },
        
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification-slide notification-${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }
    }
}
</script>

<style scoped>
/* Base theme variables with enhanced colors */
:root {
    --blog-bg: #ffffff;
    --blog-title-color: #1f2937;
    --blog-description-color: #4b5563;
    --tile-bg: #ffffff;
    --tile-border: transparent;
    --tile-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --tile-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
    --tile-title-color: #1f2937;
    --tile-description-color: #4b5563;
    --tile-hover-border: rgba(59, 130, 246, 0.2);
    --category-bg: #ffffff;
    --category-hover-bg: #f9fafb;
    --category-name-color: #1f2937;
    --category-count-color: #6b7280;
    --activity-container-bg: #ffffff;
    --activity-item-hover: #f9fafb;
    --stat-card-bg: #f9fafb;
    --stat-label-color: #6b7280;
    --newsletter-bg: linear-gradient(to right, #8b5cf6, #3b82f6);
    
    /* Animation Colors */
    --glow-blue: rgba(59, 130, 246, 0.5);
    --glow-green: rgba(34, 197, 94, 0.5);
    --glow-purple: rgba(168, 85, 247, 0.5);
    --cyber-cyan: #00ffff;
    --cyber-green: #00ff00;
}

.dark-theme {
    --blog-bg: #111827;
    --blog-title-color: #f3f4f6;
    --blog-description-color: #d1d5db;
    --tile-bg: #1f2937;
    --tile-border: #374151;
    --tile-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
    --tile-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
    --tile-title-color: #f3f4f6;
    --tile-description-color: #d1d5db;
    --tile-hover-border: rgba(96, 165, 250, 0.3);
    --category-bg: #1f2937;
    --category-hover-bg: #374151;
    --category-name-color: #f3f4f6;
    --category-count-color: #9ca3af;
    --activity-container-bg: #1f2937;
    --activity-item-hover: #374151;
    --stat-card-bg: #1f2937;
    --stat-label-color: #9ca3af;
    --newsletter-bg: linear-gradient(to right, #7c3aed, #2563eb);
}

/* Base styles */
.flex-1 {
    background-color: var(--blog-bg);
    transition: background-color 0.3s ease;
    min-height: 100vh;
    position: relative;
}

.container {
    position: relative;
    z-index: 10;
}

/* Animated Background */
.animated-bg-elements {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
}

.floating-particle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.3;
    filter: blur(1px);
    animation: float 15s infinite ease-in-out;
}

.circuit-board-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(to right, var(--glow-blue) 1px, transparent 1px),
        linear-gradient(to bottom, var(--glow-blue) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: 0.05;
}

/* Easter Egg Trigger */
.easter-egg-trigger {
    position: fixed;
    top: 80px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    animation: pulse-glow 2s infinite;
}

.easter-egg-trigger:hover {
    transform: scale(1.1) rotate(15deg);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
}

.easter-egg-trigger.nerd-mode-active {
    background: linear-gradient(135deg, var(--cyber-green) 0%, #008800 100%);
    animation: spin 2s linear infinite, pulse-glow 1s infinite;
}

.easter-tooltip {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 10px;
    background: #1f2937;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.easter-tooltip::before {
    content: '';
    position: absolute;
    bottom: 100%;
    right: 15px;
    border-width: 0 6px 6px 6px;
    border-style: solid;
    border-color: transparent transparent #1f2937 transparent;
}

/* Konami Code Hint */
.konami-hint {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: var(--cyber-green);
    padding: 8px 16px;
    border-radius: 20px;
    font-family: monospace;
    font-size: 12px;
    z-index: 1000;
    opacity: 0.7;
    backdrop-filter: blur(10px);
}

/* Header Styling */
.text-gradient {
    background: linear-gradient(45deg, var(--glow-blue), var(--glow-green), var(--glow-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
}

.sparkle {
    display: inline-block;
    animation: sparkle 2s infinite;
    margin-left: 10px;
}

.blog-description {
    color: var(--blog-description-color);
}

.section-title {
    color: var(--blog-title-color);
}

/* Featured Articles */
.featured-articles-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.featured-article-card {
    background: var(--tile-bg);
    border-radius: 0.75rem;
    box-shadow: var(--tile-shadow);
    padding: 1.25rem;
    text-align: center;
    border: 1px solid var(--tile-border);
    position: relative;
    overflow: hidden;
}

.featured-article-card:hover {
    box-shadow: var(--tile-hover-shadow);
    border-color: var(--tile-hover-border);
}

.tile-glow {
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: 0.75rem;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.featured-article-card:hover .tile-glow {
    background: radial-gradient(circle at center, var(--glow-blue), transparent 70%);
    opacity: 0.3;
}

.featured-article-card:nth-child(2):hover .tile-glow {
    background: radial-gradient(circle at center, var(--glow-green), transparent 70%);
}

.featured-article-card:nth-child(3):hover .tile-glow {
    background: radial-gradient(circle at center, var(--glow-purple), transparent 70%);
}

.floating-icon {
    animation: float-icon 3s ease-in-out infinite;
}

.tile-title {
    color: var(--tile-title-color);
}

.tile-description {
    color: var(--tile-description-color);
}

.interactive-button {
    transition: all 0.3s ease;
}

.interactive-button:hover {
    transform: translateX(3px);
}

/* Blog Categories */
.categories-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.category-card {
    background: var(--category-bg);
    border-radius: 0.75rem;
    box-shadow: var(--tile-shadow);
    padding: 1rem;
    text-align: center;
    border: 1px solid var(--tile-border);
    position: relative;
    overflow: hidden;
}

.category-card:hover {
    background: var(--category-hover-bg);
    box-shadow: var(--tile-hover-shadow);
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
    color: var(--category-name-color);
    margin-bottom: 0.25rem;
}

.category-count {
    color: var(--category-count-color);
    font-size: 0.75rem;
}

.category-glow {
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: 0.75rem;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-card:hover .category-glow {
    background: radial-gradient(circle at center, var(--glow-blue), transparent 70%);
    opacity: 0.2;
}

/* Latest Articles */
.activity-container {
    background: var(--activity-container-bg);
    border: 1px solid var(--tile-border);
}

.activity-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--cyber-green);
    margin-left: 10px;
}

/* Blog Overview Stats */
.stats-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-card {
    border-radius: 0.75rem;
    padding: 1.25rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    background: var(--stat-card-bg);
    border-left-width: 4px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--tile-hover-shadow);
}

.stat-card-blue {
    border-left-color: var(--glow-blue);
}

.stat-card-green {
    border-left-color: var(--glow-green);
}

.stat-card-purple {
    border-left-color: var(--glow-purple);
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
    position: relative;
    z-index: 1;
}

.stat-card-blue .stat-number {
    color: #1d4ed8;
}

.dark-theme .stat-card-blue .stat-number {
    color: #60a5fa;
}

.stat-card-green .stat-number {
    color: #15803d;
}

.dark-theme .stat-card-green .stat-number {
    color: #4ade80;
}

.stat-card-purple .stat-number {
    color: #7e22ce;
}

.dark-theme .stat-card-purple .stat-number {
    color: #a78bfa;
}

.stat-label {
    color: var(--stat-label-color);
    font-size: 0.875rem;
    position: relative;
    z-index: 1;
}

.stat-sparkle {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1), transparent 50%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover .stat-sparkle {
    opacity: 1;
}

.cyber-glow-blue {
    text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
}

.cyber-glow-green {
    text-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
}

.cyber-glow-purple {
    text-shadow: 0 0 10px rgba(168, 85, 247, 0.5);
}

/* Newsletter */
.newsletter-container {
    background: var(--newsletter-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Hidden Message */
.hidden-message {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 15px;
    max-width: 400px;
    z-index: 1000;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
}

.message-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* Nerd Terminal */
.nerd-terminal {
    position: fixed;
    bottom: 30px;
    left: 30px;
    width: 400px;
    max-width: 90%;
    background: #1a1a1a;
    border-radius: 10px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    z-index: 1001;
    overflow: hidden;
    font-family: 'Courier New', monospace;
    border: 1px solid var(--cyber-green);
}

.terminal-header {
    background: #2d2d2d;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #444;
}

.terminal-dots {
    display: flex;
    gap: 8px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.dot.red { background: #ff5f56; }
.dot.yellow { background: #ffbd2e; }
.dot.green { background: #27ca3f; }

.terminal-title {
    color: #888;
    font-size: 14px;
    font-family: 'Courier New', monospace;
}

.terminal-close {
    color: #888;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    line-height: 1;
    transition: color 0.2s;
}

.terminal-body {
    padding: 20px;
    color: var(--cyber-green);
    font-size: 14px;
    max-height: 300px;
    overflow-y: auto;
}

.terminal-line {
    margin-bottom: 10px;
    line-height: 1.5;
}

.prompt {
    color: var(--cyber-green);
    margin-right: 10px;
    font-weight: bold;
}

.blink {
    animation: blink 1s infinite;
}

/* Matrix Rain */
.matrix-rain {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.5s;
    font-family: 'Courier New', monospace;
    color: var(--cyber-green);
    font-size: 14px;
    overflow: hidden;
}

.matrix-rain.active {
    opacity: 0.1;
}

.matrix-rain::before {
    content: '01010101 10101010 00110011 11001100 01010101 10101010 00110011 11001100';
    position: absolute;
    width: 100%;
    animation: matrix-fall 20s linear infinite;
    white-space: pre;
    line-height: 1.5;
}

/* Particle Container */
.particle-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

/* Text truncation utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animations */
@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
    }
}

@keyframes float-icon {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 20px var(--glow-blue);
    }
    50% {
        box-shadow: 0 0 30px var(--glow-purple);
    }
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes sparkle {
    0%, 100% {
        opacity: 0.3;
        transform: scale(0.8);
    }
    50% {
        opacity: 1;
        transform: scale(1.2);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes pulse-slow {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

@keyframes ping-slow {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

@keyframes matrix-fall {
    0% {
        transform: translateY(-100%);
    }
    100% {
        transform: translateY(100vh);
    }
}

@keyframes gradient-shift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

@keyframes blink {
    0%, 50% {
        opacity: 1;
    }
    51%, 100% {
        opacity: 0;
    }
}

/* Animation Classes */
.animate-slide-up {
    animation: slide-up 0.6s ease-out forwards;
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out forwards;
}

.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 2s ease-in-out infinite;
}

.animate-ping-slow {
    animation: ping-slow 2s ease-out infinite;
}

.animate-tada {
    animation: tada 1s ease-out;
}

.animate-gradient-shift {
    background-size: 200% 200%;
    animation: gradient-shift 3s ease infinite;
}

.animate-count-up {
    animation: slide-up 0.5s ease-out;
}

/* Transition Effects */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-down-enter,
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(20px);
}

.terminal-enter-active,
.terminal-leave-active {
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.terminal-enter,
.terminal-leave-to {
    opacity: 0;
    transform: translateX(-30px) scale(0.9);
}

/* Interactive Card Base */
.interactive-card {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.interactive-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.7s ease;
}

.interactive-card:hover::before {
    left: 100%;
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

@media (max-width: 640px) {
    .nerd-terminal {
        width: calc(100% - 40px);
        left: 20px;
        bottom: 20px;
    }
    
    .hidden-message {
        left: 20px;
        right: 20px;
        max-width: none;
    }
    
    .easter-egg-trigger {
        width: 40px;
        height: 40px;
        top: 70px;
        right: 10px;
    }
    
    .konami-hint {
        font-size: 10px;
        padding: 6px 12px;
        bottom: 10px;
    }
}

/* Ensure proper stacking */
.container {
    position: relative;
    z-index: 10;
}

/* Transform effects */
.transform {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}
</style>