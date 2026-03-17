<!-- resources/js/views/JobTypes.vue -->
<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Job Types</h1>
        <p class="text-gray-500 mt-1">Configure job types and their fields</p>
      </div>
      <router-link to="/job-types/create" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2">
        <i class="fas fa-plus"></i>
        <span>Add Job Type</span>
      </router-link>
    </div>

    <!-- Job Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="jobType in jobTypes" :key="jobType.id" 
           class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white shadow-sm">
                <i :class="getIconClass(jobType.icon)"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-800">{{ jobType.name }}</h3>
            </div>
            <span class="px-2 py-1 text-xs rounded-full font-medium" 
                  :class="jobType.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
              <i :class="jobType.is_active ? 'fas fa-circle text-green-500 mr-1 text-[8px]' : 'fas fa-circle text-gray-400 mr-1 text-[8px]'"></i>
              {{ jobType.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>
          
          <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ jobType.description || 'No description' }}</p>
          
          <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="text-sm text-gray-500 flex items-center">
              <i class="fas fa-list-ul mr-1 text-xs"></i>
              <span class="font-medium">{{ jobType.fields?.length || 0 }}</span> fields
            </div>
            <div class="flex space-x-3">
              <router-link :to="`/job-types/${jobType.id}/fields`" 
                         class="text-green-600 hover:text-green-800 text-sm flex items-center"
                         :title="'Manage fields'">
                <i class="fas fa-cog mr-1"></i> Fields
              </router-link>
              <router-link :to="`/job-types/${jobType.id}/edit`" 
                         class="text-blue-600 hover:text-blue-800"
                         :title="'Edit job type'">
                <i class="fas fa-edit"></i>
              </router-link>
              <button @click="confirmDelete(jobType)" 
                      class="text-red-600 hover:text-red-800"
                      :title="'Delete job type'">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="jobTypes.length === 0" class="text-center py-12">
      <div class="text-6xl text-gray-300 mb-4">
        <i class="fas fa-clipboard-list"></i>
      </div>
      <h3 class="text-lg font-medium text-gray-700 mb-2">No Job Types Yet</h3>
      <p class="text-gray-500 mb-6">Get started by creating your first job type</p>
      <router-link to="/job-types/create" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i>
        Create Job Type
      </router-link>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      jobTypes: []
    };
  },
  mounted() {
    this.loadJobTypes();
  },
  methods: {
    async loadJobTypes() {
      try {
        const response = await axios.get('/job-types?all=true');
        this.jobTypes = response.data.data;
      } catch (error) {
        console.error('Failed to load job types:', error);
      }
    },
    getIconClass(icon) {
      // Map common emoji/icons to Font Awesome
      const iconMap = {
        '🧪': 'fas fa-flask',
        '📋': 'fas fa-clipboard-list',
        '🌱': 'fas fa-seedling',
        '📈': 'fas fa-chart-line',
        '🔬': 'fas fa-microscope',
        '💧': 'fas fa-tint',
        '🌿': 'fas fa-leaf',
        '🐛': 'fas fa-bug',
        '🧫': 'fas fa-vial',
        '📏': 'fas fa-ruler',
        '📷': 'fas fa-camera',
        '📝': 'fas fa-pen',
        '⏱️': 'fas fa-stopwatch',
        '🌡️': 'fas fa-thermometer-half',
        '💊': 'fas fa-pills',
        '🧂': 'fas fa-flask',
      };
      
      return iconMap[icon] || icon || 'fas fa-tasks';
    },
    confirmDelete(jobType) {
      if (confirm(`Are you sure you want to delete "${jobType.name}"? This will also delete all associated fields.`)) {
        this.deleteJobType(jobType.id);
      }
    },
    async deleteJobType(id) {
      try {
        await axios.delete(`/job-types/${id}`);
        this.jobTypes = this.jobTypes.filter(j => j.id !== id);
      } catch (error) {
        console.error('Failed to delete job type:', error);
        alert('Failed to delete job type. It may have associated jobs.');
      }
    }
  }
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>