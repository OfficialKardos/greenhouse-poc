<!-- resources/js/views/JobFieldForm.vue -->
<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
      <div class="flex items-center space-x-3 mb-6">
        <router-link :to="`/job-types/${jobTypeId}/fields`" class="text-gray-400 hover:text-gray-600">
          <span class="text-2xl">←</span>
        </router-link>
        <h1 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Edit' : 'Add New' }} Field for {{ jobTypeName }}</h1>
      </div>
      
      <form @submit.prevent="saveField">
        <div class="space-y-6">
          <!-- Label -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Field Label *</label>
            <input type="text" v-model="form.label" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="e.g., What plant?">
          </div>

          <!-- Field Name (internal) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Field Name (internal) *</label>
            <input type="text" v-model="form.field_name" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="e.g., plant, chemical_applied">
            <p class="text-xs text-gray-500 mt-1">Lowercase, no spaces. Used in API and reports.</p>
          </div>

          <!-- Field Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Field Type *</label>
            <select v-model="form.field_type" @change="onFieldTypeChange" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              <option value="text">Text (single line)</option>
              <option value="textarea">Textarea (multi line)</option>
              <option value="number">Number</option>
              <option value="dropdown">Dropdown</option>
              <option value="yes_no">Yes/No</option>
              <option value="temperature">Temperature</option>
              <option value="photo">Photo</option>
              <option value="date">Date</option>
              <option value="ppm">PPM</option>
              <option value="ph">pH</option>
              <option value="ec">EC</option>
              <option value="time">Time</option>
              <option value="datetime">Date & Time</option>
            </select>
          </div>

          <!-- Dropdown Source (only if field_type = dropdown) -->
          <div v-if="form.field_type === 'dropdown'">
            <label class="block text-sm font-medium text-gray-700 mb-2">Dropdown Source *</label>
            <select v-model="form.dropdown_source_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              <option value="">Select Dropdown List</option>
              <option v-for="list in dropdownLists" :key="list.id" :value="list.id">{{ list.name }}</option>
            </select>
            <router-link to="/dropdowns/create" class="text-sm text-green-600 hover:text-green-700 mt-1 inline-block">
              + Create new dropdown list
            </router-link>
          </div>

          <!-- Placeholder -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Placeholder Text</label>
            <input type="text" v-model="form.placeholder"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   :placeholder="`e.g., Enter ${form.label.toLowerCase()}...`">
          </div>

          <!-- Help Text -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Help Text</label>
            <input type="text" v-model="form.help_text"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="Additional instructions for the worker">
          </div>

          <!-- Required -->
          <div class="flex items-center">
            <input type="checkbox" v-model="form.is_required" id="is_required" class="h-4 w-4 text-green-600 rounded border-gray-300">
            <label for="is_required" class="ml-2 text-sm text-gray-700">Required field (must be filled)</label>
          </div>

          <!-- Active -->
          <div class="flex items-center">
            <input type="checkbox" v-model="form.is_active" id="is_active" class="h-4 w-4 text-green-600 rounded border-gray-300">
            <label for="is_active" class="ml-2 text-sm text-gray-700">Active (visible in forms)</label>
          </div>

          <!-- Sort Order -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
            <input type="number" v-model="form.sort_order" min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <router-link :to="`/job-types/${jobTypeId}/fields`" 
                         class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
              Cancel
            </router-link>
            <button type="submit" :disabled="saving"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
              {{ saving ? 'Saving...' : (isEdit ? 'Update Field' : 'Create Field') }}
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
      jobTypeId: this.$route.params.jobTypeId,
      jobTypeName: '',
      dropdownLists: [],
      form: {
        label: '',
        field_name: '',
        field_type: 'text',
        placeholder: '',
        help_text: '',
        is_required: false,
        dropdown_source_id: '',
        sort_order: 0,
        is_active: true
      },
      saving: false,
      isEdit: false
    };
  },
  mounted() {
    this.loadJobType();
    this.loadDropdownLists();
    
    const fieldId = this.$route.params.id;
    if (fieldId) {
      this.isEdit = true;
      this.loadField(fieldId);
    }
  },
  methods: {
    async loadJobType() {
      try {
        const response = await axios.get(`/job-types/${this.jobTypeId}`);
        this.jobTypeName = response.data.data.name;
      } catch (error) {
        console.error('Failed to load job type:', error);
      }
    },
    async loadDropdownLists() {
      try {
        const response = await axios.get('/dropdown-lists');
        this.dropdownLists = response.data.data;
      } catch (error) {
        console.error('Failed to load dropdown lists:', error);
      }
    },
    async loadField(id) {
      try {
        const response = await axios.get(`/job-fields/${id}`);
        const field = response.data.data;
        this.form = {
          label: field.label,
          field_name: field.field_name,
          field_type: field.field_type,
          placeholder: field.placeholder || '',
          help_text: field.help_text || '',
          is_required: field.is_required,
          dropdown_source_id: field.dropdown_source_id || '',
          sort_order: field.sort_order || 0,
          is_active: field.is_active
        };
      } catch (error) {
        console.error('Failed to load field:', error);
      }
    },
    onFieldTypeChange() {
      if (this.form.field_type !== 'dropdown') {
        this.form.dropdown_source_id = '';
      }
    },
    async saveField() {
      this.saving = true;
      try {
        const url = this.isEdit 
          ? `/job-fields/${this.$route.params.id}`
          : `/job-types/${this.jobTypeId}/job-fields`;
        
        if (this.isEdit) {
          await axios.put(url, this.form);
        } else {
          await axios.post(url, this.form);
        }
        
        this.$router.push(`/job-types/${this.jobTypeId}/fields`);
      } catch (error) {
        console.error('Failed to save field:', error);
        alert('Error saving field. Please check all fields.');
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>