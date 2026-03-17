<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Soil pH & EC Trends</h1>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Greenhouse</label
                    >
                    <select
                        v-model="filters.greenhouse_id"
                        class="w-full border rounded-md px-3 py-2"
                    >
                        <option value="">All Greenhouses</option>
                        <option
                            v-for="gh in greenhouses"
                            :key="gh.id"
                            :value="gh.id"
                        >
                            {{ gh.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Plant</label
                    >
                    <select
                        v-model="filters.plant"
                        class="w-full border rounded-md px-3 py-2"
                    >
                        <option value="">All Plants</option>
                        <option
                            v-for="plant in plants"
                            :key="plant"
                            :value="plant"
                        >
                            {{ plant }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >From Date</label
                    >
                    <input
                        type="date"
                        v-model="filters.from_date"
                        class="w-full border rounded-md px-3 py-2"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >To Date</label
                    >
                    <input
                        type="date"
                        v-model="filters.to_date"
                        class="w-full border rounded-md px-3 py-2"
                    />
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button
                    @click="applyFilters"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
                    Apply Filters
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-12">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"
            ></div>
        </div>

        <!-- No Data State -->
        <div
            v-else-if="!trendsData || Object.keys(trendsData).length === 0"
            class="bg-white rounded-lg shadow p-12 text-center"
        >
            <span class="text-6xl mb-4 block">🌱</span>
            <h3 class="text-xl font-medium text-gray-700 mb-2">
                No soil sample data found
            </h3>
            <p class="text-gray-500">
                Try adjusting your filters or adding some soil samples
            </p>
        </div>

        <!-- Data Display -->
        <div v-else>
            <!-- Plant Selection Tabs -->
            <div class="bg-white rounded-lg shadow p-4 mb-6 overflow-x-auto">
                <div class="flex space-x-2">
                    <button
                        v-for="plant in Object.keys(trendsData)"
                        :key="plant"
                        @click="selectedPlant = plant"
                        class="px-4 py-2 rounded-lg whitespace-nowrap"
                        :class="
                            selectedPlant === plant
                                ? 'bg-green-600 text-white'
                                : 'bg-gray-100 hover:bg-gray-200'
                        "
                    >
                        {{ plant }}
                    </button>
                </div>
            </div>

            <!-- Selected Plant Data -->
            <div
                v-if="selectedPlant && trendsData[selectedPlant]"
                class="space-y-6"
            >
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-sm text-gray-500">Total Samples</div>
                        <div class="text-2xl font-bold">
                            {{ trendsData[selectedPlant].length }}
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-sm text-gray-500">Avg pH</div>
                        <div class="text-2xl font-bold">
                            {{ averagePH.toFixed(2) }}
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-sm text-gray-500">Avg EC</div>
                        <div class="text-2xl font-bold">
                            {{ averageEC.toFixed(2) }}
                        </div>
                    </div>
                </div>

                <!-- Chart Placeholder -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        pH Trend - {{ selectedPlant }}
                    </h3>
                    <div
                        class="h-64 flex items-center justify-center bg-gray-50 rounded"
                    >
                        <p class="text-gray-400">
                            Chart visualization will appear here
                        </p>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-semibold">Sample History</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Week
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        pH
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        EC
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Location
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Photos
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="sample in trendsData[selectedPlant]"
                                    :key="sample.date"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ sample.date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        Week {{ sample.week || "N/A" }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getPHClass(sample.ph)">{{
                                            sample.ph || "—"
                                        }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getECClass(sample.ec)">{{
                                            sample.ec || "—"
                                        }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ sample.greenhouse }} /
                                        {{ sample.bay }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex -space-x-2">
                                            <div
                                                v-for="photo in sample.photos?.slice(
                                                    0,
                                                    3,
                                                )"
                                                :key="photo.id"
                                                class="w-8 h-8 rounded-full border-2 border-white overflow-hidden cursor-pointer"
                                                @click="viewPhoto(photo)"
                                            >
                                                <img
                                                    :src="
                                                        photo.thumbnail_url ||
                                                        photo.url
                                                    "
                                                    class="w-full h-full object-cover"
                                                />
                                            </div>
                                            <div
                                                v-if="sample.photos?.length > 3"
                                                class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs"
                                            >
                                                +{{ sample.photos.length - 3 }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo Viewer Modal -->
        <div
            v-if="selectedPhoto"
            class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center"
            @click="selectedPhoto = null"
        >
            <img :src="selectedPhoto.url" class="max-h-screen max-w-full p-4" />
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            greenhouses: [],
            plants: [],
            trendsData: null,
            filters: {
                greenhouse_id: "",
                plant: "",
                from_date: "",
                to_date: "",
            },
            selectedPlant: null,
            loading: false,
            selectedPhoto: null,
        };
    },
    computed: {
        averagePH() {
            if (!this.selectedPlant || !this.trendsData[this.selectedPlant])
                return 0;
            const samples = this.trendsData[this.selectedPlant].filter(
                (s) => s.ph,
            );
            if (samples.length === 0) return 0;
            const sum = samples.reduce((acc, s) => acc + parseFloat(s.ph), 0);
            return sum / samples.length;
        },
        averageEC() {
            if (!this.selectedPlant || !this.trendsData[this.selectedPlant])
                return 0;
            const samples = this.trendsData[this.selectedPlant].filter(
                (s) => s.ec,
            );
            if (samples.length === 0) return 0;
            const sum = samples.reduce((acc, s) => acc + parseFloat(s.ec), 0);
            return sum / samples.length;
        },
    },
    mounted() {
        this.loadGreenhouses();
        this.loadData();
    },
    methods: {
        async loadGreenhouses() {
            try {
                const response = await axios.get("/greenhouses");
                this.greenhouses = response.data.data || [];
            } catch (error) {
                console.error("Failed to load greenhouses:", error);
            }
        },
        async loadData() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                Object.entries(this.filters).forEach(([key, value]) => {
                    if (value) params.append(key, value);
                });

                const response = await axios.get(`/reports/soil?${params}`);

                // DEBUG: Log the full response
                console.log("Full API Response:", response);
                console.log("Response data:", response.data);
                console.log("Success:", response.data?.success);
                console.log("Data object:", response.data?.data);
                console.log("Trends:", response.data?.data?.trends);

                // Rest of your code...
            } catch (error) {
                console.error("Failed to load soil trends:", error);
            }
        },
        applyFilters() {
            this.loadData();
        },
        getPHClass(ph) {
            if (!ph) return "";
            const val = parseFloat(ph);
            if (isNaN(val)) return "";
            if (val < 5.5) return "text-red-600 font-medium";
            if (val > 7.5) return "text-red-600 font-medium";
            if (val >= 6.0 && val <= 7.0) return "text-green-600 font-medium";
            return "text-yellow-600 font-medium";
        },
        getECClass(ec) {
            if (!ec) return "";
            const val = parseFloat(ec);
            if (isNaN(val)) return "";
            if (val < 0.5) return "text-yellow-600 font-medium";
            if (val > 2.0) return "text-red-600 font-medium";
            if (val >= 1.0 && val <= 1.5) return "text-green-600 font-medium";
            return "text-blue-600 font-medium";
        },
        viewPhoto(photo) {
            this.selectedPhoto = photo;
        },
    },
};
</script>
