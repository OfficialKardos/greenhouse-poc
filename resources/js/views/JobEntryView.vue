<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header with back button -->
    <div class="mb-6 flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <router-link to="/entries" class="text-gray-500 hover:text-gray-700">
          <span class="text-2xl">←</span>
        </router-link>
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Job Entry Details</h1>
          <p class="text-gray-500">Job #{{ job?.job_number }}</p>
        </div>
      </div>
      <div class="flex space-x-3">
        <span class="px-3 py-1 text-sm rounded-full"
              :class="{
                'bg-green-100 text-green-700': job?.status === 'approved',
                'bg-yellow-100 text-yellow-700': job?.status === 'submitted',
                'bg-red-100 text-red-700': job?.status === 'rejected'
              }">
          {{ job?.status?.toUpperCase() }}
        </span>
        <button @click="exportJob" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
          📥 Export
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
    </div>

    <div v-else-if="job" class="space-y-6">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-500">Worker</div>
          <div class="text-lg font-semibold">{{ job.user?.name }}</div>
          <div class="text-xs text-gray-400">{{ job.user?.email }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-500">Location</div>
          <div class="text-lg font-semibold">{{ job.greenhouse?.name }}</div>
          <div class="text-xs text-gray-400">Bay: {{ job.bay?.name }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-500">Job Type</div>
          <div class="text-lg font-semibold">{{ job.job_type?.name }}</div>
          <div class="text-xs text-gray-400">Submitted: {{ formatDate(job.submitted_at) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-500">Photos</div>
          <div class="text-lg font-semibold">{{ job.photos?.length || 0 }}</div>
          <div class="text-xs text-gray-400">Attached to job</div>
        </div>
      </div>

      <!-- Job Details Section -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-800">Job Details</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="value in job.values" :key="value.id" 
                 class="border-b border-gray-100 pb-3">
              <div class="text-sm text-gray-500">{{ value.job_field?.label }}</div>
              <div class="font-medium mt-1" :class="getValueClass(value)">
                {{ formatFieldValue(value) }}
              </div>
            </div>
          </div>
          
          <!-- Notes Section -->
          <div v-if="job.notes" class="mt-6 pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-500 mb-2">Notes</div>
            <div class="bg-gray-50 p-4 rounded-lg text-gray-700 whitespace-pre-wrap">
              {{ job.notes }}
            </div>
          </div>
        </div>
      </div>

      <!-- Photos Gallery -->
      <div v-if="job.photos?.length" class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-lg font-semibold text-gray-800">Photos ({{ job.photos.length }})</h2>
          <button @click="downloadAllPhotos" class="text-green-600 hover:text-green-700 text-sm">
            📥 Download All
          </button>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="photo in job.photos" :key="photo.id" 
                 class="relative group cursor-pointer"
                 @click="viewPhoto(photo)">
              <img :src="photo.thumbnail_url || photo.url" 
                   :alt="photo.filename"
                   class="w-full h-48 object-cover rounded-lg border border-gray-200">
              <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">
                <span class="text-white opacity-0 group-hover:opacity-100 text-2xl">🔍</span>
              </div>
              <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                {{ photo.filename }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Metadata/System Info -->
      <div class="bg-gray-50 rounded-lg p-4 text-xs text-gray-500">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
          <div>Created: {{ formatDateTime(job.created_at) }}</div>
          <div>Submitted: {{ formatDateTime(job.submitted_at) }}</div>
          <div v-if="job.approved_at">Approved: {{ formatDateTime(job.approved_at) }}</div>
          <div>IP: {{ job.metadata?.ip || 'N/A' }}</div>
          <div>Device: {{ job.metadata?.device || 'N/A' }}</div>
        </div>
      </div>
    </div>

    <!-- Photo Viewer Modal -->
    <div v-if="selectedPhoto" class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center" @click="selectedPhoto = null">
      <div class="relative max-w-5xl max-h-screen p-4">
        <img :src="selectedPhoto.url" :alt="selectedPhoto.filename" class="max-h-screen max-w-full object-contain">
        <button class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300" @click="selectedPhoto = null">✕</button>
        <div class="absolute bottom-4 left-4 text-white bg-black bg-opacity-50 px-4 py-2 rounded">
          {{ selectedPhoto.filename }}
        </div>
        <a :href="selectedPhoto.url" download 
           class="absolute bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
          📥 Download
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { format } from 'date-fns';

export default {
  data() {
    return {
      job: null,
      loading: true,
      selectedPhoto: null
    };
  },
  mounted() {
    this.loadJob();
  },
  methods: {
    async loadJob() {
      try {
        const response = await axios.get(`/job-entries/${this.$route.params.id}`);
        this.job = response.data.data;
      } catch (error) {
        console.error('Failed to load job:', error);
        this.$router.push('/entries');
      } finally {
        this.loading = false;
      }
    },
    formatDate(date) {
      return date ? format(new Date(date), 'MMM d, yyyy') : 'N/A';
    },
    formatDateTime(date) {
      return date ? format(new Date(date), 'MMM d, yyyy h:mm a') : 'N/A';
    },
    formatFieldValue(value) {
      if (!value.value) return '<em>Not provided</em>';
      
      // Handle yes/no fields
      if (value.job_field?.field_type === 'yes_no') {
        return value.value === 'yes' ? '✅ Yes' : '❌ No';
      }
      
      // Handle numbers
      if (['number', 'temperature', 'ppm', 'ph', 'ec'].includes(value.job_field?.field_type)) {
        return parseFloat(value.value).toFixed(2);
      }
      
      return value.value;
    },
    getValueClass(value) {
      if (value.job_field?.field_type === 'ph') {
        const ph = parseFloat(value.value);
        if (ph < 5.5 || ph > 7.5) return 'text-red-600';
        if (ph >= 6.0 && ph <= 7.0) return 'text-green-600';
        return 'text-yellow-600';
      }
      return '';
    },
    viewPhoto(photo) {
      this.selectedPhoto = photo;
    },
    async downloadAllPhotos() {
      if (!this.job.photos?.length) return;
      
      // Create a zip of all photos (simplified - just open each in new tab)
      this.job.photos.forEach(photo => {
        window.open(photo.url, '_blank');
      });
    },
    exportJob() {
      // Export as JSON
      const dataStr = JSON.stringify(this.job, null, 2);
      const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
      const exportFileDefaultName = `job-${this.job.job_number}.json`;
      
      const linkElement = document.createElement('a');
      linkElement.setAttribute('href', dataUri);
      linkElement.setAttribute('download', exportFileDefaultName);
      linkElement.click();
    }
  }
};
</script>