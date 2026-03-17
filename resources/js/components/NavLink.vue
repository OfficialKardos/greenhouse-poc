<!-- resources/js/components/NavLink.vue -->
<template>
  <router-link 
    :to="to" 
    class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all group"
    :class="{ 'bg-green-50 text-green-700 shadow-sm': active }"
  >
    <span class="text-xl group-hover:scale-110 transition-transform">{{ icon }}</span>
    <span class="font-medium"><slot /></span>
  </router-link>
</template>

<script>
export default {
  props: {
    to: {
      type: [String, Object],
      required: true
    },
    icon: {
      type: String,
      required: true
    },
    active: {
      type: Boolean,
      default: false
    }
  }
};
</script>