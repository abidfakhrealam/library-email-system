<template>
    <div class="email-list">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th v-for="header in headers" :key="header.key" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ header.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="email in emails" :key="email.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ email.subject }}</div>
                            <div class="text-sm text-gray-500">{{ truncate(email.body_preview, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ email.from_name }}</div>
                            <div class="text-sm text-gray-500">{{ email.from_email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatDate(email.received_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="statusClass(email.status)">
                                {{ formatStatus(email.status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ email.assignee ? email.assignee.name : 'Unassigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button @click="$emit('select', email)" 
                                class="text-blue-600 hover:text-blue-900 mr-3">
                                View
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-4" v-if="pagination">
            <Pagination :data="pagination" @pagination-change-page="$emit('paginate', $event)" />
        </div>
    </div>
</template>

<script>
import Pagination from '@/Components/Pagination.vue';

export default {
    components: {
        Pagination
    },
    props: {
        emails: {
            type: Array,
            required: true
        },
        headers: {
            type: Array,
            default: () => [
                { key: 'subject', label: 'Subject' },
                { key: 'from', label: 'From' },
                { key: 'received_at', label: 'Received' },
                { key: 'status', label: 'Status' },
                { key: 'assignee', label: 'Assigned To' },
                { key: 'actions', label: 'Actions' }
            ]
        },
        pagination: {
            type: Object,
            default: null
        }
    },
    methods: {
        truncate(text, length) {
            return text.length > length ? text.substring(0, length) + '...' : text;
        },
        formatDate(date) {
            return new Date(date).toLocaleString();
        },
        formatStatus(status) {
            return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        },
        statusClass(status) {
            const classes = {
                'unassigned': 'bg-gray-100 text-gray-800',
                'assigned': 'bg-blue-100 text-blue-800',
                'in_progress': 'bg-yellow-100 text-yellow-800',
                'completed': 'bg-green-100 text-green-800'
            };
            return `px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${classes[status]}`;
        }
    }
}
</script>
