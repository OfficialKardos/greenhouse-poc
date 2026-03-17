<template>
  <div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Total Entries</div>
        <div class="text-2xl font-bold">{{ data.summary?.total_entries || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Unique Plants</div>
        <div class="text-2xl font-bold">{{ data.summary?.unique_plants || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">With Photos</div>
        <div class="text-2xl font-bold">{{ data.summary?.with_photos || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Total Photos</div>
        <div class="text-2xl font-bold">{{ data.summary?.total_photos || 0 }}</div>
      </div>
    </div>

    <!-- Plant Selection -->
    <div class="bg-white rounded-lg shadow p-4">
      <div class="flex flex-wrap gap-2">
        <button v-for="plant in data.plants" :key="plant"
                @click="selectedPlant = plant"
                class="px-4 py-2 rounded-lg"
                :class="selectedPlant === plant ? 'bg-green-600 text-white' : 'bg-gray-100 hover:bg-gray-200'">
          {{ plant }}
        </button>
      </div>
    </div>

    <!-- Growth Timeline -->
    <div v-if="selectedPlant" class="space-y-4">
      <div v-for="(entry, index) in data.growth[selectedPlant]" :key="entry.id"
           class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
          <div>
            <span class="font-semibold">Week {{ entry.week || 'N/A' }}</span>
            <span class="text-sm text-gray-500 ml-3">{{ entry.date }}</span>
          </div>
          <span class="text-sm text-gray-500">{{ entry.greenhouse }} / {{ entry.bay }}</span>
        </div>
        
        <div class="p-6">
          <!-- Photo Gallery -->
          <div v-if="entry.photos?.length" class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="photo in entry.photos" :key="photo.id"
                 class="cursor-pointer" @click="$emit('view-photo', photo)">
              <img :src="photo.thumbnail_url || photo.url" 
                   class="w-full h-32 object-cover rounded-lg border">
              <p class="text-xs text-gray-500 mt-1 truncate">{{ photo.filename }}</p>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-400">
            No photos for this entry
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['data'],
  data() {
    return {
      selectedPlant: this.data.plants?.[0] || null
    };
  }
};
</script>