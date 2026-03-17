<!-- resources/js/views/GreenhouseForm.vue -->
<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isEdit ? 'Edit' : 'Add New' }} Greenhouse</h1>
      
      <form @submit.prevent="saveGreenhouse">
        <div class="space-y-6">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Greenhouse Name *</label>
            <input type="text" v-model="form.name" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="e.g., 72nd Street Greenhouse">
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea v-model="form.description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Brief description of this greenhouse..."></textarea>
          </div>

          <!-- Address -->
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
              <input type="text" v-model="form.address"
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                     placeholder="123 Greenhouse Lane">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
              <input type="text" v-model="form.city"
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                     placeholder="Springfield">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
              <input type="text" v-model="form.state"
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                     placeholder="IL">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
              <input type="text" v-model="form.zip"
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                     placeholder="62701">
            </div>
          </div>

          <!-- Bays Section -->
          <div class="pt-6 border-t border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Bays</h2>
            
            <div v-for="(bay, index) in form.bays" :key="index" class="flex items-center space-x-4 mb-3">
              <input type="text" v-model="bay.name" 
                     class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                     :placeholder="`Bay ${index + 1} name`">
              <button type="button" @click="removeBay(index)" 
                      class="text-red-500 hover:text-red-700">
                <span>🗑️</span>
              </button>
            </div>

            <button type="button" @click="addBay" 
                    class="mt-2 text-green-600 hover:text-green-700 flex items-center space-x-2">
              <span>➕</span>
              <span>Add Bay</span>
            </button>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <router-link to="/greenhouses" 
                         class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
              Cancel
            </router-link>
            <button type="submit" :disabled="saving"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
              {{ saving ? 'Saving...' : (isEdit ? 'Update' : 'Create') }} Greenhouse
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
        address: '',
        city: '',
        state: '',
        zip: '',
        bays: [{ name: '' }]
      },
      saving: false,
      isEdit: false
    };
  },
  mounted() {
    const id = this.$route.params.id;
    if (id) {
      this.isEdit = true;
      this.loadGreenhouse(id);
    }
  },
  methods: {
    async loadGreenhouse(id) {
      try {
        const response = await axios.get(`/greenhouses/${id}`);
        const greenhouse = response.data.data;
        this.form = {
          name: greenhouse.name,
          description: greenhouse.description,
          address: greenhouse.address,
          city: greenhouse.city,
          state: greenhouse.state,
          zip: greenhouse.zip,
          bays: greenhouse.bays?.length ? greenhouse.bays : [{ name: '' }]
        };
      } catch (error) {
        console.error('Failed to load greenhouse:', error);
      }
    },
    addBay() {
      this.form.bays.push({ name: '' });
    },
    removeBay(index) {
      this.form.bays.splice(index, 1);
    },
    async saveGreenhouse() {
      this.saving = true;
      try {
        if (this.isEdit) {
          await axios.put(`/greenhouses/${this.$route.params.id}`, this.form);
        } else {
          await axios.post('/greenhouses', this.form);
        }
        this.$router.push('/greenhouses');
      } catch (error) {
        console.error('Failed to save greenhouse:', error);
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>