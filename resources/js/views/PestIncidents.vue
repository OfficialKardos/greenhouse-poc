<!-- resources/js/views/PestIncidents.vue -->
<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Pest & Disease Incidents</h1>
        <p class="text-gray-500 mt-1">Track and monitor pest issues</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
      <div class="grid grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium mb-2">Greenhouse</label>
          <select v-model="filters.greenhouse_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">All</option>
            <option v-for="gh in greenhouses" :key="gh.id" :value="gh.id">{{ gh.name }}</option>
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
        <button @click="applyFilters" class="bg-green-600 text-white px-4 py-2 rounded-lg">Apply Filters</button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-gray-500">Total Incidents</span>
          <span class="text-2xl">🐛</span>
        </div>
        <p class="text-3xl font-bold">{{ incidents.length }}</p>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-gray-500">Affected Bays</span>
          <span class="text-2xl">🧩</span>
        </div>
        <p class="text-3xl font-bold">{{ uniqueBays }}</p>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-gray-500">Last 7 Days</span>
          <span class="text-2xl">📅</span>
        </div>
        <p class="text-3xl font-bold">{{ last7Days }}</p>
      </div>
    </div>

    <!-- Heat Map by Location -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4">Incidents by Location</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div v-for="(count, location) in locationHeatmap" :key="location" 
             class="p-4 rounded-lg text-center"
             :style="{ backgroundColor: getHeatmapColor(count) }">
          <p class="font-medium">{{ location }}</p>
          <p class="text-2xl font-bold">{{ count }}</p>
        </div>
      </div>
    </div>

    <!-- Incidents Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-lg font-semibold">Incident Reports</h2>
      </div>
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plant</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Moisture</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reported By</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="incident in incidents" :key="incident.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">{{ incident.date }}</td>
            <td class="px-6 py-4 font-medium">{{ incident.job_number }}</td>
            <td class="px-6 py-4">{{ incident.greenhouse }} / {{ incident.bay }}</td>
            <td class="px-6 py-4">{{ incident.plant || 'N/A' }}</td>
            <td class="px-6 py-4">{{ incident.moisture_level || 'N/A' }}</td>
            <td class="px-6 py-4">{{ incident.user }}</td>
            <td class="px-6 py-4">
              <router-link :to="`/entries/${incident.id}`" class="text-blue-600 hover:text-blue-800">
                View Details
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      greenhouses: [],
      incidents: [],
      filters: {
        greenhouse_id: '',
        from_date: '',
        to_date: ''
      }
    };
  },
  computed: {
    uniqueBays() {
      const bays = new Set(this.incidents.map(i => `${i.greenhouse}-${i.bay}`));
      return bays.size;
    },
    last7Days() {
      const weekAgo = new Date();
      weekAgo.setDate(weekAgo.getDate() - 7);
      return this.incidents.filter(i => new Date(i.date) >= weekAgo).length;
    },
    locationHeatmap() {
      const heatmap = {};
      this.incidents.forEach(i => {
        const loc = `${i.greenhouse} - ${i.bay}`;
        heatmap[loc] = (heatmap[loc] || 0) + 1;
      });
      return heatmap;
    }
  },
  mounted() {
    this.loadGreenhouses();
    this.loadData();
  },
  methods: {
    async loadGreenhouses() {
      try {
        const response = await axios.get('/greenhouses');
        this.greenhouses = response.data.data;
      } catch (error) {
        console.error('Failed to load greenhouses:', error);
      }
    },
    async loadData() {
      try {
        const params = new URLSearchParams();
        Object.entries(this.filters).forEach(([k, v]) => v && params.append(k, v));
        
        const response = await axios.get(`/reports/pest-incidents?${params}`);
        this.incidents = response.data.data;
      } catch (error) {
        console.error('Failed to load pest incidents:', error);
      }
    },
    applyFilters() {
      this.loadData();
    },
    getHeatmapColor(count) {
      if (count === 0) return '#f3f4f6';
      if (count === 1) return '#fee2e2';
      if (count === 2) return '#fecaca';
      if (count === 3) return '#fca5a5';
      if (count === 4) return '#f87171';
      return '#ef4444';
    }
  }
};
</script>