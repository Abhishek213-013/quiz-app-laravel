<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex justify-center">
                    <i class="fas fa-brain text-4xl text-indigo-600"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Admin Login
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter your credentials to access the admin panel
                </p>
            </div>
            
            <form class="mt-8 space-y-6" @submit.prevent="login">
                <div v-if="hasErrors" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ errors.email || errors.password || 'Invalid credentials' }}
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input 
                            id="email" 
                            v-model="form.email"
                            type="email" 
                            required 
                            class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Enter your email"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input 
                            id="password" 
                            v-model="form.password"
                            type="password" 
                            required 
                            class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Enter your password"
                        >
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        :disabled="processing"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        <span v-if="processing">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Signing in...
                        </span>
                        <span v-else>
                            Sign in to Admin Panel
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const processing = ref(false)
const form = ref({
    email: '',
    password: ''
})

// Get errors from Inertia
const errors = computed(() => usePage().props.errors)
const hasErrors = computed(() => Object.keys(errors.value).length > 0)

const login = () => {
    processing.value = true
    
    router.post('/admin/login', form.value, {
        onSuccess: () => {
            processing.value = false
        },
        onError: (errors) => {
            processing.value = false
        },
        onFinish: () => {
            processing.value = false
        }
    })
}
</script>