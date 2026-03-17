<!-- resources/js/views/JobFields.vue -->
<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Fields for {{ jobType?.name }}
                </h1>
                <p class="text-gray-500 mt-1">Configure form fields</p>
            </div>
            <router-link
                :to="`/job-types/${jobTypeId}/fields/create`"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2"
            >
                <span>➕</span>
                <span>Add Field</span>
            </router-link>
        </div>

        <!-- Fields List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div
                v-for="(field, index) in fields"
                :key="field.id"
                class="p-4 border-b last:border-0 hover:bg-gray-50"
            >
                <span
                    class="px-2 py-1 text-xs rounded-full ml-2"
                    :class="
                        field.is_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-700'
                    "
                >
                    {{ field.is_active ? "Active" : "Inactive" }}
                </span>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-400">{{ index + 1 }}.</span>
                        <div>
                            <h3 class="font-medium">{{ field.label }}</h3>
                            <div
                                class="flex items-center space-x-2 text-sm text-gray-500"
                            >
                                <span class="px-2 py-1 bg-gray-100 rounded">{{
                                    field.field_type
                                }}</span>
                                <span
                                    v-if="field.is_required"
                                    class="text-red-500"
                                    >Required</span
                                >
                                <span
                                    v-if="field.dropdown_source"
                                    class="text-blue-500"
                                >
                                    Source: {{ field.dropdown_source.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="moveUp(index)"
                            v-if="index > 0"
                            class="text-gray-500"
                        >
                            ↑
                        </button>
                        <button
                            @click="moveDown(index)"
                            v-if="index < fields.length - 1"
                            class="text-gray-500"
                        >
                            ↓
                        </button>
                        <router-link
                            :to="`/job-types/${jobTypeId}/fields/${field.id}/edit`"
                            class="text-blue-600"
                            >✏️</router-link
                        >
                        <button
                            @click="confirmDelete(field)"
                            class="text-red-600 hover:text-red-800"
                            :disabled="!field.is_active"
                            :class="{
                                'opacity-50 cursor-not-allowed':
                                    !field.is_active,
                            }"
                            :title="
                                !field.is_active
                                    ? 'Cannot delete inactive field'
                                    : ''
                            "
                        >
                            🗑️
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            jobTypeId: this.$route.params.jobTypeId,
            jobType: null,
            fields: [],
        };
    },
    mounted() {
        this.loadData();
    },
    methods: {
        async loadData() {
            const [jobTypeRes, fieldsRes] = await Promise.all([
                axios.get(`/job-types/${this.jobTypeId}`),
                axios.get(`/job-types/${this.jobTypeId}/fields`),
            ]);
            this.jobType = jobTypeRes.data.data;
            this.fields = fieldsRes.data.data;
        },
        moveUp(index) {
            // Reorder logic
            if (index > 0) {
                [this.fields[index - 1], this.fields[index]] = [
                    this.fields[index],
                    this.fields[index - 1],
                ];
                this.updateOrder();
            }
        },
        moveDown(index) {
            if (index < this.fields.length - 1) {
                [this.fields[index], this.fields[index + 1]] = [
                    this.fields[index + 1],
                    this.fields[index],
                ];
                this.updateOrder();
            }
        },
        async updateOrder() {
            const orders = this.fields.map((f, i) => ({
                id: f.id,
                order: i + 1,
            }));
            await axios.post("/job-fields/reorder", { orders });
        },
        confirmDelete(field) {
            if (
                confirm(
                    `Delete field "${field.label}"? This action cannot be undone.`,
                )
            ) {
                this.deleteField(field.id);
            }
        },
        async deleteField(id) {
            try {
                await axios.delete(`/job-fields/${id}`);
                this.fields = this.fields.filter((f) => f.id !== id);
                alert("Field deleted successfully");
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Show the error message from the server
                    const message =
                        error.response.data.message ||
                        "Cannot delete this field because it is being used by existing job entries.";
                    alert(message);

                    // Option to deactivate instead
                    const field = this.fields.find((f) => f.id === id);
                    if (
                        field &&
                        confirm(
                            "Would you like to deactivate this field instead? It will be hidden from new forms but existing data will be preserved.",
                        )
                    ) {
                        await this.toggleActive(field);
                    }
                } else {
                    console.error("Failed to delete field:", error);
                    alert("Failed to delete field. Please try again.");
                }
            }
        },

        async toggleActive(field) {
            try {
                await axios.put(`/job-fields/${field.id}`, {
                    is_active: !field.is_active,
                });
                field.is_active = !field.is_active;
                alert(
                    `Field ${field.is_active ? "activated" : "deactivated"} successfully`,
                );
            } catch (error) {
                console.error("Failed to toggle field status:", error);
                alert("Failed to update field status");
            }
        },
    },
};
</script>
