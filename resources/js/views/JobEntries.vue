<!-- resources/js/views/JobEntries.vue -->
<template>
  <div class="container mx-auto px-4 py-4 sm:px-6 sm:py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Job Entries</h1>
        <p class="text-gray-500 mt-1 text-sm sm:text-base">View all submitted jobs</p>
      </div>
      
      <!-- Mobile Filter Toggle -->
      <button 
        @click="showMobileFilters = !showMobileFilters" 
        class="sm:hidden bg-gray-100 text-gray-700 px-4 py-2 rounded-lg flex items-center justify-center space-x-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
        <span>Filters</span>
      </button>
    </div>

    <!-- Filters - Desktop -->
    <div class="hidden sm:block bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium mb-2">Greenhouse</label>
          <select v-model="filters.greenhouse_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">All</option>
            <option v-for="gh in greenhouses" :key="gh.id" :value="gh.id">{{ gh.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">Job Type</label>
          <select v-model="filters.job_type_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">All</option>
            <option v-for="jt in jobTypes" :key="jt.id" :value="jt.id">{{ jt.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">From Date</label>
          <input type="date" v-model="filters.from_date" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">To Date</label>
          <input type="date" v-model="filters.to_date" class="w-full border rounded-lg px-3 py-2">
        </div>
      </div>
      <div class="flex justify-end mt-4">
        <button @click="applyFilters" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
          Apply Filters
        </button>
      </div>
    </div>

    <!-- Filters - Mobile (Slide-down) -->
    <div v-if="showMobileFilters" class="sm:hidden bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100 animate-slideDown">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-2">Greenhouse</label>
          <select v-model="filters.greenhouse_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">All</option>
            <option v-for="gh in greenhouses" :key="gh.id" :value="gh.id">{{ gh.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">Job Type</label>
          <select v-model="filters.job_type_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">All</option>
            <option v-for="jt in jobTypes" :key="jt.id" :value="jt.id">{{ jt.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">From Date</label>
          <input type="date" v-model="filters.from_date" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium mb-2">To Date</label>
          <input type="date" v-model="filters.to_date" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div class="flex space-x-2 pt-2">
          <button @click="applyFilters" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Apply
          </button>
          <button @click="showMobileFilters = false" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- Entries - Mobile Card View -->
    <div class="sm:hidden space-y-4">
      <div v-for="entry in entries" :key="entry.id" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
          <span class="font-semibold text-gray-800">{{ entry.job_number }}</span>
          <span class="px-2 py-1 text-xs rounded-full"
                :class="{
                  'bg-green-100 text-green-700': entry.status === 'approved',
                  'bg-yellow-100 text-yellow-700': entry.status === 'submitted',
                  'bg-red-100 text-red-700': entry.status === 'rejected'
                }">
            {{ entry.status }}
          </span>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
          <div>
            <span class="text-gray-500 block text-xs">Date</span>
            <span class="font-medium">{{ formatDate(entry.submitted_at) }}</span>
          </div>
          <div>
            <span class="text-gray-500 block text-xs">Worker</span>
            <span class="font-medium">{{ entry.user?.name }}</span>
          </div>
          <div>
            <span class="text-gray-500 block text-xs">Job Type</span>
            <span class="font-medium">{{ entry.job_type?.name }}</span>
          </div>
          <div>
            <span class="text-gray-500 block text-xs">Location</span>
            <span class="font-medium">{{ entry.greenhouse?.name }}/{{ entry.bay?.name }}</span>
          </div>
        </div>

        <!-- Photos -->
        <div class="mb-3">
          <span class="text-gray-500 text-xs block mb-2">Photos</span>
          <div class="flex -space-x-2">
            <div v-for="photo in entry.photos?.slice(0, 3)" :key="photo.id"
                 class="w-10 h-10 rounded-full border-2 border-white overflow-hidden">
              <img :src="photo.thumbnail_url || photo.url" 
                   class="w-full h-full object-cover"
                   :title="photo.filename">
            </div>
            <div v-if="entry.photos?.length > 3" 
                 class="w-10 h-10 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs font-medium text-gray-600">
              +{{ entry.photos.length - 3 }}
            </div>
            <div v-if="!entry.photos?.length" class="text-gray-400 text-xs">
              No photos
            </div>
          </div>
        </div>

        <!-- Action -->
        <router-link :to="`/entries/${entry.id}`" 
                   class="block w-full text-center bg-green-50 text-green-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors">
          View Details
        </router-link>
      </div>
    </div>

    <!-- Entries - Desktop Table -->
    <div class="hidden sm:block bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job #</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photos</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap font-medium">{{ entry.job_number }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(entry.submitted_at) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ entry.user?.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ entry.job_type?.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ entry.greenhouse?.name }} / {{ entry.bay?.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex -space-x-2">
                  <div v-for="photo in entry.photos?.slice(0, 3)" :key="photo.id"
                       class="w-8 h-8 rounded-full border-2 border-white overflow-hidden">
                    <img :src="photo.thumbnail_url || photo.url" 
                         class="w-full h-full object-cover"
                         :title="photo.filename">
                  </div>
                  <div v-if="entry.photos?.length > 3" 
                       class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs text-gray-600">
                    +{{ entry.photos.length - 3 }}
                  </div>
                  <div v-if="!entry.photos?.length" class="text-gray-400 text-sm">
                    No photos
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full"
                      :class="{
                        'bg-green-100 text-green-700': entry.status === 'approved',
                        'bg-yellow-100 text-yellow-700': entry.status === 'submitted',
                        'bg-red-100 text-red-700': entry.status === 'rejected'
                      }">
                  {{ entry.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <router-link :to="`/entries/${entry.id}`" 
                           class="text-green-600 hover:text-green-700 mr-3">
                  View Details
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="entries.length === 0" class="text-center py-12">
      <div class="text-6xl text-gray-300 mb-4">📋</div>
      <h3 class="text-lg font-medium text-gray-700 mb-2">No entries found</h3>
      <p class="text-gray-500">Try adjusting your filters</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { format } from 'date-fns';

export default {
  data() {
    return {
      entries: [],
      greenhouses: [],
      jobTypes: [],
      filters: {
        greenhouse_id: '',
        job_type_id: '',
        from_date: '',
        to_date: ''
      },
      showMobileFilters: false
    };
  },
  mounted() {
    this.loadFilters();
    this.loadEntries();
  },
  methods: {
    async loadFilters() {
      const [ghRes, jtRes] = await Promise.all([
        axios.get('/greenhouses'),
        axios.get('/job-types')
      ]);
      this.greenhouses = ghRes.data.data;
      this.jobTypes = jtRes.data.data;
    },
    async loadEntries() {
      const params = new URLSearchParams();
      Object.entries(this.filters).forEach(([k, v]) => v && params.append(k, v));
      
      const response = await axios.get(`/job-entries?${params}`);
      this.entries = response.data.data;
      
      // Auto-hide mobile filters after applying
      this.showMobileFilters = false;
    },
    applyFilters() {
      this.loadEntries();
    },
    formatDate(date) {
      return format(new Date(date), 'MMM d, yyyy');
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