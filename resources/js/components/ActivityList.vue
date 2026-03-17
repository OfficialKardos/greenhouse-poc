<!-- resources/js/components/ActivityList.vue -->
<template>
  <div v-if="entries.length" class="space-y-4">
    <div v-for="entry in entries" :key="entry.id" 
         class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-xl transition-colors group cursor-pointer"
         @click="viewEntry(entry.id)">
      <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-sm group-hover:scale-110 transition-transform">
        {{ entry.job_type?.name?.charAt(0) || 'J' }}
      </div>
      <div class="flex-1">
        <p class="text-sm font-medium text-gray-800">{{ entry.job_number }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ entry.job_type?.name }} • {{ entry.bay?.name }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ timeAgo(entry.submitted_at) }}</p>
      </div>
      <span class="px-2 py-1 text-xs rounded-full" 
            :class="statusClass(entry.status)">
        {{ entry.status }}
      </span>
    </div>
  </div>
  <div v-else class="text-center py-8 text-gray-400">
    No recent activity
  </div>
</template>

<script>
import { formatDistanceToNow } from 'date-fns';

export default {
  props: {
    entries: Array
  },
  methods: {
    timeAgo(date) {
      return formatDistanceToNow(new Date(date), { addSuffix: true });
    },
    statusClass(status) {
      return {
        'bg-green-100 text-green-700': status === 'approved',
        'bg-yellow-100 text-yellow-700': status === 'submitted',
        'bg-red-100 text-red-700': status === 'rejected',
        'bg-gray-100 text-gray-700': status === 'draft'
      }[status] || 'bg-gray-100 text-gray-700';
    },
    viewEntry(id) {
      this.$router.push(`/entries/${id}`);
    }
  }
};
</script>