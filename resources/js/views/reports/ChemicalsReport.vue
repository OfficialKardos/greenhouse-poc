<template>
  <div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Total Applications</div>
        <div class="text-2xl font-bold">{{ totalApplications }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Total Volume (oz)</div>
        <div class="text-2xl font-bold">{{ totalVolume.toFixed(2) }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Unique Chemicals</div>
        <div class="text-2xl font-bold">{{ uniqueChemicals }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Plants Treated</div>
        <div class="text-2xl font-bold">{{ uniquePlants }}</div>
      </div>
    </div>

    <!-- Chemical Breakdown -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 bg-gray-50 border-b">
        <h3 class="font-semibold">Chemical Usage Breakdown</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chemical</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applications</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Volume</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Volume</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plants</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Methods</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="chem in chemicalsBreakdown" :key="chem.name" class="hover:bg-gray-50">
              <td class="px-6 py-4 font-medium">{{ chem.name }}</td>
              <td class="px-6 py-4">{{ chem.applications }}</td>
              <td class="px-6 py-4">{{ chem.total_volume.toFixed(2) }} oz</td>
              <td class="px-6 py-4">{{ chem.avg_volume.toFixed(2) }} oz</td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                  <span v-for="plant in chem.plants" :key="plant" 
                        class="px-2 py-1 bg-gray-100 rounded text-xs">
                    {{ plant }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                  <span v-for="method in chem.methods" :key="method" 
                        class="px-2 py-1 bg-blue-100 rounded text-xs">
                    {{ method }}
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Applications -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 bg-gray-50 border-b">
        <h3 class="font-semibold">Recent Applications</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job #</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chemical</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plant</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photos</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="entry in entries" :key="entry.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">{{ formatDate(entry.submitted_at) }}</td>
              <td class="px-6 py-4 font-medium">{{ entry.job_number }}</td>
              <td class="px-6 py-4">{{ getFieldValue(entry, 'chemical_applied') || '—' }}</td>
              <td class="px-6 py-4">{{ getFieldValue(entry, 'plant') || '—' }}</td>
              <td class="px-6 py-4">
                {{ getFieldValue(entry, 'oz_per_100_gallons') || 
                   getFieldValue(entry, 'oz_per_100_gal') ||
                   getFieldValue(entry, 'volume') || 
                   getFieldValue(entry, 'oz') || '—' }} oz
              </td>
              <td class="px-6 py-4">{{ getFieldValue(entry, 'application_method') || '—' }}</td>
              <td class="px-6 py-4">{{ entry.greenhouse?.name || 'N/A' }} / {{ entry.bay?.name || 'N/A' }}</td>
              <td class="px-6 py-4">
                <div class="flex -space-x-2">
                  <div v-for="photo in entry.photos?.slice(0, 3)" :key="photo.id"
                       class="w-8 h-8 rounded-full border-2 border-white overflow-hidden cursor-pointer"
                       @click="$emit('view-photo', photo)">
                    <img :src="photo.thumbnail_url || photo.url" class="w-full h-full object-cover">
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { format } from 'date-fns';

export default {
  props: ['data'],
  computed: {
    entries() {
      return this.data.entries || [];
    },
    totalApplications() {
      return this.entries.length;
    },
    totalVolume() {
      if (!this.entries.length) return 0;
      
      let total = 0;
      this.entries.forEach(entry => {
        const volume = this.getFieldValue(entry, 'oz_per_100_gallons') || 
                       this.getFieldValue(entry, 'oz_per_100_gal') ||
                       this.getFieldValue(entry, 'volume') ||
                       this.getFieldValue(entry, 'oz');
        
        if (volume) {
          total += parseFloat(volume) || 0;
        }
      });
      return total;
    },
    uniqueChemicals() {
      if (!this.entries.length) return 0;
      
      const chemicals = new Set();
      this.entries.forEach(entry => {
        const chem = this.getFieldValue(entry, 'chemical_applied') ||
                    this.getFieldValue(entry, 'chemical') ||
                    this.getFieldValue(entry, 'chemical_used');
        if (chem && chem !== '—' && chem !== '') {
          chemicals.add(chem);
        }
      });
      return chemicals.size;
    },
    uniquePlants() {
      if (!this.entries.length) return 0;
      
      const plants = new Set();
      this.entries.forEach(entry => {
        const plant = this.getFieldValue(entry, 'plant') ||
                     this.getFieldValue(entry, 'plant_type');
        if (plant && plant !== '—' && plant !== '') {
          plants.add(plant);
        }
      });
      return plants.size;
    },
    chemicalsBreakdown() {
      if (!this.entries.length) return [];
      
      const chemMap = new Map();
      
      this.entries.forEach(entry => {
        const chemical = this.getFieldValue(entry, 'chemical_applied') ||
                        this.getFieldValue(entry, 'chemical') ||
                        this.getFieldValue(entry, 'chemical_used') ||
                        'Unknown';
        
        const plant = this.getFieldValue(entry, 'plant') ||
                     this.getFieldValue(entry, 'plant_type') ||
                     'Unknown';
        
        const volume = parseFloat(
          this.getFieldValue(entry, 'oz_per_100_gallons') ||
          this.getFieldValue(entry, 'oz_per_100_gal') ||
          this.getFieldValue(entry, 'volume') ||
          this.getFieldValue(entry, 'oz') || 0
        ) || 0;
        
        const method = this.getFieldValue(entry, 'application_method') ||
                      this.getFieldValue(entry, 'method') ||
                      'Unknown';
        
        if (!chemMap.has(chemical)) {
          chemMap.set(chemical, {
            name: chemical,
            total_volume: 0,
            applications: 0,
            plants: new Set(),
            methods: new Set()
          });
        }
        
        const chem = chemMap.get(chemical);
        chem.total_volume += volume;
        chem.applications++;
        if (plant !== 'Unknown') chem.plants.add(plant);
        if (method !== 'Unknown') chem.methods.add(method);
      });
      
      return Array.from(chemMap.values()).map(chem => ({
        ...chem,
        avg_volume: chem.applications > 0 ? chem.total_volume / chem.applications : 0,
        plants: Array.from(chem.plants),
        methods: Array.from(chem.methods)
      }));
    }
  },
  methods: {
    formatDate(date) {
      return date ? format(new Date(date), 'MMM d, yyyy') : 'N/A';
    },
    getFieldValue(entry, fieldName) {
      if (!entry || !entry.values) return null;
      
      let value = entry.values.find(v => v.job_field?.field_name === fieldName);
      if (value?.value) return value.value;
      
      value = entry.values.find(v => 
        v.job_field?.field_name?.toLowerCase() === fieldName.toLowerCase()
      );
      if (value?.value) return value.value;
      
      value = entry.values.find(v => 
        v.job_field?.field_name?.toLowerCase().includes(fieldName.toLowerCase())
      );
      if (value?.value) return value.value;
      
      return null;
    }
  }
};
</script>