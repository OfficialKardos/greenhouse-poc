<!-- resources/js/views/Login.vue -->
<template>
    <div
        class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-green-900 flex items-center justify-center p-4"
    >
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"
            ></div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"
            ></div>
            <div
                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-green-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"
            ></div>
        </div>

        <!-- Login Card -->
        <div class="relative w-full max-w-md">
            <div
                class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white/20"
            >
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div
                        class="inline-block p-4 bg-gradient-to-br from-green-500 to-green-600 rounded-3xl shadow-lg mb-4 animate-pulse"
                    >
                        <span class="text-5xl">🌿</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">GrowTrack</h1>
                    <p class="text-gray-500 mt-2">
                        Greenhouse Operations Management
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleLogin" class="space-y-6">
                    <div class="space-y-4">
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400"
                                >📧</span
                            >
                            <input
                                v-model="email"
                                type="email"
                                required
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-gray-50 transition-all"
                                placeholder="Email address"
                            />
                        </div>

                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400"
                                >🔒</span
                            >
                            <input
                                v-model="password"
                                type="password"
                                required
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-gray-50 transition-all"
                                placeholder="Password"
                            />
                        </div>
                    </div>

                    <div
                        v-if="error"
                        class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm"
                    >
                        {{ error }}
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-gradient-to-r from-green-600 to-green-500 text-white py-3 rounded-xl font-medium hover:from-green-700 hover:to-green-600 transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:hover:scale-100 shadow-lg shadow-green-500/25 relative overflow-hidden group"
                    >
                        <span
                            class="relative z-10 flex items-center justify-center"
                        >
                            <svg
                                v-if="loading"
                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            {{ loading ? "Signing in..." : "Sign In" }}
                        </span>
                        <div
                            class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
                        ></div>
                    </button>

                    <!-- Demo Credentials -->
                    <div
                        class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200"
                    >
                        <p class="text-xs text-gray-500 mb-3 flex items-center">
                            <span
                                class="w-2 h-2 bg-green-500 rounded-full mr-2"
                            ></span>
                            Demo Credentials
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="space-y-1">
                                <p class="font-medium text-gray-700">Admin</p>
                                <p class="text-gray-500 font-mono">
                                    admin@greenhouse.com
                                </p>
                                <p class="text-gray-500 font-mono">password</p>
                            </div>
                            <div class="space-y-1">
                                <p class="font-medium text-gray-700">Worker</p>
                                <p class="text-gray-500 font-mono">
                                    worker@greenhouse.com
                                </p>
                                <p class="text-gray-500 font-mono">password</p>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Footer -->
                <p class="text-center text-xs text-gray-400 mt-6">
                    © 2024 GrowTrack. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            email: "",
            password: "",
            loading: false,
            error: null,
        };
    },
    methods: {
        async handleLogin() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post("/login", {
                    email: this.email,
                    password: this.password,
                    device_name: "web",
                });

                const user = response.data.user;
                const token = response.data.token;

                if (user.can_access_admin) {
                    // Admin user - store token
                    localStorage.setItem("token", token);
                    localStorage.setItem("user", JSON.stringify(user));
                    axios.defaults.headers.common["Authorization"] =
                        `Bearer ${token}`;

                    // FORCE A FULL PAGE RELOAD - THIS IS KEY!
                    window.location.href = "/";
                } else if (user.can_access_mobile) {
                    // Mobile-only user
                    localStorage.removeItem("token");
                    localStorage.removeItem("user");
                    delete axios.defaults.headers.common["Authorization"];

                    try {
                        await axios.post(
                            "/logout",
                            {},
                            {
                                headers: { Authorization: `Bearer ${token}` },
                            },
                        );
                    } catch (e) {}

                    window.location.href = "/mobile/login";
                } else {
                    alert("Your account does not have access to any platform.");
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Login failed. Please check your credentials.";
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
