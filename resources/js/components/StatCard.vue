<!-- resources/js/components/StatCard.vue -->
<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all hover:scale-[1.02]">
    <div class="flex items-center justify-between mb-4">
      <div class="w-12 h-12" :class="bgColor + ' rounded-xl flex items-center justify-center'">
        <span class="text-2xl">{{ icon }}</span>
      </div>
      <span class="text-sm text-gray-400">Total</span>
    </div>
    <h3 class="text-3xl font-bold text-gray-800">{{ value }}</h3>
    <p class="text-gray-500 text-sm mt-1">{{ label }}</p>
    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center">
      <span class="text-sm font-medium flex items-center" :class="trendColor">
        {{ trend }} <span class="text-gray-400 text-xs ml-1">from last month</span>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    icon: String,
    value: [Number, String],
    label: String,
    trend: String,
    trendColor: String,
    bgColor: String
  }
};
</script>