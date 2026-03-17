<!-- resources/js/views/BayForm.vue -->
<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
      <div class="flex items-center space-x-3 mb-6">
        <router-link to="/bays" class="text-gray-400 hover:text-gray-600">
          <span class="text-2xl">←</span>
        </router-link>
        <h1 class="text-2xl font-bold text-gray-800">{{ isEdit ? 'Edit' : 'Add New' }} Bay</h1>
      </div>
      
      <form @submit.prevent="saveBay">
        <div class="space-y-6">
          <!-- Greenhouse Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Greenhouse *</label>
            <select v-model="form.greenhouse_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
              <option value="">Select Greenhouse</option>
              <option v-for="gh in greenhouses" :key="gh.id" :value="gh.id">{{ gh.name }}</option>
            </select>
          </div>

          <!-- Bay Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bay Name *</label>
            <input type="text" v-model="form.name" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="e.g., Shipping Area">
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea v-model="form.description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Brief description of this bay..."></textarea>
          </div>

          <!-- Capacity -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Capacity (plants)</label>
            <input type="number" v-model="form.capacity" min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="e.g., 1000">
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <router-link to="/bays" 
                         class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
              Cancel
            </router-link>
            <button type="submit" :disabled="saving"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
              {{ saving ? 'Saving...' : (isEdit ? 'Update Bay' : 'Create Bay') }}
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
        greenhouse_id: '',
        name: '',
        description: '',
        capacity: ''
      },
      greenhouses: [],
      saving: false,
      isEdit: false
    };
  },
  mounted() {
    this.loadGreenhouses();
    const id = this.$route.params.id;
    if (id) {
      this.isEdit = true;
      this.loadBay(id);
    }
  },
  methods: {
    async loadGreenhouses() {
      try {
        const response = await axios.get('/greenhouses');
        this.greenhouses = response.data.data;
      } catch (error) {
        console.error('Failed to load greenhouses:', error);
      }
    },
    async loadBay(id) {
      try {
        const response = await axios.get(`/bays/${id}`);
        const bay = response.data.data;
        this.form = {
          greenhouse_id: bay.greenhouse_id,
          name: bay.name,
          description: bay.description || '',
          capacity: bay.capacity || ''
        };
      } catch (error) {
        console.error('Failed to load bay:', error);
      }
    },
    async saveBay() {
      this.saving = true;
      try {
        if (this.isEdit) {
          await axios.put(`/bays/${this.$route.params.id}`, this.form);
        } else {
          await axios.post('/bays', this.form);
        }
        this.$router.push('/bays');
      } catch (error) {
        console.error('Failed to save bay:', error);
        alert('Error saving bay. Please check all fields.');
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>