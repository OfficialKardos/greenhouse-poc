<!-- resources/js/views/Users.vue -->
<template>
  <div class="container mx-auto px-4 py-4 sm:px-6 sm:py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
        <p class="text-gray-500 mt-1 text-sm sm:text-base">Manage system users and their access levels</p>
      </div>
      <button @click="openUserModal()" 
              class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center justify-center space-x-2 w-full sm:w-auto">
        <i class="fas fa-plus"></i>
        <span>Add User</span>
      </button>
    </div>

    <!-- Users - Mobile Card View -->
    <div class="sm:hidden space-y-4">
      <div v-for="user in users" :key="user.id" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <!-- User Header -->
        <div class="flex items-center space-x-3 mb-3">
          <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
            {{ userInitials(user.name) }}
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-gray-900 truncate">{{ user.name }}</h3>
            <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
          </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
          <div>
            <span class="text-gray-500 block text-xs">Role</span>
            <span class="px-2 py-1 text-xs rounded-full inline-block mt-1" 
                  :class="roleClass(user.role)">
              {{ user.role }}
            </span>
          </div>
          <div>
            <span class="text-gray-500 block text-xs">Status</span>
            <span class="px-2 py-1 text-xs rounded-full inline-block mt-1" 
                  :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
              {{ user.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>
        </div>

        <!-- Access Badges -->
        <div class="mb-3">
          <span class="text-gray-500 text-xs block mb-2">Access</span>
          <div class="flex flex-wrap gap-2">
            <span v-if="user.can_access_admin" class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
              <i class="fas fa-desktop mr-1"></i>Admin Panel
            </span>
            <span v-if="user.can_access_mobile" class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
              <i class="fas fa-mobile-alt mr-1"></i>Mobile App
            </span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex space-x-2 pt-2 border-t border-gray-100">
          <button @click="openUserModal(user)" class="flex-1 bg-blue-50 text-blue-600 px-3 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
            <i class="fas fa-edit mr-1"></i> Edit
          </button>
          <button @click="confirmDelete(user)" class="flex-1 bg-red-50 text-red-600 px-3 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
            <i class="fas fa-trash mr-1"></i> Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Users - Desktop Table -->
    <div class="hidden sm:block bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Access</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ userInitials(user.name) }}
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full" 
                      :class="roleClass(user.role)">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex space-x-2">
                  <span v-if="user.can_access_admin" class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                    <i class="fas fa-desktop mr-1"></i>Admin
                  </span>
                  <span v-if="user.can_access_mobile" class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                    <i class="fas fa-mobile-alt mr-1"></i>Mobile
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full" 
                      :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
                  {{ user.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="openUserModal(user)" class="text-blue-600 hover:text-blue-900 mr-3">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="confirmDelete(user)" class="text-red-600 hover:text-red-900">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- User Modal (already responsive) -->
    <div v-if="showUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 p-4">
      <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4 sticky top-0 bg-white py-2">
          <h3 class="text-lg font-semibold text-gray-900">{{ modalTitle }}</h3>
          <button @click="closeUserModal" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <form @submit.prevent="saveUser">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Name -->
            <div class="col-span-1 sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
              <input type="text" v-model="userForm.name" required
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Email -->
            <div class="col-span-1 sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
              <input type="email" v-model="userForm.email" required
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Password -->
            <div class="col-span-1 sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ isEditing ? 'New Password (leave blank to keep current)' : 'Password *' }}
              </label>
              <input type="password" v-model="userForm.password" 
                     :required="!isEditing"
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Role -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
              <select v-model="userForm.role" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="admin">Admin</option>
                <option value="supervisor">Supervisor</option>
                <option value="worker">Worker</option>
              </select>
            </div>

            <!-- Access Permissions -->
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">Access Permissions</label>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input type="checkbox" v-model="userForm.can_access_admin" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                  <span class="ml-2 text-sm text-gray-700">Admin Panel Access</span>
                </label>
                <label class="flex items-center">
                  <input type="checkbox" v-model="userForm.can_access_mobile" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                  <span class="ml-2 text-sm text-gray-700">Mobile App Access</span>
                </label>
              </div>
            </div>

            <!-- Active Status -->
            <div class="col-span-1">
              <label class="flex items-center">
                <input type="checkbox" v-model="userForm.is_active" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Active</span>
              </label>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
            <button type="button" @click="closeUserModal"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 w-full sm:w-auto order-2 sm:order-1">
              Cancel
            </button>
            <button type="submit" :disabled="saving"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 w-full sm:w-auto order-1 sm:order-2">
              {{ saving ? 'Saving...' : (isEditing ? 'Update User' : 'Create User') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal (already responsive) -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 p-4">
      <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Delete</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete user "{{ deletingUser?.name }}"? This action cannot be undone.</p>
        <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
          <button @click="showDeleteModal = false"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 w-full sm:w-auto">
            Cancel
          </button>
          <button @click="deleteUser"
                  class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 w-full sm:w-auto">
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="users.length === 0" class="text-center py-12">
      <div class="text-6xl text-gray-300 mb-4">👥</div>
      <h3 class="text-lg font-medium text-gray-700 mb-2">No users found</h3>
      <p class="text-gray-500">Get started by adding your first user</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { format } from 'date-fns';

export default {
  data() {
    return {
      users: [],
      showUserModal: false,
      showDeleteModal: false,
      isEditing: false,
      saving: false,
      deletingUser: null,
      userForm: {
        name: '',
        email: '',
        password: '',
        role: 'worker',
        can_access_admin: false,
        can_access_mobile: true,
        is_active: true
      }
    };
  },
  computed: {
    modalTitle() {
      return this.isEditing ? 'Edit User' : 'Add New User';
    }
  },
  mounted() {
    this.loadUsers();
  },
  methods: {
    async loadUsers() {
      try {
        const response = await axios.get('/users');
        this.users = response.data.data;
      } catch (error) {
        console.error('Failed to load users:', error);
      }
    },
    userInitials(name) {
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    },
    roleClass(role) {
      const classes = {
        admin: 'bg-purple-100 text-purple-700',
        supervisor: 'bg-blue-100 text-blue-700',
        worker: 'bg-green-100 text-green-700'
      };
      return classes[role] || 'bg-gray-100 text-gray-700';
    },
    formatDate(date) {
      return date ? format(new Date(date), 'MMM d, yyyy') : 'N/A';
    },
    openUserModal(user = null) {
      if (user) {
        this.isEditing = true;
        this.userForm = {
          id: user.id,
          name: user.name,
          email: user.email,
          password: '',
          role: user.role,
          can_access_admin: user.can_access_admin,
          can_access_mobile: user.can_access_mobile,
          is_active: user.is_active
        };
      } else {
        this.isEditing = false;
        this.userForm = {
          name: '',
          email: '',
          password: '',
          role: 'worker',
          can_access_admin: false,
          can_access_mobile: true,
          is_active: true
        };
      }
      this.showUserModal = true;
    },
    closeUserModal() {
      this.showUserModal = false;
      this.userForm = {
        name: '',
        email: '',
        password: '',
        role: 'worker',
        can_access_admin: false,
        can_access_mobile: true,
        is_active: true
      };
    },
    async saveUser() {
      this.saving = true;
      try {
        if (this.isEditing) {
          await axios.put(`/users/${this.userForm.id}`, this.userForm);
        } else {
          await axios.post('/users', this.userForm);
        }
        await this.loadUsers();
        this.closeUserModal();
      } catch (error) {
        console.error('Failed to save user:', error);
        alert('Failed to save user. Please check the form and try again.');
      } finally {
        this.saving = false;
      }
    },
    confirmDelete(user) {
      this.deletingUser = user;
      this.showDeleteModal = true;
    },
    async deleteUser() {
      try {
        await axios.delete(`/users/${this.deletingUser.id}`);
        await this.loadUsers();
        this.showDeleteModal = false;
        this.deletingUser = null;
      } catch (error) {
        console.error('Failed to delete user:', error);
        alert('Failed to delete user.');
      }
    }
  }
};
</script>