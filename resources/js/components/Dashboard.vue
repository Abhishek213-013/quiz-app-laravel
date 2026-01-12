<template>
    <div class="flex-1 overflow-hidden relative">
        <!-- Animated Background Elements -->
        <div class="animated-bg-elements">
            <div class="floating-particle" v-for="(particle, index) in particles" :key="index"
                 :style="particle.style"></div>
            <div class="circuit-board-bg"></div>
        </div>

        <!-- Easter Egg Trigger - Moved below navbar -->
        <div class="easter-egg-trigger" @click="toggleNerdMode"
             @mouseenter="showEasterTooltip = true"
             @mouseleave="showEasterTooltip = false"
             :class="{ 'nerd-mode-active': nerdMode }">
            <span class="text-2xl">🧠</span>
            <div v-if="showEasterTooltip && !nerdMode" class="easter-tooltip">
                Click for NERD MODE
            </div>
        </div>

        <div class="container mx-auto px-4 py-8 relative z-10">
            <!-- Konami Code Progress -->
            <div v-if="konamiProgress > 0" class="konami-hint">
                Konami Code: {{ konamiSequence.slice(0, konamiProgress).join(' ') }}
            </div>

            <!-- Animated Title - Fixed centering -->
            <div class="text-center mb-12">
                <h1 class="dashboard-title animate-slide-up inline-block">
                    <span class="text-gradient animate-gradient-shift text-4xl font-bold">MindSpark</span>
                    <span class="sparkle">✨</span>
                    <div class="dashboard-title-sub text-2xl mt-2">Dashboard</div>
                </h1>
            </div>
        
            <!-- Enhanced Tiles with Staggered Animations -->
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 md:gap-8 max-w-4xl mx-auto">
                <!-- Take Quiz Tile -->
                <div 
                    class="dashboard-tile quiz-tile cursor-pointer transform transition-all duration-300 hover:scale-105 animate-slide-up interactive-card"
                    @click="navigateWithEffect('quiz-sets')"
                    @mouseenter="playHoverSound('click')"
                    :style="{ animationDelay: '0.1s' }"
                >
                    <div class="icon-container bg-blue-500 mb-3 sm:mb-4 animate-pulse-slow">
                        <span class="text-xl sm:text-2xl text-white floating-icon">📝</span>
                    </div>
                    <h3 class="tile-title mb-2">Take Quiz</h3>
                    <p class="tile-description">Start a new quiz and test your knowledge</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden theme-hint">(Mobile Layout)</div>
                    <div class="tile-glow"></div>
                    <div class="beaker-bubble"></div>
                </div>

                <!-- GK Blog Tile -->
                <div 
                    class="dashboard-tile blog-tile cursor-pointer transform transition-all duration-300 hover:scale-105 animate-slide-up interactive-card"
                    @click="navigateWithEffect('gk-blog')"
                    @mouseenter="playHoverSound('page')"
                    :style="{ animationDelay: '0.2s' }"
                >
                    <div class="icon-container bg-green-500 mb-3 sm:mb-4 animate-pulse-slow">
                        <span class="text-xl sm:text-2xl text-white floating-icon">📚</span>
                    </div>
                    <h3 class="tile-title mb-2">GK Blog</h3>
                    <p class="tile-description">Enhance your knowledge with educational articles</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden theme-hint">(Mobile Layout)</div>
                    <div class="tile-glow"></div>
                    <div class="beaker-bubble"></div>
                </div>

                <!-- Previous Records Tile -->
                <div 
                    class="dashboard-tile records-tile cursor-pointer transform transition-all duration-300 hover:scale-105 animate-slide-up interactive-card"
                    @click="navigateWithEffect('records')"
                    @mouseenter="playHoverSound('data')"
                    :style="{ animationDelay: '0.3s' }"
                >
                    <div class="icon-container bg-purple-500 mb-3 sm:mb-4 animate-pulse-slow">
                        <span class="text-xl sm:text-2xl text-white floating-icon">📊</span>
                    </div>
                    <h3 class="tile-title mb-2">Previous Records</h3>
                    <p class="tile-description">View and filter previous quiz results</p>
                    <div class="mt-2 text-xs text-gray-400 sm:hidden theme-hint">(Mobile Layout)</div>
                    <div class="tile-glow"></div>
                    <div class="beaker-bubble"></div>
                </div>
            </div>

            <!-- Responsive Breakpoint Indicator -->
            <div class="mt-8 max-w-4xl mx-auto text-center">
                <div class="breakpoint-indicator animate-fade-in">
                    <div class="text-sm">
                        Current layout: 
                        <span class="font-semibold text-blue-600 sm:hidden animate-pulse-slow">MOBILE (vertical)</span>
                        <span class="font-semibold text-green-600 hidden sm:inline md:hidden animate-pulse-slow">SMALL (horizontal)</span>
                        <span class="font-semibold text-purple-600 hidden md:inline animate-pulse-slow">DESKTOP (horizontal)</span>
                    </div>
                    <div class="mt-2 text-xs opacity-75 flex items-center justify-center gap-2">
                        System Status: 
                        <span class="status-dot"></span>
                        <span class="animate-gradient-shift">All Systems Operational</span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Section with Counting Animation -->
            <div class="mt-16 max-w-4xl mx-auto">
                <h2 class="section-title mb-8 animate-fade-in text-center">Quick Overview</h2>
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <div class="stat-card blue-card animate-slide-up" :style="{ animationDelay: '0.1s' }">
                        <div class="stat-number text-blue-600 mb-2 cyber-glow-blue animate-count-up">
                            {{ animatedQuizzes }}
                        </div>
                        <div class="stat-label">Available Quizzes</div>
                        <div class="stat-sparkle"></div>
                        <div class="data-stream"></div>
                    </div>
                    <div class="stat-card green-card animate-slide-up" :style="{ animationDelay: '0.2s' }">
                        <div class="stat-number text-green-600 mb-2 cyber-glow-green animate-count-up">
                            {{ animatedArticles }}
                        </div>
                        <div class="stat-label">GK Articles</div>
                        <div class="stat-sparkle"></div>
                        <div class="data-stream"></div>
                    </div>
                    <div class="stat-card purple-card animate-slide-up" :style="{ animationDelay: '0.3s' }">
                        <div class="stat-number text-purple-600 mb-2 cyber-glow-purple animate-count-up">
                            {{ animatedAttempts }}
                        </div>
                        <div class="stat-label">Quiz Attempts</div>
                        <div class="stat-sparkle"></div>
                        <div class="data-stream"></div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity with Enhanced Effects -->
            <!-- <div class="mt-16 max-w-4xl mx-auto">
                <h2 class="section-title mb-8 animate-fade-in text-center">Recent Activity</h2>
                <div class="activity-container crt-scanlines">
                    <div class="space-y-4">
                        <div 
                            v-for="(activity, index) in recentActivities" 
                            :key="activity.id" 
                            class="activity-item animate-slide-right interactive-card"
                            :style="{ animationDelay: `${index * 0.1}s` }"
                            @mouseenter="playHoverSound('notification')"
                        >
                            <div :class="['activity-icon', activity.bgColor, 'animate-bounce-slow']">
                                <span class="text-white text-xs sm:text-sm">{{ activity.icon }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="activity-message">{{ activity.message }}</p>
                                <p class="activity-time">{{ activity.time }}</p>
                            </div>
                            <div class="activity-indicator animate-ping-slow"></div>
                            <div class="neural-node" :style="getNodeStyle(index)"></div>
                        </div>
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
                        <span class="terminal-title">mindspark@nerd_mode:~</span>
                        <button @click="toggleNerdMode" class="terminal-close hover:text-white">×</button>
                    </div>
                    <div class="terminal-body">
                        <div class="terminal-line">
                            <span class="prompt">$</span> Welcome to <span class="text-cyan-400">Nerd Mode</span> <span class="blink">_</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">$</span> System initialized at {{ new Date().toLocaleTimeString() }}
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Active Users: <span class="text-green-400">{{ totalAttempts }}</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Knowledge Base: <span class="text-blue-400">{{ totalQuizzes }} quizzes</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Articles Loaded: <span class="text-purple-400">{{ totalArticles }}</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">$</span> Type <span class="text-yellow-400">'help'</span> for commands
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">#</span> <span class="text-pink-400">mindspark_ai</span> is ready for challenges!
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
    name: 'Dashboard',
    props: {
        theme: {
            type: String,
            default: 'light'
        }
    },
    data() {
        return {
            totalQuizzes: 5,
            totalArticles: 24,
            totalAttempts: 36,
            animatedQuizzes: 0,
            animatedArticles: 0,
            animatedAttempts: 0,
            nerdMode: false,
            showEasterTooltip: false,
            showHiddenMessage: false,
            konamiProgress: 0,
            konamiSequence: ['↑', '↑', '↓', '↓', '←', '→', '←', '→', 'B', 'A'],
            randomFact: '',
            particles: [],
            recentActivities: [
                {
                    id: 1,
                    message: "New Geography quiz set added",
                    time: "2 hours ago",
                    icon: '🌎',
                    bgColor: "bg-blue-500"
                },
                {
                    id: 2,
                    message: "Science & Technology article published",
                    time: "5 hours ago",
                    icon: '🧪',
                    bgColor: "bg-green-500"
                },
                {
                    id: 3,
                    message: "History quiz completed by 15 participants",
                    time: "1 day ago",
                    icon: '📜',
                    bgColor: "bg-purple-500"
                },
                {
                    id: 4,
                    message: "New World Facts article available",
                    time: "2 days ago",
                    icon: '📖',
                    bgColor: "bg-orange-500"
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
        await this.fetchDashboardStats();
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
        async fetchDashboardStats() {
            try {
                const quizSetsResponse = await axios.get('/api/quiz-sets');
                this.totalQuizzes = quizSetsResponse.data.length;

                const recordsResponse = await axios.get('/api/quiz-results');
                this.totalAttempts = recordsResponse.data.length;

                this.totalArticles = 24;

            } catch (error) {
                console.error('Error fetching dashboard stats:', error);
                this.totalQuizzes = 5;
                this.totalArticles = 24;
                this.totalAttempts = 36;
            }
        },
        
        animateNumbers() {
            const duration = 1500;
            const steps = 60;
            
            const animate = (target, currentValue) => {
                const increment = target / steps;
                let current = currentValue;
                let step = 0;
                
                const timer = setInterval(() => {
                    step++;
                    if (currentValue === 'animatedQuizzes') {
                        this.animatedQuizzes = Math.min(Math.floor(increment * step), this.totalQuizzes);
                    } else if (currentValue === 'animatedArticles') {
                        this.animatedArticles = Math.min(Math.floor(increment * step), this.totalArticles);
                    } else if (currentValue === 'animatedAttempts') {
                        this.animatedAttempts = Math.min(Math.floor(increment * step), this.totalAttempts);
                    }
                    
                    if (step >= steps) {
                        clearInterval(timer);
                    }
                }, duration / steps);
            };
            
            animate(this.totalQuizzes, 'animatedQuizzes');
            animate(this.totalArticles, 'animatedArticles');
            animate(this.totalAttempts, 'animatedAttempts');
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
        
        navigateWithEffect(route) {
            this.createClickEffect();
            setTimeout(() => {
                this.$emit('navigate', route);
            }, 300);
        },
        
        playHoverSound(type) {
            // In a real app, you would play actual sounds
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
            this.totalQuizzes += 10;
            this.totalArticles += 8;
            this.totalAttempts += 25;
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
        
        getNodeStyle(index) {
            const left = 10 + (index * 25);
            const top = 50;
            return {
                left: `${left}%`,
                top: `${top}%`,
                animationDelay: `${index * 0.5}s`
            };
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
    --dashboard-bg: #ffffff;
    --dashboard-title-color: #1f2937;
    --tile-bg: #ffffff;
    --tile-border: transparent;
    --tile-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --tile-title-color: #1f2937;
    --tile-description-color: #4b5563;
    --tile-hover-border: rgba(59, 130, 246, 0.2);
    --stat-card-bg: #f9fafb;
    --activity-container-bg: #ffffff;
    --activity-item-hover: #f9fafb;
    --activity-message-color: #1f2937;
    --activity-time-color: #6b7280;
    --breakpoint-indicator-bg: #f3f4f6;
    --theme-hint-color: #9ca3af;
    
    /* Animation Colors */
    --glow-blue: rgba(59, 130, 246, 0.5);
    --glow-green: rgba(34, 197, 94, 0.5);
    --glow-purple: rgba(168, 85, 247, 0.5);
    --cyber-cyan: #00ffff;
    --cyber-pink: #ff00ff;
    --cyber-green: #00ff00;
}

.dark-theme {
    --dashboard-bg: #111827;
    --dashboard-title-color: #f3f4f6;
    --tile-bg: #1f2937;
    --tile-border: #374151;
    --tile-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
    --tile-title-color: #f3f4f6;
    --tile-description-color: #d1d5db;
    --tile-hover-border: rgba(96, 165, 250, 0.3);
    --stat-card-bg: #1f2937;
    --activity-container-bg: #1f2937;
    --activity-item-hover: #374151;
    --activity-message-color: #f3f4f6;
    --activity-time-color: #9ca3af;
    --breakpoint-indicator-bg: #374151;
    --theme-hint-color: #6b7280;
}

/* Base styles */
.flex-1 {
    background-color: var(--dashboard-bg);
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

/* Easter Egg Trigger - Fixed position to avoid navbar overlap */
.easter-egg-trigger {
    position: fixed;
    top: 80px; /* Positioned below typical navbar height */
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

/* Title Styling - Fixed centering */
.dashboard-title {
    position: relative;
    display: inline-block;
    color: var(--dashboard-title-color);
}

.text-gradient {
    background: linear-gradient(45deg, var(--glow-blue), var(--glow-green), var(--glow-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
}

.sparkle {
    position: absolute;
    top: -10px;
    right: -30px;
    animation: sparkle 2s infinite;
    font-size: 1.5rem;
}

.dashboard-title-sub {
    display: block;
    opacity: 0.8;
    color: var(--dashboard-title-color);
}

/* Enhanced Tile Styles */
.dashboard-tile {
    background: var(--tile-bg);
    border: 2px solid var(--tile-border);
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: var(--tile-shadow);
    flex: 1;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.dashboard-tile::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.7s ease;
}

.dashboard-tile:hover::before {
    left: 100%;
}

.dashboard-tile:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--tile-hover-border);
}

.quiz-tile:hover {
    --tile-hover-border: var(--glow-blue);
}

.blog-tile:hover {
    --tile-hover-border: var(--glow-green);
}

.records-tile:hover {
    --tile-hover-border: var(--glow-purple);
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

.quiz-tile:hover .tile-glow {
    background: radial-gradient(circle at center, var(--glow-blue), transparent 70%);
    opacity: 0.3;
}

.blog-tile:hover .tile-glow {
    background: radial-gradient(circle at center, var(--glow-green), transparent 70%);
    opacity: 0.3;
}

.records-tile:hover .tile-glow {
    background: radial-gradient(circle at center, var(--glow-purple), transparent 70%);
    opacity: 0.3;
}

.icon-container {
    width: 3rem;
    height: 3rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
    margin-right: auto;
    position: relative;
    overflow: hidden;
}

.icon-container::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transform: rotate(45deg);
    transition: transform 0.5s ease;
}

.icon-container:hover::after {
    transform: rotate(45deg) translate(50%, 50%);
}

@media (min-width: 640px) {
    .icon-container {
        width: 4rem;
        height: 4rem;
    }
}

.tile-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--tile-title-color);
    transition: color 0.3s ease;
    position: relative;
}

@media (min-width: 640px) {
    .tile-title {
        font-size: 1.25rem;
    }
}

.tile-description {
    font-size: 0.875rem;
    color: var(--tile-description-color);
    transition: color 0.3s ease;
}

@media (min-width: 640px) {
    .tile-description {
        font-size: 1rem;
    }
}

.theme-hint {
    color: var(--theme-hint-color);
}

/* Breakpoint indicator */
.breakpoint-indicator {
    display: inline-block;
    background: var(--breakpoint-indicator-bg);
    border-radius: 0.75rem;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid var(--tile-border);
}

.status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--cyber-green);
    animation: status-pulse 2s infinite;
    margin-right: 5px;
}

/* Stat cards */
.stat-card {
    background: var(--stat-card-bg);
    border-radius: 0.75rem;
    padding: 1rem;
    flex: 1;
    border-left-width: 4px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--tile-shadow);
}

@media (min-width: 640px) {
    .stat-card {
        padding: 1.5rem;
    }
}

.blue-card {
    border-left-color: var(--glow-blue);
}

.green-card {
    border-left-color: var(--glow-green);
}

.purple-card {
    border-left-color: var(--glow-purple);
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

@media (min-width: 640px) {
    .stat-number {
        font-size: 1.875rem;
    }
}

.stat-label {
    color: var(--tile-description-color);
    font-size: 0.875rem;
    transition: color 0.3s ease;
    position: relative;
    z-index: 1;
}

@media (min-width: 640px) {
    .stat-label {
        font-size: 1rem;
    }
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

/* Activity section */
.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dashboard-title-color);
    transition: color 0.3s ease;
    position: relative;
}

.activity-container {
    background: var(--activity-container-bg);
    border-radius: 0.75rem;
    box-shadow: var(--tile-shadow);
    padding: 1rem;
    transition: all 0.3s ease;
    position: relative;
}

@media (min-width: 640px) {
    .activity-container {
        padding: 1.5rem;
    }
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    position: relative;
}

.activity-item:hover {
    background: var(--activity-item-hover);
    transform: translateX(5px);
}

.activity-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

@media (min-width: 640px) {
    .activity-icon {
        width: 2.5rem;
        height: 2.5rem;
    }
}

.activity-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--cyber-green);
    margin-left: 10px;
}

.activity-message {
    color: var(--activity-message-color);
    font-weight: 500;
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

@media (min-width: 640px) {
    .activity-message {
        font-size: 1rem;
    }
}

.activity-time {
    color: var(--activity-time-color);
    font-size: 0.75rem;
    transition: color 0.3s ease;
}

@media (min-width: 640px) {
    .activity-time {
        font-size: 0.875rem;
    }
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

/* Fireworks */
.firework {
    position: absolute;
    width: 4px;
    height: 4px;
    background: var(--firework-color);
    border-radius: 50%;
    animation: firework-explode 1s ease-out forwards;
    pointer-events: none;
}

/* Click Particles */
.click-particle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: var(--click-color);
    border-radius: 50%;
    animation: particle-burst 0.6s ease-out forwards;
    pointer-events: none;
}

/* Neural Node */
.neural-node {
    position: absolute;
    width: 6px;
    height: 6px;
    background: var(--cyber-blue);
    border-radius: 50%;
    animation: node-pulse 2s infinite;
    z-index: 0;
}

/* Beaker Bubble */
.beaker-bubble {
    position: absolute;
    width: 10px;
    height: 10px;
    background: rgba(59, 130, 246, 0.3);
    border-radius: 50%;
    animation: bubble-rise 4s infinite ease-in;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
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

@keyframes status-pulse {
    0%, 100% {
        opacity: 1;
        box-shadow: 0 0 5px var(--cyber-green);
    }
    50% {
        opacity: 0.7;
        box-shadow: 0 0 10px var(--cyber-green);
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

@keyframes slide-right {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
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

/* Animation Classes */
.animate-slide-up {
    animation: slide-up 0.6s ease-out forwards;
}

.animate-slide-right {
    animation: slide-right 0.5s ease-out forwards;
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

/* Responsive Adjustments */
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
        top: 70px; /* Adjusted for mobile */
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

/* Transform effects */Recent Activity
.transform {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}
</style>