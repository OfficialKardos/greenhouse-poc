<!-- resources/js/views/Dashboard.vue -->
<template>
  <div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-gray-800 p-8 text-white">
      <div class="absolute top-0 right-0 w-64 h-64 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
      <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
      <div class="relative">
        <h1 class="text-3xl font-bold mb-2">Welcome back, {{ user?.name }}! 👋</h1>
        <p class="text-gray-300">Here's what's happening in your greenhouses today.</p>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatCard
        v-for="stat in stats"
        :key="stat.label"
        :icon="stat.icon"
        :value="stat.value"
        :label="stat.label"
        :trend="stat.trend"
        :trend-color="stat.trendColor"
        :bg-color="stat.bgColor"
      />
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-6">Recent Activity</h2>
      <ActivityList :entries="recentEntries" />
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import StatCard from '../components/StatCard.vue';
import ActivityList from '../components/ActivityList.vue';

export default {
  components: {
    StatCard,
    ActivityList
  },
  props: {
    user: Object
  },
  data() {
    return {
      recentEntries: [],
      statsData: {
        greenhouses: 0,
        bays: 0,
        jobTypes: 0,
        todayEntries: 0
      }
    };
  },
  computed: {
    stats() {
      return [
        {
          icon: '🏢',
          value: this.statsData.greenhouses,
          label: 'Greenhouses',
          trend: '+12%',
          trendColor: 'text-green-600',
          bgColor: 'bg-green-100'
        },
        {
          icon: '🧩',
          value: this.statsData.bays,
          label: 'Bays',
          trend: '+8%',
          trendColor: 'text-blue-600',
          bgColor: 'bg-blue-100'
        },
        {
          icon: '📋',
          value: this.statsData.jobTypes,
          label: 'Job Types',
          trend: '0%',
          trendColor: 'text-purple-600',
          bgColor: 'bg-purple-100'
        },
        {
          icon: '📝',
          value: this.statsData.todayEntries,
          label: "Today's Entries",
          trend: '+24%',
          trendColor: 'text-yellow-600',
          bgColor: 'bg-yellow-100'
        }
      ];
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    async loadData() {
      try {
        const [greenhouses, bays, jobTypes, entries] = await Promise.all([
          axios.get('/greenhouses'),
          axios.get('/bays'),
          axios.get('/job-types'),
          axios.get('/job-entries?per_page=5')
        ]);

        this.statsData.greenhouses = greenhouses.data.data.length;
        this.statsData.bays = bays.data.data.length;
        this.statsData.jobTypes = jobTypes.data.data.length;
        
        const today = new Date().toISOString().split('T')[0];
        const todayEntries = entries.data.data.filter(e => 
          e.submitted_at?.startsWith(today)
        );
        this.statsData.todayEntries = todayEntries.length;
        
        this.recentEntries = entries.data.data;
      } catch (error) {
        console.error('Failed to load dashboard data:', error);
      }
    }
  }
};
</script>