<!-- resources/js/views/Greenhouses.vue -->
<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Greenhouses</h1>
        <p class="text-gray-500 mt-1">Manage your greenhouse locations</p>
      </div>
      <router-link to="/greenhouses/create" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2">
        <span>➕</span>
        <span>Add Greenhouse</span>
      </router-link>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
      <div class="flex items-center space-x-4">
        <div class="flex-1 relative">
          <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
          <input type="text" v-model="search" placeholder="Search greenhouses..." 
                 class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <select v-model="sortBy" class="border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="name">Sort by Name</option>
          <option value="created_at">Sort by Date</option>
          <option value="bays_count">Sort by Bays</option>
        </select>
      </div>
    </div>

    <!-- Greenhouses Grid -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
    </div>

    <div v-else-if="filteredGreenhouses.length === 0" class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
      <span class="text-6xl mb-4 block">🏢</span>
      <h3 class="text-xl font-medium text-gray-700 mb-2">No greenhouses found</h3>
      <p class="text-gray-500 mb-6">Get started by creating your first greenhouse</p>
      <router-link to="/greenhouses/create" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 inline-block">
        Add Greenhouse
      </router-link>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="greenhouse in filteredGreenhouses" :key="greenhouse.id" 
           class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
        <div class="h-32 bg-gradient-to-r from-green-600 to-green-700 relative">
          <div class="absolute top-4 right-4 flex space-x-2">
            <router-link :to="`/greenhouses/${greenhouse.id}/edit`" 
                       class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center hover:bg-opacity-30 transition-colors">
              <span class="text-white">✏️</span>
            </router-link>
            <button @click="confirmDelete(greenhouse)" 
                    class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center hover:bg-opacity-30 transition-colors">
              <span class="text-white">🗑️</span>
            </button>
          </div>
          <div class="absolute -bottom-8 left-6">
            <div class="w-16 h-16 bg-white rounded-xl shadow-lg flex items-center justify-center text-3xl">
              🏢
            </div>
          </div>
        </div>
        
        <div class="pt-10 p-6">
          <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ greenhouse.name }}</h3>
          <p class="text-gray-500 text-sm mb-4">{{ greenhouse.full_address || 'No address provided' }}</p>
          
          <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center space-x-4">
              <div class="flex items-center space-x-1">
                <span class="text-gray-400">🧩</span>
                <span class="text-sm text-gray-600">{{ greenhouse.bays?.length || 0 }} Bays</span>
              </div>
              <div class="flex items-center space-x-1">
                <span class="text-gray-400">📝</span>
                <span class="text-sm text-gray-600">{{ greenhouse.job_entries_count || 0 }} Jobs</span>
              </div>
            </div>
            <router-link :to="`/greenhouses/${greenhouse.id}`" class="text-green-600 hover:text-green-700 text-sm font-medium">
              View Details →
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
      <div class="bg-white rounded-xl p-6 max-w-md w-full">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Delete Greenhouse</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete "{{ deletingGreenhouse?.name }}"? This action cannot be undone.</p>
        <div class="flex justify-end space-x-4">
          <button @click="showDeleteModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button @click="deleteGreenhouse" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      greenhouses: [],
      loading: true,
      search: '',
      sortBy: 'name',
      showDeleteModal: false,
      deletingGreenhouse: null
    };
  },
  computed: {
    filteredGreenhouses() {
      let filtered = this.greenhouses;
      
      if (this.search) {
        const searchLower = this.search.toLowerCase();
        filtered = filtered.filter(g => 
          g.name.toLowerCase().includes(searchLower) ||
          (g.city && g.city.toLowerCase().includes(searchLower))
        );
      }
      
      return filtered.sort((a, b) => {
        if (this.sortBy === 'name') return a.name.localeCompare(b.name);
        if (this.sortBy === 'created_at') return new Date(b.created_at) - new Date(a.created_at);
        if (this.sortBy === 'bays_count') return (b.bays?.length || 0) - (a.bays?.length || 0);
        return 0;
      });
    }
  },
  mounted() {
    this.loadGreenhouses();
  },
  methods: {
    async loadGreenhouses() {
      this.loading = true;
      try {
        const response = await axios.get('/greenhouses');
        this.greenhouses = response.data.data;
      } catch (error) {
        console.error('Failed to load greenhouses:', error);
      } finally {
        this.loading = false;
      }
    },
    confirmDelete(greenhouse) {
      this.deletingGreenhouse = greenhouse;
      this.showDeleteModal = true;
    },
    async deleteGreenhouse() {
      try {
        await axios.delete(`/greenhouses/${this.deletingGreenhouse.id}`);
        this.greenhouses = this.greenhouses.filter(g => g.id !== this.deletingGreenhouse.id);
        this.showDeleteModal = false;
        this.deletingGreenhouse = null;
      } catch (error) {
        console.error('Failed to delete greenhouse:', error);
      }
    }
  }
};
</script>