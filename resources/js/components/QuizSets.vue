<template>
    <div class="flex-1 overflow-hidden relative">
        <!-- Animated Background Elements -->
        <div class="animated-bg-elements">
            <div class="floating-particle" v-for="(particle, index) in particles" :key="index"
                 :style="particle.style"></div>
            <div class="circuit-board-bg"></div>
        </div>

        <!-- Easter Egg Trigger -->
        <!-- <div class="easter-egg-trigger" @click="toggleQuizMasterMode"
             @mouseenter="showEasterTooltip = true"
             @mouseleave="showEasterTooltip = false"
             :class="{ 'nerd-mode-active': quizMasterMode }">
            <span class="text-2xl">🎯</span>
            <div v-if="showEasterTooltip && !quizMasterMode" class="easter-tooltip">
                Click for NERD MODE
            </div>
        </div> -->

        <div class="container mx-auto px-4 py-8 relative z-10">
            <!-- Konami Code Progress -->
            <div v-if="konamiProgress > 0" class="konami-hint">
                Konami Code: {{ konamiSequence.slice(0, konamiProgress).join(' ') }}
            </div>

            <!-- Animated Header -->
            <div class="text-center mb-12">
                <div class="dashboard-title animate-slide-up inline-block">
                    <span class="text-gradient animate-gradient-shift text-4xl font-bold">Choose Quiz Set</span>
                    <span class="sparkle">🎯</span>
                    <div class="dashboard-title-sub text-2xl mt-2">Select your challenge</div>
                </div>
                <div class="mt-4 text-sm text-gray-500 animate-fade-in">
                    <span class="status-dot"></span>
                    {{ quizSets.length }} sets ready for battle
                </div>
            </div>

            <!-- Stats Overview with Animations -->
            <div v-if="quizSets.length > 0" class="stats-grid mb-12">
                <div class="stat-card blue-stat animate-slide-up" :style="{ animationDelay: '0.1s' }">
                    <div class="stat-number blue-stat-number cyber-glow-blue animate-count-up">
                        {{ quizSets.length }}
                    </div>
                    <div class="stat-label blue-stat-label">Total Sets</div>
                    <div class="stat-sparkle"></div>
                </div>
                <div class="stat-card green-stat animate-slide-up" :style="{ animationDelay: '0.2s' }">
                    <div class="stat-number green-stat-number cyber-glow-green animate-count-up">
                        {{ animatedQuestions }}
                    </div>
                    <div class="stat-label green-stat-label">Questions</div>
                    <div class="stat-sparkle"></div>
                </div>
                <div class="stat-card purple-stat animate-slide-up" :style="{ animationDelay: '0.3s' }">
                    <div class="stat-number purple-stat-number cyber-glow-purple animate-count-up">
                        {{ totalTime }}
                    </div>
                    <div class="stat-label purple-stat-label">Total Time</div>
                    <div class="stat-sparkle"></div>
                </div>
                <div class="stat-card orange-stat animate-slide-up" :style="{ animationDelay: '0.4s' }">
                    <div class="stat-number orange-stat-number cyber-glow-orange animate-count-up">
                        {{ animatedCategories }}
                    </div>
                    <div class="stat-label orange-stat-label">Categories</div>
                    <div class="stat-sparkle"></div>
                </div>
            </div>

            <!-- Quiz Sets Grid with Enhanced Animations -->
            <div class="quizsets-grid">
                <div 
                    v-for="(quizSet, index) in quizSets" 
                    :key="quizSet.id"
                    class="quizset-card animate-slide-up interactive-card"
                    :style="{ animationDelay: `${index * 0.1}s` }"
                    @click="selectQuizSetWithEffect(quizSet.id)"
                    @mouseenter="playHoverSound('card')"
                >
                    <!-- Category Badge with Animation -->
                    <div class="category-section">
                        <span class="category-badge animate-pulse-slow">
                            {{ getCategoryIcon(quizSet.category) }} {{ quizSet.category }}
                        </span>
                        <span class="question-count floating-icon">
                            ❓ {{ quizSet.question_count }}
                        </span>
                    </div>
                    
                    <!-- Quiz Set Title -->
                    <h3 class="quizset-title glitch" :data-text="quizSet.name">{{ quizSet.name }}</h3>
                    
                    <!-- Description -->
                    <p class="quizset-description">{{ quizSet.description }}</p>
                    
                    <!-- Stats with Animations -->
                    <div class="quizset-stats">
                        <span class="stat-item animate-bounce-slow">
                            ⏱️ {{ quizSet.time_limit }}s
                        </span>
                        <span class="stat-item animate-bounce-slow" :style="{ animationDelay: '0.2s' }">
                            🕒 {{ Math.floor(quizSet.time_limit / 60) }}min
                        </span>
                        <span class="stat-item animate-bounce-slow" :style="{ animationDelay: '0.4s' }">
                            📊 {{ getDifficulty(quizSet.difficulty) }}
                        </span>
                    </div>
                    
                    <!-- Start Quiz Button with Effects -->
                    <button 
                        class="start-quiz-btn animate-pulse-slow"
                        @click.stop="selectQuizSetWithEffect(quizSet.id)"
                    >
                        <span class="button-icon animate-spin-slow">🚀</span>
                        Start Quiz
                        <span class="button-sparkle"></span>
                    </button>
                    
                    <!-- Card Glow Effect -->
                    <div class="tile-glow"></div>
                    <!-- Science Bubbles -->
                    <div class="beaker-bubble" :style="{ left: '20%', animationDelay: '0.5s' }"></div>
                    <div class="beaker-bubble" :style="{ left: '60%', animationDelay: '1s' }"></div>
                </div>
            </div>

            <!-- Loading State with Animations -->
            <div v-if="loading" class="loading-state animate-fade-in">
                <div class="loading-spinner animate-spin-slow"></div>
                <p class="loading-text animate-pulse-slow">Loading quiz sets...</p>
                <p class="loading-subtext">Preparing your knowledge battlefield</p>
                <div class="loading-particles">
                    <div class="loading-particle" v-for="n in 8" :key="n"
                         :style="{ animationDelay: `${n * 0.2}s` }"></div>
                </div>
            </div>

            <!-- Error State -->
            <div v-if="error" class="error-state animate-shake">
                <div class="error-content">
                    <div class="error-icon animate-tada">😞</div>
                    <h3 class="error-title">Connection Lost!</h3>
                    <p class="error-message">{{ error }}</p>
                    <button 
                        @click="fetchQuizSets"
                        class="retry-btn animate-pulse-slow"
                    >
                        🔄 Retry Connection
                    </button>
                </div>
            </div>

            <!-- Empty State with Easter Egg -->
            <div v-if="!loading && quizSets.length === 0 && !error" class="empty-state animate-fade-in">
                <div class="empty-icon animate-tada">📚</div>
                <h3 class="empty-title">No Quiz Sets Available</h3>
                <p class="empty-message">The quiz library is currently being updated with new challenges.</p>
                <button 
                    @click="fetchQuizSets"
                    class="refresh-btn animate-pulse-slow"
                >
                    🔄 Refresh Library
                </button>
                <div class="empty-hint mt-4 text-sm opacity-50">
                    Tip: Try the Konami Code for secret quizzes!
                </div>
            </div>

            <!-- Nerd Mode Terminal (Matches Dashboard Exactly) -->
            <transition name="terminal">
                <div v-if="quizMasterMode" class="nerd-terminal">
                    <div class="terminal-header">
                        <div class="terminal-dots">
                            <span class="dot red"></span>
                            <span class="dot yellow"></span>
                            <span class="dot green"></span>
                        </div>
                        <span class="terminal-title">mindspark@nerd_mode:~</span>
                        <button @click="toggleQuizMasterMode" class="terminal-close hover:text-white">×</button>
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
                            <span class="prompt">></span> Quiz Sets: <span class="text-blue-400">{{ quizSets.length }} loaded</span>
                        </div>
                        <div class="terminal-line">
                            <span class="prompt">></span> Questions: <span class="text-purple-400">{{ totalQuestions }}</span>
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

            <!-- Hidden Quiz Fact -->
            <transition name="slide-down">
                <div v-if="showQuizFact" class="hidden-message animate-tada">
                    <div class="message-content">
                        <span class="text-3xl mr-3">🧠</span>
                        <div>
                            <h3 class="font-bold">Quiz Trivia!</h3>
                            <p class="text-sm opacity-75 mt-1">{{ randomQuizFact }}</p>
                        </div>
                        <button @click="showQuizFact = false" class="ml-auto text-2xl hover:text-red-500 transition-colors">×</button>
                    </div>
                </div>
            </transition>

            <!-- Particle Container -->
            <div class="particle-container" ref="particleContainer"></div>
        </div>

        <!-- Matrix Rain Effect -->
        <div class="matrix-rain" :class="{ active: quizMasterMode }"></div>
    </div>
</template>

<script>
export default {
    name: 'QuizSets',
    props: {
        theme: {
            type: String,
            default: 'light'
        }
    },
    data() {
        return {
            quizSets: [],
            loading: false,
            error: null,
            animatedQuestions: 0,
            animatedCategories: 0,
            quizMasterMode: false,
            showEasterTooltip: false,
            showQuizFact: false,
            konamiProgress: 0,
            konamiSequence: ['↑', '↑', '↓', '↓', '←', '→', '←', '→', 'B', 'A'],
            randomQuizFact: '',
            particles: [],
            quizFacts: [
                "The longest quiz marathon lasted 60 hours and contained 5,000 questions!",
                "The word 'quiz' was originally slang for an eccentric person",
                "Japan has quiz shows where contestants compete for millions of yen",
                "The first known quiz was held in 1780 by a Dublin newspaper",
                "There's a quiz show that tests contestants while they're skydiving",
                "Some quiz champions can answer questions in under 0.5 seconds",
                "The largest quiz had 2,280 participants in one venue",
                "Quiz bowls have been part of academic competitions since 1953"
            ]
        }
    },
    computed: {
        totalQuestions() {
            return this.quizSets.reduce((total, set) => total + (set.question_count || 0), 0);
        },
        totalTime() {
            const totalSeconds = this.quizSets.reduce((total, set) => total + (set.time_limit || 0), 0);
            const minutes = Math.floor(totalSeconds / 60);
            return minutes > 0 ? `${minutes}m` : `${totalSeconds}s`;
        },
        categories() {
            const uniqueCategories = new Set(this.quizSets.map(set => set.category));
            return Array.from(uniqueCategories);
        }
    },
    async mounted() {
        await this.fetchQuizSets();
        this.animateNumbers();
        this.initParticles();
        this.initKonamiCode();
        this.initQuizFacts();
    },
    methods: {
        async fetchQuizSets() {
            this.loading = true;
            this.error = null;
            
            try {
                console.log('🔄 Fetching quiz sets...');
                const response = await fetch('/api/quiz-sets');
                
                if (!response.ok) {
                    throw new Error(`Failed to fetch quiz sets: ${response.status} ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('✅ Quiz sets loaded:', data);
                
                this.quizSets = Array.isArray(data) ? data : [];
                
                if (this.quizSets.length === 0) {
                    console.log('ℹ️ No quiz sets found');
                }
                
            } catch (error) {
                console.error('❌ Error fetching quiz sets:', error);
                this.error = error.message || 'Failed to load quiz sets. Please check your connection and try again.';
                this.quizSets = [];
            } finally {
                this.loading = false;
            }
        },

        animateNumbers() {
            // Animate questions count
            const duration = 1500;
            const steps = 60;
            const increment = this.totalQuestions / steps;
            let step = 0;
            
            const timer = setInterval(() => {
                step++;
                this.animatedQuestions = Math.min(Math.floor(increment * step), this.totalQuestions);
                this.animatedCategories = Math.min(Math.floor(step / steps * this.categories.length), this.categories.length);
                
                if (step >= steps) {
                    clearInterval(timer);
                }
            }, duration / steps);
        },

        selectQuizSetWithEffect(quizSetId) {
            console.log('🎯 Selected quiz set:', quizSetId);
            
            const selectedSet = this.quizSets.find(set => set.id === quizSetId);
            if (!selectedSet) {
                this.error = 'Invalid quiz set selected';
                return;
            }
            
            // Create particle effect
            this.createQuizSelectEffect();
            
            // Show selection feedback
            this.showSelectionFeedback(selectedSet.name);
            
            // Navigate after animation
            setTimeout(() => {
                this.$emit('select-set', quizSetId);
            }, 500);
        },

        showSelectionFeedback(quizSetName) {
            console.log(`🚀 Starting quiz: ${quizSetName}`);
            this.showNotification(`Launching: ${quizSetName}`, 'quiz');
        },

        getCategoryIcon(category) {
            const iconMap = {
                'Science': '🔬',
                'Geography': '🌍',
                'History': '📜',
                'Mathematics': '🧮',
                'Technology': '💻',
                'Sports': '⚽',
                'Entertainment': '🎬',
                'General': '🌟',
                'Literature': '📚',
                'Art': '🎨',
                'Music': '🎵',
                'Biology': '🧬',
                'Chemistry': '⚗️',
                'Physics': '⚛️',
                'World': '🗺️',
                'Current Affairs': '📰'
            };
            
            return iconMap[category] || '📝';
        },

        getDifficulty(level) {
            const difficulties = {
                'easy': 'Easy',
                'medium': 'Medium',
                'hard': 'Hard',
                'expert': 'Expert'
            };
            return difficulties[level] || 'Medium';
        },

        toggleQuizMasterMode() {
            this.quizMasterMode = !this.quizMasterMode;
            if (this.quizMasterMode) {
                this.createFireworks();
                this.showNotification("Quiz Master Mode Activated!", "success");
            }
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
                        this.activateQuizKonami();
                        sequence = [];
                        this.konamiProgress = 0;
                    }
                } else {
                    sequence = [];
                    this.konamiProgress = 0;
                }
            });
        },

        activateQuizKonami() {
            this.quizMasterMode = true;
            this.showQuizFact = true;
            this.randomQuizFact = this.quizFacts[Math.floor(Math.random() * this.quizFacts.length)];
            this.createFireworks();
            
            // Add bonus quiz sets for fun
            this.showNotification("Secret Quizzes Unlocked!", "achievement");
            
            setTimeout(() => {
                this.showQuizFact = false;
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

        createQuizSelectEffect() {
            const container = this.$refs.particleContainer;
            if (!container) return;
            
            for (let i = 0; i < 12; i++) {
                const particle = document.createElement('div');
                particle.className = 'quiz-particle';
                particle.style.cssText = `
                    --tx: ${(Math.random() - 0.5) * 150}px;
                    --ty: ${(Math.random() - 0.5) * 150}px;
                    background: hsl(${Math.random() * 360}, 100%, 60%);
                    left: 50%;
                    top: 50%;
                `;
                container.appendChild(particle);
                
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.parentNode.removeChild(particle);
                    }
                }, 800);
            }
        },

        initQuizFacts() {
            setInterval(() => {
                if (!this.showQuizFact && Math.random() > 0.7) {
                    this.randomQuizFact = this.quizFacts[Math.floor(Math.random() * this.quizFacts.length)];
                    this.showQuizFact = true;
                    
                    setTimeout(() => {
                        this.showQuizFact = false;
                    }, 6000);
                }
            }, 60000);
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
/* Add to your existing theme variables */
:root {
    --quizsets-bg: #ffffff;
    --quizsets-title-color: #1f2937;
    --quizsets-subtitle-color: #4b5563;
    --card-bg: #ffffff;
    --card-border: #e5e7eb;
    --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --card-hover-border: #3b82f6;
    --text-primary: #1f2937;
    --text-secondary: #4b5563;
    --text-tertiary: #6b7280;
    --stat-card-bg: #f9fafb;
    --loading-color: #3b82f6;
    --error-bg: #fef2f2;
    --error-border: #fecaca;
    --error-title: #991b1b;
    --error-text: #dc2626;
    --empty-icon-color: #6b7280;
    --cyber-blue: #0066ff;
    --cyber-green: #00ff00;
    --cyber-purple: #9900ff;
    --cyber-orange: #ff9900;
    --glow-blue: rgba(59, 130, 246, 0.5);
    --glow-green: rgba(34, 197, 94, 0.5);
    --glow-purple: rgba(168, 85, 247, 0.5);
    --glow-orange: rgba(245, 158, 11, 0.5);
}

.dark-theme {
    --quizsets-bg: #111827;
    --quizsets-title-color: #f3f4f6;
    --quizsets-subtitle-color: #d1d5db;
    --card-bg: #1f2937;
    --card-border: #374151;
    --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
    --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
    --card-hover-border: #60a5fa;
    --text-primary: #f3f4f6;
    --text-secondary: #d1d5db;
    --text-tertiary: #9ca3af;
    --stat-card-bg: #1f2937;
    --loading-color: #60a5fa;
    --error-bg: #1f2937;
    --error-border: #7f1d1d;
    --error-title: #fca5a5;
    --error-text: #fca5a5;
    --empty-icon-color: #9ca3af;
}

/* Base Styles */
.flex-1 {
    background-color: var(--quizsets-bg);
    min-height: 100vh;
    position: relative;
    transition: background-color 0.3s ease;
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

.easter-egg-trigger.quiz-master-active {
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

/* Title Styling */
.dashboard-title {
    position: relative;
    display: inline-block;
    color: var(--quizsets-title-color);
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
    color: var(--quizsets-subtitle-color);
}

/* Stats Grid with Enhanced Effects */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

@media (min-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.stat-card {
    background: var(--stat-card-bg);
    padding: 1rem;
    border-radius: 0.75rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-hover-shadow);
}

.blue-stat { border-color: var(--glow-blue); }
.green-stat { border-color: var(--glow-green); }
.purple-stat { border-color: var(--glow-purple); }
.orange-stat { border-color: var(--glow-orange); }

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
    position: relative;
    z-index: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
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
    text-shadow: 0 0 10px var(--glow-blue), 0 0 20px var(--glow-blue);
}

.cyber-glow-green {
    text-shadow: 0 0 10px var(--glow-green), 0 0 20px var(--glow-green);
}

.cyber-glow-purple {
    text-shadow: 0 0 10px var(--glow-purple), 0 0 20px var(--glow-purple);
}

.cyber-glow-orange {
    text-shadow: 0 0 10px var(--glow-orange), 0 0 20px var(--glow-orange);
}

/* Quiz Sets Grid */
.quizsets-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .quizsets-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .quizsets-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Quiz Set Card with Enhanced Effects */
.quizset-card {
    background: var(--card-bg);
    border: 2px solid var(--card-border);
    border-radius: 0.75rem;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.quizset-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.7s ease;
}

.quizset-card:hover::before {
    left: 100%;
}

.quizset-card:hover {
    transform: scale(1.05);
    box-shadow: var(--card-hover-shadow);
    border-color: var(--card-hover-border);
}

/* Category Section */
.category-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.category-badge {
    background: linear-gradient(135deg, var(--glow-blue), var(--glow-purple));
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
}

.dark-theme .category-badge {
    background: linear-gradient(135deg, var(--cyber-blue), var(--cyber-purple));
}

.question-count {
    background: var(--stat-card-bg);
    color: var(--text-tertiary);
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

/* Quiz Set Content */
.quizset-title {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: bold;
    margin-bottom: 0.75rem;
    transition: color 0.3s ease;
    position: relative;
}

.quizset-description {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 1rem;
    transition: color 0.3s ease;
}

/* Stats */
.quizset-stats {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.875rem;
    color: var(--text-tertiary);
    border-top: 1px solid var(--card-border);
    padding-top: 0.75rem;
    transition: color 0.3s ease, border-color 0.3s ease;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Start Quiz Button with Enhanced Effects */
.start-quiz-btn {
    width: 100%;
    margin-top: 1rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    font-weight: 600;
    padding: 0.75rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.start-quiz-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: scale(1.05);
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
}

.dark-theme .start-quiz-btn {
    background: linear-gradient(135deg, #34d399, #10b981);
}

.dark-theme .start-quiz-btn:hover {
    background: linear-gradient(135deg, #10b981, #059669);
}

.button-icon {
    display: inline-block;
}

.button-sparkle {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 10px;
    height: 10px;
    background: white;
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.start-quiz-btn:hover .button-sparkle {
    opacity: 0.8;
    animation: sparkle 1s infinite;
}

/* Card Glow Effect */
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

.quizset-card:hover .tile-glow {
    background: radial-gradient(circle at center, var(--glow-green), transparent 70%);
    opacity: 0.3;
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
}

/* Loading State with Particles */
.loading-state {
    text-align: center;
    padding: 3rem 0;
    position: relative;
}

.loading-spinner {
    display: inline-block;
    width: 3rem;
    height: 3rem;
    border: 4px solid var(--loading-color);
    border-top-color: transparent;
    border-radius: 50%;
}

.loading-text {
    color: var(--text-primary);
    margin-top: 1rem;
    font-size: 1.125rem;
    transition: color 0.3s ease;
}

.loading-subtext {
    color: var(--text-tertiary);
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

.loading-particles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.loading-particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: var(--loading-color);
    border-radius: 50%;
    animation: loading-particle-float 3s infinite ease-in-out;
}

@keyframes loading-particle-float {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0;
    }
    50% {
        transform: translate(calc(var(--x) * 100px), calc(var(--y) * 100px)) scale(1.5);
        opacity: 0.5;
    }
}

/* Error State */
.error-state {
    text-align: center;
    padding: 2rem 0;
}

.error-content {
    background: var(--error-bg);
    border: 1px solid var(--error-border);
    border-radius: 0.75rem;
    padding: 1.5rem;
    max-width: 28rem;
    margin: 0 auto;
}

.error-icon {
    font-size: 2.25rem;
    margin-bottom: 1rem;
}

.error-title {
    color: var(--error-title);
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.error-message {
    color: var(--error-text);
    margin-bottom: 1rem;
}

.retry-btn {
    background: #ef4444;
    color: white;
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.retry-btn:hover {
    background: #dc2626;
    transform: scale(1.05);
}

.dark-theme .retry-btn {
    background: #dc2626;
}

.dark-theme .retry-btn:hover {
    background: #b91c1c;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 0;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--empty-icon-color);
    transition: color 0.3s ease;
}

.empty-title {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    transition: color 0.3s ease;
}

.empty-message {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    transition: color 0.3s ease;
}

.refresh-btn {
    background: #3b82f6;
    color: white;
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.refresh-btn:hover {
    background: #2563eb;
    transform: scale(1.05);
}

.dark-theme .refresh-btn {
    background: #60a5fa;
}

.dark-theme .refresh-btn:hover {
    background: #3b82f6;
}

/* Quiz Master Terminal */
.quiz-master-terminal {
    border-color: var(--cyber-green);
}

.quiz-master-terminal .terminal-body {
    color: var(--cyber-green);
}

/* Hidden Quiz Fact */
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

/* Quiz Particles */
.quiz-particle {
    position: absolute;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    animation: particle-burst 0.8s ease-out forwards;
    pointer-events: none;
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

/* ==================== */
/* ANIMATIONS           */
/* ==================== */

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

@keyframes tada {
    0% {
        transform: scale(1);
    }
    10%, 20% {
        transform: scale(0.9) rotate(-3deg);
    }
    30%, 50%, 70%, 90% {
        transform: scale(1.1) rotate(3deg);
    }
    40%, 60%, 80% {
        transform: scale(1.1) rotate(-3deg);
    }
    100% {
        transform: scale(1) rotate(0);
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

@keyframes matrix-fall {
    0% {
        transform: translateY(-100%);
    }
    100% {
        transform: translateY(100vh);
    }
}

@keyframes bubble-rise {
    0% {
        transform: translateY(0) scale(0.5);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100px) scale(1);
        opacity: 0;
    }
}

@keyframes particle-burst {
    0% {
        transform: translate(0, 0) scale(1);
        opacity: 1;
    }
    100% {
        transform: translate(var(--tx), var(--ty)) scale(0);
        opacity: 0;
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
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

.animate-spin-slow {
    animation: spin 3s linear infinite;
}

.animate-tada {
    animation: tada 1s ease-out;
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

.animate-gradient-shift {
    background-size: 200% 200%;
    animation: gradient-shift 3s ease infinite;
}

.animate-count-up {
    animation: slide-up 0.5s ease-out;
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

/* Staggered animations for cards */
.quizsets-grid > div:nth-child(1) { animation-delay: 0.1s; }
.quizsets-grid > div:nth-child(2) { animation-delay: 0.2s; }
.quizsets-grid > div:nth-child(3) { animation-delay: 0.3s; }
.quizsets-grid > div:nth-child(4) { animation-delay: 0.4s; }
.quizsets-grid > div:nth-child(5) { animation-delay: 0.5s; }
.quizsets-grid > div:nth-child(6) { animation-delay: 0.6s; }

/* Responsive Adjustments */
@media (max-width: 640px) {
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
    
    .hidden-message {
        left: 20px;
        right: 20px;
        max-width: none;
    }
    
    .nerd-terminal {
        width: calc(100% - 40px);
        left: 20px;
        bottom: 20px;
    }
}
</style>