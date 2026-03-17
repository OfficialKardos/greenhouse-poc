<template>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            📊 Reports & Analytics
        </h1>

        <!-- Report Type Cards -->
        <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-8"
        >
            <div
                @click="activeTab = 'summary'"
                class="bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-lg transition-all"
                :class="{
                    'ring-2 ring-green-500 bg-green-50':
                        activeTab === 'summary',
                }"
            >
                <div class="text-2xl mb-2">📋</div>
                <h3 class="font-semibold">Summary</h3>
                <p class="text-xs text-gray-500">All entries overview</p>
            </div>

            <div
                @click="activeTab = 'chemicals'"
                class="bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-lg transition-all"
                :class="{
                    'ring-2 ring-green-500 bg-green-50':
                        activeTab === 'chemicals',
                }"
            >
                <div class="text-2xl mb-2">🧪</div>
                <h3 class="font-semibold">Chemicals</h3>
                <p class="text-xs text-gray-500">Usage & applications</p>
            </div>

            <div
                @click="activeTab = 'soil'"
                class="bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-lg transition-all"
                :class="{
                    'ring-2 ring-green-500 bg-green-50': activeTab === 'soil',
                }"
            >
                <div class="text-2xl mb-2">🌱</div>
                <h3 class="font-semibold">Soil Samples</h3>
                <p class="text-xs text-gray-500">pH & EC trends</p>
            </div>

            <div
                @click="activeTab = 'pests'"
                class="bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-lg transition-all"
                :class="{
                    'ring-2 ring-green-500 bg-green-50': activeTab === 'pests',
                }"
            >
                <div class="text-2xl mb-2">🐛</div>
                <h3 class="font-semibold">Pest Incidents</h3>
                <p class="text-xs text-gray-500">Disease tracking</p>
            </div>

            <div
                @click="activeTab = 'growth'"
                class="bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-lg transition-all"
                :class="{
                    'ring-2 ring-green-500 bg-green-50': activeTab === 'growth',
                }"
            >
                <div class="text-2xl mb-2">📈</div>
                <h3 class="font-semibold">Growth</h3>
                <p class="text-xs text-gray-500">Plant tracking</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Filter Results</h2>
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

                <div v-if="activeTab !== 'summary'">
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Plant</label
                    >
                    <input
                        type="text"
                        v-model="filters.plant"
                        placeholder="Filter by plant"
                        class="w-full border rounded-md px-3 py-2"
                    />
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

            <div class="flex justify-end mt-4 space-x-3">
                <button
                    @click="exportReport"
                    class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 flex items-center"
                    :disabled="!reportData"
                >
                    📥 Export CSV
                </button>
                <button
                    @click="generateReport"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center"
                >
                    🔄 Generate Report
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
            v-else-if="!reportData"
            class="bg-white rounded-lg shadow p-12 text-center"
        >
            <span class="text-6xl mb-4 block">📊</span>
            <h3 class="text-xl font-medium text-gray-700 mb-2">
                No Data Available
            </h3>
            <p class="text-gray-500">
                Select filters and generate a report to see results
            </p>
        </div>

        <!-- Report Results -->
        <div v-else>
            <!-- Dynamic Component Based on Active Tab -->
            <component
                :is="activeTabComponent"
                :data="reportData"
                @view-photo="viewPhoto"
            />
        </div>

        <!-- Photo Viewer Modal -->
        <div
            v-if="selectedPhoto"
            class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4"
            @click="selectedPhoto = null"
        >
            <div class="relative max-w-5xl max-h-full">
                <img
                    :src="selectedPhoto.url"
                    class="max-h-full max-w-full object-contain"
                />
                <button
                    class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300"
                    @click="selectedPhoto = null"
                >
                    ✕
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import SummaryReport from "./reports/SummaryReport.vue";
import ChemicalsReport from "./reports/ChemicalsReport.vue";
import SoilReport from "./reports/SoilReport.vue";
import PestsReport from "./reports/PestsReport.vue";
import GrowthReport from "./reports/GrowthReport.vue";

export default {
    components: {
        SummaryReport,
        ChemicalsReport,
        SoilReport,
        PestsReport,
        GrowthReport,
    },

    data() {
        return {
            activeTab: "summary",
            greenhouses: [],
            filters: {
                greenhouse_id: "",
                plant: "",
                from_date: "",
                to_date: "",
            },
            reportData: null,
            loading: false,
            selectedPhoto: null,
        };
    },

    computed: {
        activeTabComponent() {
            const components = {
                summary: "SummaryReport",
                chemicals: "ChemicalsReport",
                soil: "SoilReport",
                pests: "PestsReport",
                growth: "GrowthReport",
            };
            return components[this.activeTab] || "SummaryReport";
        },
    },

    mounted() {
        this.loadGreenhouses();
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

        async generateReport() {
            this.loading = true;
            this.reportData = null;

            try {
                const params = new URLSearchParams();
                Object.entries(this.filters).forEach(([key, value]) => {
                    if (value) params.append(key, value);
                });

                const response = await axios.get(
                    `/reports/${this.activeTab}?${params}`,
                );

                if (response.data.success) {
                    this.reportData = response.data.data;
                } else {
                    alert(
                        "Failed to generate report: " +
                            (response.data.message || "Unknown error"),
                    );
                }
            } catch (error) {
                console.error("Failed to generate report:", error);
                alert("Error generating report. Check console for details.");
            } finally {
                this.loading = false;
            }
        },

        async exportReport() {
            try {
                const token = localStorage.getItem("token");
                const params = new URLSearchParams(this.filters);
                params.append("type", this.activeTab);

                const response = await fetch(`/api/reports/export?${params}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "text/csv",
                    },
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error("Export failed:", text);
                    throw new Error("Export failed");
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = `${this.activeTab}_report.csv`;
                a.click();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error("Export error:", error);
                alert("Export failed");
            }
        },

        viewPhoto(photo) {
            this.selectedPhoto = photo;
        },
    },
};
</script>
