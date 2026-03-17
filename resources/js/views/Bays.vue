<!-- resources/js/views/Bays.vue -->
<template>
  <div class="container mx-auto px-4 py-4 sm:px-6 sm:py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Bays</h1>
        <p class="text-gray-500 mt-1 text-sm sm:text-base">Manage greenhouse bays and sections</p>
      </div>
      <router-link to="/bays/create" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center justify-center space-x-2 w-full sm:w-auto">
        <span class="text-xl">➕</span>
        <span>Add Bay</span>
      </router-link>
    </div>

    <!-- Filters - Desktop -->
    <div class="hidden sm:block bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Greenhouse</label>
          <select v-model="filters.greenhouse_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">All Greenhouses</option>
            <option v-for="gh in greenhouses" :key="gh.id" :value="gh.id">{{ gh.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input type="text" v-model="search" placeholder="Search bays..." 
                 class="w-full border rounded-lg px-3 py-2">
        </div>
      </div>
    </div>

    <!-- Mobile Filter Toggle -->
    <div class="sm:hidden mb-4">
      <button 
        @click="showMobileFilters = !showMobileFilters" 
        class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-lg flex items-center justify-center space-x-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round"  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <span>{{ showMobileFilters ? 'Hide Filters' : 'Show Filters' }}</span>
      </button>
    </div>

    <!-- Mobile Filters -->
    <div v-if="showMobileFilters" class="sm:hidden bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100 animate-slideDown">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Greenhouse</label>
          <select v-model="filters.greenhouse_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">All Greenhouses</option>
            <option v-for="gh in greenhouses" :key="gh.id" :value="gh.id">{{ gh.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input type="text" v-model="search" placeholder="Search bays..." 
                 class="w-full border rounded-lg px-3 py-2">
        </div>
      </div>
    </div>

    <!-- Bays - Mobile Card View -->
    <div class="sm:hidden space-y-4">
      <div v-for="bay in filteredBays" :key="bay.id" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-gray-800">{{ bay.name }}</h3>
          <div class="flex space-x-2">
            <router-link :to="`/bays/${bay.id}/edit`" class="text-blue-600 hover:text-blue-800 p-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
            </router-link>
            <button @click="confirmDelete(bay)" class="text-red-600 hover:text-red-800 p-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-3 text-sm">
          <div>
            <span class="text-gray-500 block text-xs">Greenhouse</span>
            <span class="font-medium">{{ bay.greenhouse?.name || '-' }}</span>
          </div>
          <div>
            <span class="text-gray-500 block text-xs">Capacity</span>
            <span class="font-medium">{{ bay.capacity || '-' }}</span>
          </div>
          <div class="col-span-2">
            <span class="text-gray-500 block text-xs">Description</span>
            <span class="text-gray-700">{{ bay.description || 'No description' }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Bays - Desktop Table -->
    <div class="hidden sm:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Greenhouse</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="bay in filteredBays" :key="bay.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap font-medium">{{ bay.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ bay.greenhouse?.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ bay.description || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ bay.capacity || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex space-x-3">
                  <router-link :to="`/bays/${bay.id}/edit`" class="text-blue-600 hover:text-blue-800" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round"  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                  </router-link>
                  <button @click="confirmDelete(bay)" class="text-red-600 hover:text-red-800" title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round"  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredBays.length === 0" class="text-center py-12">
      <div class="text-6xl text-gray-300 mb-4">🧩</div>
      <h3 class="text-lg font-medium text-gray-700 mb-2">No bays found</h3>
      <p class="text-gray-500 mb-6">Try adjusting your filters or create a new bay</p>
      <router-link to="/bays/create" 
                   class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
        <span class="mr-2">➕</span>
        Add Your First Bay
      </router-link>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 p-4">
      <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Delete</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete bay "{{ deletingBay?.name }}"? This action cannot be undone.</p>
        <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
          <button @click="showDeleteModal = false"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 w-full sm:w-auto">
            Cancel
          </button>
          <button @click="deleteBay"
                  class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 w-full sm:w-auto">
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
      bays: [],
      greenhouses: [],
      filters: { greenhouse_id: '' },
      search: '',
      showMobileFilters: false,
      showDeleteModal: false,
      deletingBay: null
    };
  },
  computed: {
    filteredBays() {
      return this.bays.filter(bay => {
        if (this.filters.greenhouse_id && bay.greenhouse_id != this.filters.greenhouse_id) return false;
        if (this.search && !bay.name.toLowerCase().includes(this.search.toLowerCase())) return false;
        return true;
      });
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    async loadData() {
      const [baysRes, greenhousesRes] = await Promise.all([
        axios.get('/bays'),
        axios.get('/greenhouses')
      ]);
      this.bays = baysRes.data.data;
      this.greenhouses = greenhousesRes.data.data;
    },
    confirmDelete(bay) {
      this.deletingBay = bay;
      this.showDeleteModal = true;
    },
    async deleteBay() {
      await axios.delete(`/bays/${this.deletingBay.id}`);
      this.bays = this.bays.filter(b => b.id !== this.deletingBay.id);
      this.showDeleteModal = false;
      this.deletingBay = null;
    }
  }
};
</script>

<style scoped>
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slideDown {
  animation: slideDown 0.2s ease-out;
}
</style>