<!-- resources/js/views/Dropdowns.vue -->
<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Dropdown Lists</h1>
        <p class="text-gray-500 mt-1">Manage dropdown options for forms</p>
      </div>
      <router-link to="/dropdowns/create" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2">
        <span>➕</span>
        <span>Add Dropdown</span>
      </router-link>
    </div>

    <!-- Dropdown Lists Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="list in dropdowns" :key="list.id" 
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">{{ list.name }}</h3>
          <span class="text-sm text-gray-500">{{ list.values?.length || 0 }} values</span>
        </div>
        
        <p class="text-gray-600 text-sm mb-4">{{ list.description || 'No description' }}</p>
        
        <!-- Preview of values -->
        <div class="mb-4">
          <div class="flex flex-wrap gap-2">
            <span v-for="value in list.values?.slice(0, 3)" :key="value.id" 
                  class="px-2 py-1 bg-gray-100 rounded text-xs">
              {{ value.label || value.value }}
            </span>
            <span v-if="list.values?.length > 3" class="text-xs text-gray-500">
              +{{ list.values.length - 3 }} more
            </span>
          </div>
        </div>

        <div class="flex justify-between items-center pt-4 border-t">
          <router-link :to="`/dropdowns/${list.id}/values`" 
                     class="text-green-600 hover:text-green-800 text-sm">
            Manage Values →
          </router-link>
          <div class="flex space-x-2">
            <router-link :to="`/dropdowns/${list.id}/edit`" class="text-blue-600">✏️</router-link>
            <button @click="confirmDelete(list)" class="text-red-600">🗑️</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      dropdowns: []
    };
  },
  mounted() {
    this.loadDropdowns();
  },
  methods: {
    async loadDropdowns() {
      const response = await axios.get('/dropdown-lists');
      this.dropdowns = response.data.data;
    },
    confirmDelete(list) {
      if (confirm(`Delete dropdown list "${list.name}"?`)) {
        this.deleteDropdown(list.id);
      }
    },
    async deleteDropdown(id) {
      await axios.delete(`/dropdown-lists/${id}`);
      this.dropdowns = this.dropdowns.filter(d => d.id !== id);
    }
  }
};
</script>