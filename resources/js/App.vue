<!-- resources/js/App.vue -->
<template>
  <div
    :key="componentKey + '-' + renderKey"
    id="app"
    class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50"
  >
    <!-- Loading overlay -->
    <div
      v-if="loading"
      class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm z-50 flex items-center justify-center"
    >
      <div
        class="bg-white/90 backdrop-blur rounded-2xl p-8 shadow-2xl border border-gray-200/50"
      >
        <div
          class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-green-600"
        ></div>
        <span class="text-gray-700 mt-4 block font-medium">Loading...</span>
      </div>
    </div>

    <!-- Show authenticated layout only when actually authenticated -->
    <div v-if="isAuthenticated" class="flex h-screen bg-gray-50">
      <!-- Mobile Overlay -->
      <div
        v-if="isMobileMenuOpen"
        class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"
        @click="isMobileMenuOpen = false"
      ></div>

      <!-- Navbar - Responsive Sidebar -->
      <aside
        :class="[
          'bg-white border-r border-gray-200/80 flex flex-col shadow-2xl transition-all duration-300 z-30',
          isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full',
          'lg:translate-x-0 lg:w-80 fixed lg:relative h-full w-72'
        ]"
      >
        <!-- Close button for mobile -->
        <button
          @click="isMobileMenuOpen = false"
          class="absolute top-4 right-4 lg:hidden text-gray-500 hover:text-gray-700"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Logo Area -->
        <div
          class="p-8 bg-gradient-to-br from-gray-50 to-white border-b border-gray-100"
        >
          <div class="flex items-center space-x-4">
            <div
              class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20"
            >
              <span class="text-3xl text-white">🌿</span>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-800">GrowTrack</h1>
              <p class="text-sm text-gray-500">Greenhouse Operations</p>
            </div>
          </div>
        </div>

        <!-- User profile -->
        <div
          v-if="user"
          class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50/50 to-white"
        >
          <div class="flex items-center space-x-4">
            <div
              class="w-14 h-14 bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-md"
            >
              {{ userInitials }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-gray-800 truncate">{{ user.name }}</p>
              <div class="flex items-center mt-1">
                <span
                  class="w-2 h-2 bg-green-500 rounded-full mr-2 flex-shrink-0"
                ></span>
                <p class="text-sm text-gray-500 capitalize truncate">
                  {{ user.role }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation - Scrollable -->
        <nav class="flex-1 p-4 overflow-y-auto space-y-1">
          <!-- Dashboard -->
          <router-link
            to="/"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
            :class="{
              'bg-green-50 text-green-700 shadow-sm': $route.path === '/',
            }"
            @click="isMobileMenuOpen = false"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >📊</span
            >
            <span class="font-medium truncate">Dashboard</span>
            <span
              class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full flex-shrink-0"
              >Live</span
            >
          </router-link>

          <!-- Operations Section -->
          <div class="pt-6 pb-2">
            <p
              class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4 truncate"
            >
              Operations
            </p>
          </div>

          <router-link
            to="/greenhouses"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
            :class="{
              'bg-green-50 text-green-700 shadow-sm': $route.path.startsWith('/greenhouses'),
            }"
            @click="isMobileMenuOpen = false"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >🏢</span
            >
            <span class="font-medium truncate">Greenhouses</span>
            <span
              class="ml-auto text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full flex-shrink-0"
              >{{ stats.greenhouses }}</span
            >
          </router-link>

          <router-link
            to="/bays"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
            :class="{
              'bg-green-50 text-green-700 shadow-sm': $route.path.startsWith('/bays'),
            }"
            @click="isMobileMenuOpen = false"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >🧩</span
            >
            <span class="font-medium truncate">Bays</span>
          </router-link>

          <router-link
            to="/job-types"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
            :class="{
              'bg-green-50 text-green-700 shadow-sm': $route.path.startsWith('/job-types'),
            }"
            @click="isMobileMenuOpen = false"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >📋</span
            >
            <span class="font-medium truncate">Job Types</span>
          </router-link>

          <router-link
            to="/entries"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
            :class="{
              'bg-green-50 text-green-700 shadow-sm': $route.path.startsWith('/entries'),
            }"
            @click="isMobileMenuOpen = false"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >📝</span
            >
            <span class="font-medium truncate">Job Entries</span>
          </router-link>

          <!-- Configuration Section -->
          <div class="pt-6 pb-2">
            <p
              class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4 truncate"
            >
              Configuration
            </p>
          </div>

          <router-link
            to="/dropdowns"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
            :class="{
              'bg-green-50 text-green-700 shadow-sm': $route.path.startsWith('/dropdowns'),
            }"
            @click="isMobileMenuOpen = false"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >🔽</span
            >
            <span class="font-medium truncate">Dropdown Lists</span>
          </router-link>

          <div v-if="user?.role === 'admin' || user?.role === 'supervisor'">
            <div class="pt-6 pb-2">
              <p
                class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4 truncate"
              >
                Administration
              </p>
            </div>

            <router-link
              to="/users"
              class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
              :class="{
                'bg-green-50 text-green-700 shadow-sm': $route.path.startsWith('/users'),
              }"
              @click="isMobileMenuOpen = false"
            >
              <span
                class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
                >👥</span
              >
              <span class="font-medium truncate">User Management</span>
              <span
                class="ml-auto text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full flex-shrink-0"
                >Admin</span
              >
            </router-link>
          </div>

          <!-- Analytics Section -->
          <div class="pt-6 pb-2">
            <p
              class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4 truncate"
            >
              Analytics
            </p>
          </div>

          <router-link
            to="/reports"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
            :class="{
              'bg-green-50 text-green-700 shadow-sm': $route.path.startsWith('/reports'),
            }"
            @click="isMobileMenuOpen = false"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >📈</span
            >
            <span class="font-medium truncate">Reports</span>
          </router-link>

          <!-- Mobile App Section -->
          <!-- <div class="pt-6 pb-2">
            <p
              class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4 truncate"
            >
              Mobile
            </p>
          </div> -->

          <!-- <a
            href="#"
            class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >📱</span
            >
            <span class="font-medium truncate">Download App</span>
            <span
              class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full flex-shrink-0"
              >New</span
            >
          </a> -->
        </nav>

        <!-- Logout button -->
        <div class="p-4 border-t border-gray-100">
          <button
            @click="handleLogout"
            class="flex items-center space-x-3 px-4 py-3 w-full rounded-xl text-gray-600 hover:bg-red-50 hover:text-red-700 transition-all group"
          >
            <span
              class="text-xl group-hover:scale-110 transition-transform flex-shrink-0"
              >🚪</span
            >
            <span class="font-medium truncate">Logout</span>
          </button>
        </div>
      </aside>

      <!-- Main content area -->
      <main class="flex-1 overflow-auto bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Top Bar with Hamburger -->
        <div
          class="sticky top-0 z-10 bg-white/80 backdrop-blur-sm border-b border-gray-200/50 px-4 sm:px-8 py-4 flex items-center justify-between"
        >
          <div class="flex items-center space-x-4">
            <!-- Hamburger menu button (visible only on mobile) -->
            <button
              @click="isMobileMenuOpen = true"
              class="lg:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <h2 class="text-xl font-semibold text-gray-800 truncate">
              {{ currentPageTitle }}
            </h2>
          </div>

          <!-- <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- Search bar - hidden on mobile, visible on tablet/desktop
            <div class="relative hidden sm:block">
              <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
              <input
                type="text"
                placeholder="Search..."
                class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-gray-50 w-48 lg:w-64"
              />
            </div>

            <button class="sm:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>

            <button
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors relative"
            >
              <span class="text-xl">🔔</span>
              <span
                class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"
              ></span>
            </button>

            <button
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <span class="text-xl">⚙️</span>
            </button>
          </div> -->
        </div>

        <!-- Page content with responsive padding -->
        <div class="p-4 sm:p-6 lg:p-8">
          <router-view :user="user" />
        </div>
      </main>
    </div>

    <!-- Public layout (login only) -->
    <div v-else class="min-h-screen">
      <router-view />
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      loading: false,
      user: null,
      stats: {
        greenhouses: 0,
        bays: 0,
        jobTypes: 0,
        todayEntries: 0,
      },
      componentKey: 0,
      renderKey: 0,
      isMobileMenuOpen: false, // Controls mobile menu visibility
    };
  },
  computed: {
    isAuthenticated() {
      const token = localStorage.getItem("token");
      return !!token;
    },
    userInitials() {
      if (!this.user) return "";
      return this.user.name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
    },
    currentPageTitle() {
      const path = this.$route.path;
      const titles = {
        "/": "Dashboard",
        "/greenhouses": "Greenhouses",
        "/bays": "Bays",
        "/job-types": "Job Types",
        "/dropdowns": "Dropdown Lists",
        "/reports": "Reports & Analytics",
        "/reports/chemicals": "Chemical History",
        "/reports/soil-trends": "Soil pH & EC Trends",
        "/reports/pest-incidents": "Pest Incidents",
        "/entries": "Job Entries",
        "/users": "User Management",
      };
      return titles[path] || "Greenhouse Management";
    },
  },
  watch: {
    isAuthenticated: {
      handler(newVal) {
        if (newVal) {
          this.loadUser();
          this.loadStats();
        } else {
          this.user = null;
          this.stats = {
            greenhouses: 0,
            bays: 0,
            jobTypes: 0,
            todayEntries: 0,
          };
        }
        this.componentKey++;
      },
      immediate: true,
    },
    $route() {
      if (this.isAuthenticated) {
        this.loadStats();
      }
      // Close mobile menu on route change
      this.isMobileMenuOpen = false;
      this.$nextTick(() => {
        this.forceRerender();
      });
    },
  },
  mounted() {
    // Kill any Vite scripts
    const killVite = () => {
      document.querySelectorAll('script[src*=":5173"]').forEach((el) => el.remove());
      document.querySelectorAll('link[href*=":5173"]').forEach((el) => el.remove());
    };
    killVite();
    setTimeout(killVite, 50);

    this.loadUser();

    // Check if current user has admin access
    const user = JSON.parse(localStorage.getItem("user") || "{}");
    const token = localStorage.getItem("token");

    if (token && user && !user.can_access_admin) {
      console.log("User lacks admin access, clearing session");
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      delete axios.defaults.headers.common["Authorization"];

      if (user.can_access_mobile) {
        window.location.href = "/mobile/login";
      } else {
        this.$router.push("/login");
      }
    }

    // Close mobile menu on window resize above lg breakpoint
    window.addEventListener('resize', this.handleResize);
  },
  beforeUnmount() {
    window.removeEventListener("storage", this.handleStorageChange);
    window.removeEventListener('resize', this.handleResize);
  },
  methods: {
    checkAuth() {
      if (this.isAuthenticated) {
        this.loadUser();
        this.loadStats();
      }
    },
    handleResize() {
      // Close mobile menu when window is resized to lg size (1024px)
      if (window.innerWidth >= 1024) {
        this.isMobileMenuOpen = false;
      }
    },
    handleStorageChange(e) {
      if (e.key === "token" && !e.newValue) {
        this.user = null;
        this.stats = {
          greenhouses: 0,
          bays: 0,
          jobTypes: 0,
          todayEntries: 0,
        };
        this.componentKey++;
        this.$router.push("/login");
      }
    },
    loadUser() {
      const userStr = localStorage.getItem("user");
      if (userStr) {
        this.user = JSON.parse(userStr);
      }
    },
    async loadStats() {
      if (!this.isAuthenticated) return;

      try {
        const [greenhouses, bays, jobTypes] = await Promise.all([
          axios.get("/greenhouses").catch(() => ({ data: { data: [] } })),
          axios.get("/bays").catch(() => ({ data: { data: [] } })),
          axios.get("/job-types").catch(() => ({ data: { data: [] } })),
        ]);

        this.stats.greenhouses = greenhouses.data.data?.length || 0;
        this.stats.bays = bays.data.data?.length || 0;
        this.stats.jobTypes = jobTypes.data.data?.length || 0;

        const today = new Date().toISOString().split("T")[0];
        const entriesResponse = await axios
          .get(`/job-entries?from_date=${today}&to_date=${today}`)
          .catch(() => ({ data: { data: [] } }));
        this.stats.todayEntries = entriesResponse.data.data?.length || 0;
      } catch (error) {
        console.error("Failed to load stats:", error);
      }
    },
    async handleLogout() {
      this.loading = true;
      try {
        await axios.post("/logout");
      } catch (error) {
        console.error("Logout error:", error);
      } finally {
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        delete axios.defaults.headers.common["Authorization"];

        this.user = null;
        this.stats = {
          greenhouses: 0,
          bays: 0,
          jobTypes: 0,
          todayEntries: 0,
        };

        this.loading = false;
        this.componentKey++;
        window.location.href = "/login";
      }
    },
    forceRerender() {
      this.renderKey += 1;
    },
  },
};
</script>

<style>
/* Smooth scrolling */
* {
  scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Mobile menu transitions */
.sidebar-transition-enter-active,
.sidebar-transition-leave-active {
  transition: transform 0.3s ease;
}

.sidebar-transition-enter-from,
.sidebar-transition-leave-to {
  transform: translateX(-100%);
}

/* Ensure content doesn't shift when menu opens on mobile */
@media (max-width: 1023px) {
  .overflow-hidden {
    overflow: hidden;
  }
}
</style>