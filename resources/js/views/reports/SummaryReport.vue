<template>
  <div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Total Entries</div>
        <div class="text-2xl font-bold">{{ data.summary?.total_entries || entries.length }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">With Photos</div>
        <div class="text-2xl font-bold">{{ data.summary?.with_photos || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Unique Workers</div>
        <div class="text-2xl font-bold">{{ data.summary?.unique_workers || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Date Range</div>
        <div class="text-sm font-medium">
          {{ data.summary?.date_range?.from || 'All' }} to {{ data.summary?.date_range?.to || 'All' }}
        </div>
      </div>
    </div>
    
    <!-- By Job Type -->
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="font-semibold mb-4">Entries by Job Type</h3>
      <div class="space-y-2">
        <div v-for="(count, type) in data.summary?.by_job_type" :key="type" 
             class="flex items-center">
          <span class="w-32 text-sm">{{ type }}</span>
          <div class="flex-1 mx-4">
            <div class="bg-gray-200 rounded-full h-4">
              <div class="bg-green-600 rounded-full h-4" 
                   :style="{ width: (count / data.summary.total_entries * 100) + '%' }"></div>
            </div>
          </div>
          <span class="text-sm font-medium">{{ count }}</span>
        </div>
      </div>
    </div>
    
    <!-- Recent Entries Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 bg-gray-50 border-b">
        <h3 class="font-semibold">Recent Entries</h3>
      </div>
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
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="entry in entries.slice(0, 10)" :key="entry.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 font-medium">{{ entry.job_number }}</td>
              <td class="px-6 py-4">{{ formatDate(entry.submitted_at) }}</td>
              <td class="px-6 py-4">{{ entry.user?.name || 'N/A' }}</td>
              <td class="px-6 py-4">{{ entry.job_type?.name || 'N/A' }}</td>
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
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full"
                      :class="{
                        'bg-green-100 text-green-700': entry.status === 'approved',
                        'bg-yellow-100 text-yellow-700': entry.status === 'submitted'
                      }">
                  {{ entry.status }}
                </span>
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
  computed: {
    entries() {
      return this.data.entries || [];
    }
  },
  methods: {
    formatDate(date) {
      return date ? format(new Date(date), 'MMM d, yyyy') : 'N/A';
    }
  }
};
</script>