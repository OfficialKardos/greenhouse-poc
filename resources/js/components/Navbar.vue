<!-- resources/js/components/Navbar.vue -->
<template>
  <aside class="w-80 bg-white border-r border-gray-200/80 flex flex-col shadow-2xl">
    <!-- Logo Area -->
    <div class="p-8 bg-gradient-to-br from-gray-50 to-white border-b border-gray-100">
      <div class="flex items-center space-x-4">
        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20">
          <span class="text-3xl text-white">🌿</span>
        </div>
        <div>
          <h1 class="text-2xl font-bold text-gray-800">GrowTrack</h1>
          <p class="text-sm text-gray-500">Greenhouse Operations</p>
        </div>
      </div>
    </div>

    <!-- User profile -->
    <div v-if="user" class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50/50 to-white">
      <div class="flex items-center space-x-4">
        <div class="w-14 h-14 bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-md">
          {{ userInitials }}
        </div>
        <div class="flex-1">
          <p class="font-semibold text-gray-800">{{ user.name }}</p>
          <div class="flex items-center mt-1">
            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
            <p class="text-sm text-gray-500 capitalize">{{ user.role }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation - modern with icons -->
    <nav class="flex-1 p-4 overflow-y-auto space-y-1">
      <!-- Dashboard -->
      <NavLink to="/" icon="📊" :active="$route.path === '/'">
        Dashboard
        <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Live</span>
      </NavLink>

      <!-- Operations Section -->
      <div class="pt-6 pb-2">
        <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4">Operations</p>
      </div>
      
      <NavLink to="/greenhouses" icon="🏢" :active="$route.path.startsWith('/greenhouses')">
        Greenhouses
        <span class="ml-auto text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ stats?.greenhouses || 0 }}</span>
      </NavLink>

      <NavLink to="/bays" icon="🧩" :active="$route.path.startsWith('/bays')">
        Bays
      </NavLink>

      <NavLink to="/job-types" icon="📋" :active="$route.path.startsWith('/job-types')">
        Job Types
      </NavLink>

      <NavLink to="/entries" icon="📝" :active="$route.path.startsWith('/entries')">
        Job Entries
      </NavLink>

      <!-- Configuration Section -->
      <div class="pt-6 pb-2">
        <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4">Configuration</p>
      </div>

      <NavLink to="/dropdowns" icon="🔽" :active="$route.path.startsWith('/dropdowns')">
        Dropdown Lists
      </NavLink>

      <!-- Analytics Section -->
      <div class="pt-6 pb-2">
        <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4">Analytics</p>
      </div>

      <NavLink to="/reports" icon="📈" :active="$route.path.startsWith('/reports')">
        Reports
      </NavLink>

      <!-- Submenu for reports -->
      <div v-if="$route.path.startsWith('/reports')" class="ml-12 mt-1 space-y-1">
        <router-link to="/reports/chemicals" class="block px-4 py-2 text-sm text-gray-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors">
          Chemical History
        </router-link>
        <router-link to="/reports/soil-trends" class="block px-4 py-2 text-sm text-gray-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors">
          Soil Trends
        </router-link>
        <router-link to="/reports/pest-incidents" class="block px-4 py-2 text-sm text-gray-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors">
          Pest Incidents
        </router-link>
      </div>

      <!-- Mobile App Section -->
      <div class="pt-6 pb-2">
        <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold px-4">Mobile</p>
      </div>

      <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group">
        <span class="text-xl group-hover:scale-110 transition-transform">📱</span>
        <span class="font-medium">Download App</span>
        <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">New</span>
      </a>
    </nav>

    <!-- Logout button -->
   <div class="p-4 border-t border-gray-100">
      <button @click="handleLogout" class="flex items-center space-x-3 px-4 py-3 w-full rounded-xl text-gray-600 hover:bg-red-50 hover:text-red-700 transition-all group">
        <span class="text-xl group-hover:scale-110 transition-transform">🚪</span>
        <span class="font-medium">Logout</span>
      </button>
    </div>
  </aside>
</template>

<script>
import NavLink from './NavLink.vue';

export default {
  components: { NavLink },
  props: {
    user: Object,
    stats: Object
  },
  computed: {
    userInitials() {
      if (!this.user) return '';
      return this.user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    }
  },
  methods: {
    handleLogout() {
      this.$emit('logout');
    }
  }
};
</script>