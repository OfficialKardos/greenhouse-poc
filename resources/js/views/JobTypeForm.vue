<!-- resources/js/views/JobTypeForm.vue -->
<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
      <div class="flex items-center space-x-3 mb-6">
        <router-link to="/job-types" class="text-gray-400 hover:text-gray-600">
          <i class="fas fa-arrow-left text-xl"></i>
        </router-link>
        <h1 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Edit' : 'Create New' }} Job Type</h1>
      </div>
      
      <form @submit.prevent="saveJobType">
        <div class="space-y-6">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-tag mr-1 text-gray-400"></i>
              Job Type Name *
            </label>
            <input type="text" v-model="form.name" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                   placeholder="e.g., Chemicals, Crop Walk">
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-align-left mr-1 text-gray-400"></i>
              Description
            </label>
            <textarea v-model="form.description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                      placeholder="Describe what this job type involves..."></textarea>
          </div>

          <!-- Icon Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-icons mr-1 text-gray-400"></i>
              Icon
            </label>
            <div class="grid grid-cols-8 gap-2 mb-3">
              <button v-for="icon in commonIcons" :key="icon.icon"
                      type="button"
                      @click="form.icon = icon.icon"
                      class="p-3 border rounded-lg hover:bg-green-50 hover:border-green-500 transition-all"
                      :class="form.icon === icon.icon ? 'bg-green-100 border-green-500 ring-2 ring-green-200' : 'border-gray-200'"
                      :title="icon.name">
                <i :class="icon.icon" class="text-xl"></i>
              </button>
            </div>
            <div class="flex items-center space-x-2">
              <input type="text" v-model="form.icon" maxlength="20"
                     class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                     placeholder="Custom icon class (e.g., fas fa-flask)">
              <span class="text-sm text-gray-500">or</span>
              <input type="text" v-model="emojiIcon" maxlength="2"
                     class="w-20 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-center"
                     placeholder="😊">
            </div>
            <p class="text-xs text-gray-500 mt-2">
              <i class="fas fa-info-circle mr-1"></i>
              Choose from common icons above, enter an emoji, or use a custom Font Awesome class
            </p>
          </div>

          <!-- Sort Order -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-sort-numeric-down mr-1 text-gray-400"></i>
              Sort Order
            </label>
            <input type="number" v-model="form.sort_order" min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="e.g., 1, 2, 3">
            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first in lists</p>
          </div>

          <!-- Active Status -->
          <div class="flex items-center p-4 bg-gray-50 rounded-lg">
            <input type="checkbox" v-model="form.is_active" id="is_active" 
                   class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
            <label for="is_active" class="ml-3 text-sm text-gray-700 flex items-center">
              <i class="fas fa-circle text-green-500 mr-2 text-xs"></i>
              Active (visible to workers)
            </label>
          </div>

          <!-- Fields List Preview (for edit mode) -->
          <div v-if="isEdit && fields.length > 0" class="pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list-ul mr-2 text-green-600"></i>
                Current Fields ({{ fields.length }})
              </h2>
              <router-link :to="`/job-types/${jobTypeId}/fields/create`" 
                         class="text-sm text-green-600 hover:text-green-700 flex items-center">
                <i class="fas fa-plus mr-1"></i> Add Field
              </router-link>
            </div>
            <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
              <div v-for="(field, index) in fields" :key="field.id" 
                   class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center">
                  <span class="text-gray-400 mr-3 text-sm">{{ index + 1 }}.</span>
                  <div>
                    <span class="font-medium">{{ field.label }}</span>
                    <span class="text-xs text-gray-500 ml-2">
                      <i :class="getFieldTypeIcon(field.field_type)" class="mr-1"></i>
                      {{ field.field_type }}
                    </span>
                  </div>
                </div>
                <router-link :to="`/job-types/${jobTypeId}/fields/${field.id}/edit`" 
                           class="text-blue-600 hover:text-blue-800 text-sm px-2">
                  <i class="fas fa-edit"></i>
                </router-link>
              </div>
            </div>
            <router-link :to="`/job-types/${jobTypeId}/fields`" 
                       class="inline-flex items-center mt-4 text-green-600 hover:text-green-700">
              <i class="fas fa-cog mr-1"></i>
              Manage All Fields
            </router-link>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <router-link to="/job-types" 
                         class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center">
              <i class="fas fa-times mr-2"></i>
              Cancel
            </router-link>
            <button type="submit" :disabled="saving"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 inline-flex items-center">
              <i :class="saving ? 'fas fa-spinner fa-spin mr-2' : (isEdit ? 'fas fa-save mr-2' : 'fas fa-plus mr-2')"></i>
              {{ saving ? 'Saving...' : (isEdit ? 'Update Job Type' : 'Create Job Type') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        name: '',
        description: '',
        icon: 'fas fa-tasks',
        sort_order: 0,
        is_active: true
      },
      fields: [],
      saving: false,
      isEdit: false,
      jobTypeId: null,
      emojiIcon: '',
      commonIcons: [
        { icon: 'fas fa-flask', name: 'Chemicals' },
        { icon: 'fas fa-clipboard-list', name: 'Crop Walk' },
        { icon: 'fas fa-seedling', name: 'Soil' },
        { icon: 'fas fa-chart-line', name: 'Growth' },
        { icon: 'fas fa-microscope', name: 'Lab' },
        { icon: 'fas fa-tint', name: 'Water' },
        { icon: 'fas fa-leaf', name: 'Plant' },
        { icon: 'fas fa-bug', name: 'Pest' },
        { icon: 'fas fa-vial', name: 'Sample' },
        { icon: 'fas fa-ruler', name: 'Measure' },
        { icon: 'fas fa-camera', name: 'Photo' },
        { icon: 'fas fa-pen', name: 'Notes' },
        { icon: 'fas fa-stopwatch', name: 'Timer' },
        { icon: 'fas fa-thermometer-half', name: 'Temperature' },
        { icon: 'fas fa-pills', name: 'Treatment' },
        { icon: 'fas fa-tools', name: 'Maintenance' },
      ]
    };
  },
  watch: {
    emojiIcon(newVal) {
      if (newVal && newVal.length > 0) {
        this.form.icon = newVal;
      }
    }
  },
  mounted() {
    const id = this.$route.params.id;
    if (id) {
      this.isEdit = true;
      this.jobTypeId = id;
      this.loadJobType(id);
    }
  },
  methods: {
    async loadJobType(id) {
      try {
        const response = await axios.get(`/job-types/${id}`);
        const jobType = response.data.data;
        this.form = {
          name: jobType.name,
          description: jobType.description || '',
          icon: jobType.icon || 'fas fa-tasks',
          sort_order: jobType.sort_order || 0,
          is_active: jobType.is_active
        };
        this.fields = jobType.fields || [];
        
        // Check if icon is an emoji
        if (this.form.icon && this.form.icon.length <= 2 && this.form.icon.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]|[\u2600-\u27FF]/)) {
          this.emojiIcon = this.form.icon;
        }
      } catch (error) {
        console.error('Failed to load job type:', error);
      }
    },
    getFieldTypeIcon(type) {
      const icons = {
        text: 'fas fa-font',
        textarea: 'fas fa-align-left',
        number: 'fas fa-hashtag',
        dropdown: 'fas fa-chevron-down',
        yes_no: 'fas fa-toggle-on',
        temperature: 'fas fa-thermometer-half',
        photo: 'fas fa-camera',
        date: 'fas fa-calendar',
        ppm: 'fas fa-flask',
        ph: 'fas fa-vial',
        ec: 'fas fa-bolt',
        time: 'fas fa-clock',
        datetime: 'fas fa-calendar-alt',
        signature: 'fas fa-pen'
      };
      return icons[type] || 'fas fa-question';
    },
    async saveJobType() {
      this.saving = true;
      try {
        if (this.isEdit) {
          await axios.put(`/job-types/${this.$route.params.id}`, this.form);
        } else {
          const response = await axios.post('/job-types', this.form);
          if (!this.isEdit) {
            this.$router.push(`/job-types/${response.data.data.id}/fields`);
            return;
          }
        }
        this.$router.push('/job-types');
      } catch (error) {
        console.error('Failed to save job type:', error);
        alert('Error saving job type. Please check all fields.');
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>