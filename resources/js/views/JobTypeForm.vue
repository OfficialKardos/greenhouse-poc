<!-- resources/js/views/JobTypeForm.vue -->
<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-0">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-8">
      <div class="flex items-center space-x-3 mb-6">
        <router-link to="/job-types" class="text-gray-400 hover:text-gray-600 p-2 -ml-2">
          <i class="fas fa-arrow-left text-xl"></i>
        </router-link>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{{ isEdit ? 'Edit' : 'Create New' }} Job Type</h1>
      </div>
      
      <!-- Error Alert -->
      <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">{{ errorMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Success Alert -->
      <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-green-700">{{ successMessage }}</p>
          </div>
        </div>
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
                   :class="[
                     'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent',
                     errors.name ? 'border-red-500' : 'border-gray-300'
                   ]"
                   placeholder="e.g., Chemicals, Crop Walk">
            <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
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

          <!-- Icon Selection - Mobile Optimized -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              <i class="fas fa-icons mr-1 text-gray-400"></i>
              Icon
            </label>
            
            <!-- Common Icons - Responsive Grid -->
            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2 mb-3">
              <button v-for="icon in commonIcons" :key="icon.icon"
                      type="button"
                      @click="form.icon = icon.icon"
                      class="p-3 border rounded-lg hover:bg-green-50 hover:border-green-500 transition-all touch-manipulation"
                      :class="form.icon === icon.icon ? 'bg-green-100 border-green-500 ring-2 ring-green-200' : 'border-gray-200'"
                      :title="icon.name">
                <i :class="icon.icon" class="text-lg sm:text-xl"></i>
              </button>
            </div>
            
            <!-- Custom Icon Input - Stacked on Mobile -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
              <input type="text" v-model="form.icon" maxlength="20"
                     class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                     placeholder="Custom icon class (e.g., fas fa-flask)">
              <span class="text-sm text-gray-500 hidden sm:inline">or</span>
              <input type="text" v-model="emojiIcon" maxlength="2"
                     class="w-full sm:w-20 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-center"
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

          <!-- Active Status - Touch Friendly Checkbox -->
          <div class="flex items-center p-4 bg-gray-50 rounded-lg touch-manipulation">
            <input type="checkbox" v-model="form.is_active" id="is_active" 
                   class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500 cursor-pointer">
            <label for="is_active" class="ml-3 text-sm text-gray-700 flex items-center cursor-pointer">
              <i class="fas fa-circle text-green-500 mr-2 text-xs"></i>
              Active (visible to workers)
            </label>
          </div>

          <!-- Fields List Preview (for edit mode) - Mobile Responsive -->
          <div v-if="isEdit && fields.length > 0" class="pt-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
              <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list-ul mr-2 text-green-600"></i>
                Current Fields ({{ fields.length }})
              </h2>
              <router-link :to="`/job-types/${jobTypeId}/fields/create`" 
                         class="text-sm text-green-600 hover:text-green-700 flex items-center justify-center px-3 py-1.5 border border-green-200 rounded-lg">
                <i class="fas fa-plus mr-1"></i> Add Field
              </router-link>
            </div>
            <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
              <div v-for="(field, index) in fields" :key="field.id" 
                   class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center mb-2 sm:mb-0">
                  <span class="text-gray-400 mr-3 text-sm">{{ index + 1 }}.</span>
                  <div>
                    <span class="font-medium text-sm sm:text-base">{{ field.label }}</span>
                    <span class="text-xs text-gray-500 ml-2">
                      <i :class="getFieldTypeIcon(field.field_type)" class="mr-1"></i>
                      {{ field.field_type }}
                    </span>
                  </div>
                </div>
                <router-link :to="`/job-types/${jobTypeId}/fields/${field.id}/edit`" 
                           class="text-blue-600 hover:text-blue-800 text-sm px-2 self-end sm:self-center">
                  <i class="fas fa-edit"></i> Edit
                </router-link>
              </div>
            </div>
            <router-link :to="`/job-types/${jobTypeId}/fields`" 
                       class="inline-flex items-center justify-center w-full sm:w-auto mt-4 text-green-600 hover:text-green-700 border border-green-200 rounded-lg px-4 py-2">
              <i class="fas fa-cog mr-1"></i>
              Manage All Fields
            </router-link>
          </div>

          <!-- Form Actions - Stacked on Mobile -->
          <div class="flex flex-col-reverse sm:flex-row justify-end space-y-2 space-y-reverse sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
            <router-link to="/job-types" 
                         class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-center">
              Cancel
            </router-link>
            <button type="submit" :disabled="saving"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 flex items-center justify-center">
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
      errorMessage: '',
      successMessage: '',
      errors: {},
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
        
        if (this.form.icon && this.form.icon.length <= 2 && this.form.icon.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]|[\u2600-\u27FF]/)) {
          this.emojiIcon = this.form.icon;
        }
      } catch (error) {
        console.error('Failed to load job type:', error);
        this.errorMessage = 'Failed to load job type data';
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
    validateForm() {
      this.errors = {};
      
      if (!this.form.name || this.form.name.trim() === '') {
        this.errors.name = 'Job type name is required';
      }
      
      return Object.keys(this.errors).length === 0;
    },
    async saveJobType() {
      this.errorMessage = '';
      this.successMessage = '';
      this.errors = {};
      
      if (!this.validateForm()) {
        this.errorMessage = 'Please fix the validation errors below';
        return;
      }
      
      this.saving = true;
      
      try {
        if (this.isEdit) {
          await axios.put(`/job-types/${this.$route.params.id}`, this.form);
          this.successMessage = 'Job type updated successfully!';
        } else {
          const response = await axios.post('/job-types', this.form);
          this.successMessage = 'Job type created successfully!';
          
          if (!this.isEdit) {
            setTimeout(() => {
              this.$router.push(`/job-types/${response.data.data.id}/fields`);
            }, 1500);
            return;
          }
        }
        
        setTimeout(() => {
          this.$router.push('/job-types');
        }, 1500);
        
      } catch (error) {
        console.error('Failed to save job type:', error);
        
        if (error.response?.status === 422 && error.response?.data?.errors) {
          this.errors = error.response.data.errors;
          this.errorMessage = 'Please fix the validation errors below';
        } else if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = error.message || 'Failed to save job type. Please try again.';
        }
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>

<style scoped>
.touch-manipulation {
  touch-action: manipulation;
}

/* Better tap targets on mobile */
@media (max-width: 640px) {
  button, 
  .router-link,
  input[type="checkbox"] + label {
    min-height: 44px;
  }
  
  button i, 
  .router-link i {
    font-size: 1.25rem;
  }
}
</style>