<template>
  <div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Total Incidents</div>
        <div class="text-2xl font-bold">{{ data.total || incidents.length }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">With Photos</div>
        <div class="text-2xl font-bold">{{ data.with_photos || incidents.filter(i => i.has_photos).length }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Affected Locations</div>
        <div class="text-2xl font-bold">{{ data.unique_locations || Object.keys(data.location_heatmap || {}).length }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Last 7 Days</div>
        <div class="text-2xl font-bold">{{ last7Days }}</div>
      </div>
    </div>

    <!-- Heatmap -->
    <div v-if="Object.keys(locationHeatmap).length" class="bg-white rounded-lg shadow p-6">
      <h3 class="font-semibold mb-4">Incidents by Location</h3>
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
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 bg-gray-50 border-b">
        <h3 class="font-semibold">Pest Incident Reports</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job #</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plant</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Moisture</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wilting</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photos</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Explanation</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="incident in incidents" :key="incident.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">{{ incident.date }}</td>
              <td class="px-6 py-4 font-medium">{{ incident.job_number }}</td>
              <td class="px-6 py-4">{{ incident.user }}</td>
              <td class="px-6 py-4">{{ incident.greenhouse }}/{{ incident.bay }}</td>
              <td class="px-6 py-4">{{ incident.plant }}</td>
              <td class="px-6 py-4">{{ incident.moisture }}</td>
              <td class="px-6 py-4">{{ incident.wilting ? 'Yes' : 'No' }}</td>
              <td class="px-6 py-4">
                <div class="flex -space-x-2">
                  <div v-for="photo in incident.photos?.slice(0, 3)" :key="photo.id"
                       class="w-8 h-8 rounded-full border-2 border-white overflow-hidden cursor-pointer"
                       @click="$emit('view-photo', photo)">
                    <img :src="photo.thumbnail_url || photo.url" class="w-full h-full object-cover">
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 max-w-xs truncate">{{ incident.explanation || '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { subDays } from 'date-fns';

export default {
  props: ['data'],
  computed: {
    incidents() {
      return this.data.incidents || [];
    },
    locationHeatmap() {
      return this.data.location_heatmap || {};
    },
    last7Days() {
      const weekAgo = subDays(new Date(), 7);
      return this.incidents.filter(i => new Date(i.date) >= weekAgo).length || 0;
    }
  },
  methods: {
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