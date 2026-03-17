<!-- resources/js/views/DropdownValues.vue -->
<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Dropdown Values: {{ dropdown?.name }}</h1>
        <p class="text-gray-500 mt-1">Manage options for this dropdown list</p>
      </div>
      <button @click="showForm = true" 
              class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2">
        <span>➕</span>
        <span>Add Value</span>
      </button>
    </div>

    <!-- Values List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Order</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Value</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Label</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="(value, index) in values" :key="value.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center space-x-2">
                <button @click="moveUp(index)" :disabled="index === 0" 
                        class="text-gray-400 hover:text-gray-600 disabled:opacity-50">↑</button>
                <span>{{ value.sort_order || index + 1 }}</span>
                <button @click="moveDown(index)" :disabled="index === values.length-1" 
                        class="text-gray-400 hover:text-gray-600 disabled:opacity-50">↓</button>
              </div>
            </td>
            <td class="px-6 py-4 font-mono text-sm">{{ value.value }}</td>
            <td class="px-6 py-4">{{ value.label || value.value }}</td>
            <td class="px-6 py-4">
              <div class="flex space-x-2">
                <button @click="editValue(value)" class="text-blue-600">✏️</button>
                <button @click="confirmDelete(value)" class="text-red-600">🗑️</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
      <div class="bg-white rounded-xl p-6 max-w-md w-full">
        <h3 class="text-xl font-bold mb-4">{{ editingValue ? 'Edit' : 'Add' }} Value</h3>
        <form @submit.prevent="saveValue">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-2">Value *</label>
              <input v-model="form.value" type="text" required
                     class="w-full border rounded-lg px-4 py-2"
                     placeholder="e.g., tomato">
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">Label (Display Text)</label>
              <input v-model="form.label" type="text"
                     class="w-full border rounded-lg px-4 py-2"
                     placeholder="e.g., Tomato Plant">
            </div>
            <div class="flex items-center">
              <input v-model="form.is_active" type="checkbox" id="is_active" class="mr-2">
              <label for="is_active">Active</label>
            </div>
          </div>
          <div class="flex justify-end space-x-4 mt-6">
            <button type="button" @click="showForm = false" class="px-4 py-2 border rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      dropdownId: this.$route.params.id,
      dropdown: null,
      values: [],
      showForm: false,
      editingValue: null,
      form: {
        value: '',
        label: '',
        is_active: true
      }
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    async loadData() {
      try {
        const [dropdownRes, valuesRes] = await Promise.all([
          axios.get(`/dropdown-lists/${this.dropdownId}`),
          axios.get(`/dropdown-lists/${this.dropdownId}/values`)
        ]);
        this.dropdown = dropdownRes.data.data;
        this.values = valuesRes.data.data;
      } catch (error) {
        console.error('Failed to load data:', error);
      }
    },
    editValue(value) {
      this.editingValue = value;
      this.form = { ...value };
      this.showForm = true;
    },
    async saveValue() {
      try {
        if (this.editingValue) {
          await axios.put(`/dropdown-values/${this.editingValue.id}`, this.form);
        } else {
          await axios.post(`/dropdown-lists/${this.dropdownId}/values`, this.form);
        }
        this.showForm = false;
        this.editingValue = null;
        this.form = { value: '', label: '', is_active: true };
        this.loadData();
      } catch (error) {
        console.error('Failed to save:', error);
      }
    },
    moveUp(index) {
      if (index > 0) {
        [this.values[index-1], this.values[index]] = [this.values[index], this.values[index-1]];
        this.updateOrder();
      }
    },
    moveDown(index) {
      if (index < this.values.length - 1) {
        [this.values[index], this.values[index+1]] = [this.values[index+1], this.values[index]];
        this.updateOrder();
      }
    },
    async updateOrder() {
      const orders = this.values.map((v, i) => ({ id: v.id, order: i + 1 }));
      await axios.post('/dropdown-values/reorder', { orders });
    },
    confirmDelete(value) {
      if (confirm(`Delete "${value.label || value.value}"?`)) {
        this.deleteValue(value.id);
      }
    },
    async deleteValue(id) {
      await axios.delete(`/dropdown-values/${id}`);
      this.values = this.values.filter(v => v.id !== id);
    }
  }
};
</script>