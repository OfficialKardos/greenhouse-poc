<template>
  <div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Total Samples</div>
        <div class="text-2xl font-bold">{{ data.summary?.total_samples || entries.length }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Unique Plants</div>
        <div class="text-2xl font-bold">{{ data.summary?.unique_plants || Object.keys(data.trends || {}).length }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">With Photos</div>
        <div class="text-2xl font-bold">{{ data.summary?.with_photos || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Avg pH</div>
        <div class="text-2xl font-bold">{{ averagePH.toFixed(2) }}</div>
      </div>
    </div>

    <!-- Plant Selection -->
    <div v-if="plants.length" class="bg-white rounded-lg shadow p-4">
      <div class="flex flex-wrap gap-2">
        <button v-for="plant in plants" :key="plant"
                @click="selectedPlant = plant"
                class="px-4 py-2 rounded-lg"
                :class="selectedPlant === plant ? 'bg-green-600 text-white' : 'bg-gray-100 hover:bg-gray-200'">
          {{ plant }}
        </button>
      </div>
    </div>

    <!-- Plant Statistics -->
    <div v-if="selectedPlant && statistics[selectedPlant]" class="bg-white rounded-lg shadow p-6">
      <h3 class="font-semibold mb-4">{{ selectedPlant }} - Statistics</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <div class="text-sm text-gray-500">Samples</div>
          <div class="text-xl font-bold">{{ statistics[selectedPlant].sample_count }}</div>
        </div>
        <div>
          <div class="text-sm text-gray-500">Avg pH</div>
          <div class="text-xl font-bold">{{ (statistics[selectedPlant].avg_ph || 0).toFixed(2) }}</div>
        </div>
        <div>
          <div class="text-sm text-gray-500">pH Range</div>
          <div class="text-sm font-medium">
            {{ (statistics[selectedPlant].min_ph || 0).toFixed(2) }} - 
            {{ (statistics[selectedPlant].max_ph || 0).toFixed(2) }}
          </div>
        </div>
        <div>
          <div class="text-sm text-gray-500">Avg EC</div>
          <div class="text-xl font-bold">{{ (statistics[selectedPlant].avg_ec || 0).toFixed(2) }}</div>
        </div>
      </div>
    </div>

    <!-- Samples Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 bg-gray-50 border-b">
        <h3 class="font-semibold">Soil Samples History</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plant</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Week</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">pH</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">EC</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photos</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">{{ formatDate(entry.submitted_at) }}</td>
              <td class="px-6 py-4 font-medium">{{ getFieldValue(entry, 'plant') || 'N/A' }}</td>
              <td class="px-6 py-4">Week {{ getFieldValue(entry, 'week') || 'N/A' }}</td>
              <td class="px-6 py-4">
                <span :class="getPHClass(getFieldValue(entry, 'ph'))">
                  {{ getFieldValue(entry, 'ph') || '—' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span :class="getECClass(getFieldValue(entry, 'ec'))">
                  {{ getFieldValue(entry, 'ec') || '—' }}
                </span>
              </td>
              <td class="px-6 py-4">{{ entry.greenhouse?.name || 'N/A' }} / {{ entry.bay?.name || 'N/A' }}</td>
              <td class="px-6 py-4">
                <div class="flex -space-x-2">
                  <div v-for="photo in entry.photos?.slice(0, 3)" :key="photo.id"
                       class="w-8 h-8 rounded-full border-2 border-white overflow-hidden cursor-pointer"
                       @click="$emit('view-photo', photo)">
                    <img :src="photo.thumbnail_url || photo.url" class="w-full h-full object-cover">
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { format } from 'date-fns';

export default {
  props: ['data'],
  data() {
    return {
      selectedPlant: null
    };
  },
  computed: {
    entries() {
      return this.data.entries || [];
    },
    trends() {
      return this.data.trends || {};
    },
    plants() {
      return this.data.plants || Object.keys(this.trends);
    },
    statistics() {
      return this.data.statistics || {};
    },
    averagePH() {
      const values = this.entries
        .map(e => parseFloat(this.getFieldValue(e, 'ph')))
        .filter(v => !isNaN(v));
      if (values.length === 0) return 0;
      return values.reduce((a, b) => a + b, 0) / values.length;
    }
  },
  mounted() {
    if (this.plants.length > 0) {
      this.selectedPlant = this.plants[0];
    }
  },
  methods: {
    formatDate(date) {
      return date ? format(new Date(date), 'MMM d, yyyy') : 'N/A';
    },
    getFieldValue(entry, fieldName) {
      const value = entry.values?.find(v => v.job_field?.field_name === fieldName);
      return value?.value || null;
    },
    getPHClass(ph) {
      if (!ph) return '';
      const val = parseFloat(ph);
      if (isNaN(val)) return '';
      if (val < 5.5) return 'text-red-600 font-medium';
      if (val > 7.5) return 'text-red-600 font-medium';
      if (val >= 6.0 && val <= 7.0) return 'text-green-600 font-medium';
      return 'text-yellow-600 font-medium';
    },
    getECClass(ec) {
      if (!ec) return '';
      const val = parseFloat(ec);
      if (isNaN(val)) return '';
      if (val < 0.5) return 'text-yellow-600 font-medium';
      if (val > 2.0) return 'text-red-600 font-medium';
      if (val >= 1.0 && val <= 1.5) return 'text-green-600 font-medium';
      return 'text-blue-600 font-medium';
    }
  }
};
</script>