<!-- resources/js/components/TopBar.vue -->
<template>
  <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-sm border-b border-gray-200/50 px-8 py-4 flex items-center justify-between">
    <h2 class="text-xl font-semibold text-gray-800">{{ title }}</h2>
    
    <div class="flex items-center space-x-4">
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    },
    searchPlaceholder: {
      type: String,
      default: 'Search...'
    }
  },
  data() {
    return {
      searchQuery: ''
    };
  }
};
</script>