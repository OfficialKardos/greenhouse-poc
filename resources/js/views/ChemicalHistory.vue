<!-- resources/js/views/ChemicalHistory.vue -->
<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Chemical Application History</h1>
        <p class="text-gray-500 mt-1">Track and analyze chemical usage</p>
      </div>
      <button @click="exportReport" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2">
        <span>📥</span>
        <span>Export</span>
      </button>
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
          <label class="block text-sm font-medium mb-2">Chemical</label>
          <select v-model="filters.chemical" class="w-full border rounded-lg px-3 py-2">
            <option value="">All</option>
            <option v-for="chem in chemicals" :key="chem" :value="chem">{{ chem }}</option>
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
          <span class="text-gray-500">Total Applications</span>
          <span class="text-2xl">🧪</span>
        </div>
        <p class="text-3xl font-bold">{{ summary.total_applications }}</p>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-gray-500">Total Chemicals Used</span>
          <span class="text-2xl">🔬</span>
        </div>
        <p class="text-3xl font-bold">{{ Object.keys(summary.chemicals_used || {}).length }}</p>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-gray-500">Total Volume</span>
          <span class="text-2xl">💧</span>
        </div>
        <p class="text-3xl font-bold">{{ totalVolume }} oz</p>
      </div>
    </div>

    <!-- Chemical Breakdown -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-lg font-semibold">Chemical Breakdown</h2>
      </div>
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chemical</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applications</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Volume (oz)</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg per Application</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plants</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="(chem, name) in summary.chemicals_used" :key="name" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ name }}</td>
            <td class="px-6 py-4">{{ chem.applications }}</td>
            <td class="px-6 py-4">{{ chem.total_oz.toFixed(2) }}</td>
            <td class="px-6 py-4">{{ (chem.total_oz / chem.applications).toFixed(2) }}</td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
                <span v-for="plant in chem.plants" :key="plant" 
                      class="px-2 py-1 bg-gray-100 rounded text-xs">
                  {{ plant }}
                </span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Timeline Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4">Application Timeline</h2>
      <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
        <p class="text-gray-400">Chart component would go here</p>
      </div>
    </div>

    <!-- Detailed Entries -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-lg font-semibold">Detailed Entries</h2>
      </div>
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chemical</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plant</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="entry in entries" :key="entry.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">{{ formatDate(entry.submitted_at) }}</td>
            <td class="px-6 py-4 font-medium">{{ entry.job_number }}</td>
            <td class="px-6 py-4">{{ getFieldValue(entry, 'chemical_applied') }}</td>
            <td class="px-6 py-4">{{ getFieldValue(entry, 'plant') }}</td>
            <td class="px-6 py-4">{{ getFieldValue(entry, 'oz_per_100_gallons') || '0' }} oz</td>
            <td class="px-6 py-4">{{ entry.greenhouse?.name }} / {{ entry.bay?.name }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { format } from 'date-fns';

export default {
  data() {
    return {
      greenhouses: [],
      chemicals: [],
      filters: {
        greenhouse_id: '',
        chemical: '',
        from_date: '',
        to_date: ''
      },
      summary: {
        total_applications: 0,
        chemicals_used: {}
      },
      entries: []
    };
  },
  computed: {
    totalVolume() {
      let total = 0;
      Object.values(this.summary.chemicals_used || {}).forEach(chem => {
        total += chem.total_oz;
      });
      return total.toFixed(2);
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
        
        const response = await axios.get(`/reports/chemicals?${params}`);
        this.summary = response.data.data;
        this.entries = response.data.data.entries || [];
        
        // Extract unique chemicals for filter
        this.chemicals = Object.keys(this.summary.chemicals_used || {});
      } catch (error) {
        console.error('Failed to load report:', error);
      }
    },
    applyFilters() {
      this.loadData();
    },
    formatDate(date) {
      return format(new Date(date), 'MMM d, yyyy');
    },
    getFieldValue(entry, fieldName) {
      const value = entry.values?.find(v => v.job_field?.field_name === fieldName);
      return value?.value || '-';
    },
    exportReport() {
      const params = new URLSearchParams(this.filters);
      window.open(`/api/reports/export/chemicals?${params}&format=excel`, '_blank');
    }
  }
};
</script>