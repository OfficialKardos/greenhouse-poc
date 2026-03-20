<!-- resources/js/views/GreenhouseForm.vue -->
<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isEdit ? 'Edit' : 'Add New' }} Greenhouse</h1>
      
      <!-- Error Alert -->
      <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error</h3>
            <div class="mt-1 text-sm text-red-700">
              <p>{{ errorMessage }}</p>
            </div>
            <div v-if="validationErrors.length" class="mt-2">
              <ul class="list-disc list-inside text-sm text-red-700">
                <li v-for="error in validationErrors" :key="error">{{ error }}</li>
              </ul>
            </div>
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
            <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Warning Alert for Required Fields -->
      <div v-if="showRequiredWarning" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Required Fields</h3>
            <div class="mt-1 text-sm text-yellow-700">
              <p>The following fields are required:</p>
              <ul class="list-disc list-inside mt-1">
                <li><strong>Greenhouse Name</strong> - Must be unique and cannot be empty</li>
                <li><strong>Bay Names</strong> - Each bay must have a name (if bays are added)</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      
      <form @submit.prevent="saveGreenhouse">
        <div class="space-y-6">
          <!-- Name with inline validation -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Greenhouse Name *
              <span v-if="formErrors.name" class="text-red-500 text-xs ml-2">{{ formErrors.name }}</span>
            </label>
            <input type="text" v-model="form.name" required
                   :class="[
                     'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500',
                     formErrors.name ? 'border-red-500' : 'border-gray-300'
                   ]"
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
                     :class="[
                       'flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500',
                       bayErrors[index] ? 'border-red-500' : 'border-gray-300'
                     ]"
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
            <p class="text-xs text-gray-500 mt-2">Note: Each bay name is required</p>
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
      isEdit: false,
      errorMessage: '',
      successMessage: '',
      validationErrors: [],
      formErrors: {},
      bayErrors: {}
    };
  },
  computed: {
    showRequiredWarning() {
      return (!this.form.name && !this.isEdit) || (this.form.bays.some(bay => !bay.name && bay.name !== ''));
    }
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
          description: greenhouse.description || '',
          address: greenhouse.address || '',
          city: greenhouse.city || '',
          state: greenhouse.state || '',
          zip: greenhouse.zip || '',
          bays: greenhouse.bays?.length ? greenhouse.bays : [{ name: '' }]
        };
      } catch (error) {
        this.errorMessage = 'Failed to load greenhouse data';
        if (error.response?.data?.message) {
          this.errorMessage += ': ' + error.response.data.message;
        }
        console.error('Failed to load greenhouse:', error);
      }
    },
    addBay() {
      this.form.bays.push({ name: '' });
    },
    removeBay(index) {
      if (this.form.bays.length > 1) {
        this.form.bays.splice(index, 1);
        // Clear error for removed bay
        delete this.bayErrors[index];
      }
    },
    validateForm() {
      const errors = [];
      const formErrors = {};
      const bayErrors = {};
      
      // Validate name
      if (!this.form.name || this.form.name.trim() === '') {
        errors.push('Greenhouse name is required');
        formErrors.name = 'Name is required';
      }
      
      // Validate bay names
      this.form.bays.forEach((bay, index) => {
        if (!bay.name || bay.name.trim() === '') {
          errors.push(`Bay ${index + 1} name is required`);
          bayErrors[index] = 'Bay name is required';
        }
      });
      
      this.validationErrors = errors;
      this.formErrors = formErrors;
      this.bayErrors = bayErrors;
      
      return errors.length === 0;
    },
    async saveGreenhouse() {
      // Clear previous messages
      this.errorMessage = '';
      this.successMessage = '';
      this.validationErrors = [];
      this.formErrors = {};
      this.bayErrors = {};
      
      // Validate form
      if (!this.validateForm()) {
        this.errorMessage = 'Please fix the validation errors below';
        return;
      }
      
      this.saving = true;
      
      try {
        let response;
        if (this.isEdit) {
          response = await axios.put(`/greenhouses/${this.$route.params.id}`, this.form);
          this.successMessage = response.data.message || 'Greenhouse updated successfully!';
        } else {
          response = await axios.post('/greenhouses', this.form);
          this.successMessage = response.data.message || 'Greenhouse created successfully!';
        }
        
        // Clear success message after 3 seconds and redirect
        setTimeout(() => {
          this.$router.push('/greenhouses');
        }, 1500);
        
      } catch (error) {
        // Handle validation errors from server
        if (error.response?.status === 422 && error.response?.data?.errors) {
          const serverErrors = error.response.data.errors;
          const errorList = [];
          
          // Process server validation errors
          Object.keys(serverErrors).forEach(key => {
            serverErrors[key].forEach(msg => {
              errorList.push(msg);
              if (key === 'name') {
                this.formErrors.name = msg;
              } else if (key.startsWith('bays.')) {
                const index = key.match(/\d+/)?.[0];
                if (index !== undefined) {
                  this.bayErrors[index] = msg;
                }
              }
            });
          });
          
          this.validationErrors = errorList;
          this.errorMessage = 'Please fix the validation errors below';
        } else if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = error.message || 'Failed to save greenhouse. Please try again.';
        }
        
        console.error('Failed to save greenhouse:', error);
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>