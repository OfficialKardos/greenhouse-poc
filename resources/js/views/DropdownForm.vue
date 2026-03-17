<!-- resources/js/views/DropdownForm.vue -->
<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
      <div class="flex items-center space-x-3 mb-6">
        <router-link to="/dropdowns" class="text-gray-400 hover:text-gray-600">
          <span class="text-2xl">←</span>
        </router-link>
        <h1 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Edit' : 'Create New' }} Dropdown List</h1>
      </div>
      
      <form @submit.prevent="saveDropdown">
        <div class="space-y-6">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">List Name *</label>
            <input type="text" v-model="form.name" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="e.g., Plants, Chemicals, Moisture Levels">
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea v-model="form.description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="What is this dropdown list used for?"></textarea>
          </div>

          <!-- Preview of existing values (if editing) -->
          <div v-if="isEdit && dropdownValues.length" class="bg-gray-50 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Values</label>
            <div class="flex flex-wrap gap-2">
              <span v-for="value in dropdownValues" :key="value.id" 
                    class="px-3 py-1 bg-white border rounded-full text-sm"
                    :style="value.color ? { backgroundColor: value.color + '20', borderColor: value.color } : {}">
                {{ value.label || value.value }}
              </span>
            </div>
            <router-link :to="`/dropdowns/${$route.params.id}/values`" class="text-sm text-green-600 hover:text-green-700 mt-2 inline-block">
              Manage Values →
            </router-link>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <router-link to="/dropdowns" 
                         class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
              Cancel
            </router-link>
            <button type="submit" :disabled="saving"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
              {{ saving ? 'Saving...' : (isEdit ? 'Update List' : 'Create List') }}
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
        description: ''
      },
      dropdownValues: [],
      saving: false,
      isEdit: false
    };
  },
  mounted() {
    const id = this.$route.params.id;
    if (id) {
      this.isEdit = true;
      this.loadDropdown(id);
    }
  },
  methods: {
    async loadDropdown(id) {
      try {
        const response = await axios.get(`/dropdown-lists/${id}`);
        const dropdown = response.data.data;
        this.form = {
          name: dropdown.name,
          description: dropdown.description || ''
        };
        this.dropdownValues = dropdown.values || [];
      } catch (error) {
        console.error('Failed to load dropdown list:', error);
      }
    },
    async saveDropdown() {
      this.saving = true;
      try {
        if (this.isEdit) {
          await axios.put(`/dropdown-lists/${this.$route.params.id}`, this.form);
        } else {
          await axios.post('/dropdown-lists', this.form);
        }
        this.$router.push('/dropdowns');
      } catch (error) {
        console.error('Failed to save dropdown list:', error);
        alert('Error saving dropdown list.');
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>