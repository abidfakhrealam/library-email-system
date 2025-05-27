<template>
  <div class="email-viewer bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-start mb-6">
      <div>
        <h2 class="text-2xl font-bold">{{ email.subject }}</h2>
        <div class="text-gray-600 mt-1">
          From: <span class="font-medium">{{ email.from_name }} &lt;{{ email.from_email }}&gt;</span>
        </div>
      </div>
      <div class="flex space-x-2">
        <span :class="statusBadgeClass">{{ formattedStatus }}</span>
        <span v-if="email.priority" :class="priorityBadgeClass">{{ email.priority }}</span>
      </div>
    </div>

    <div class="meta-info mb-6 p-4 bg-gray-50 rounded-lg">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <div class="text-sm text-gray-500">Received</div>
          <div>{{ formatDate(email.received_at) }}</div>
        </div>
        <div>
          <div class="text-sm text-gray-500">Assigned To</div>
          <div>{{ email.assignee ? email.assignee.name : 'Unassigned' }}</div>
        </div>
        <div>
          <div class="text-sm text-gray-500">Tags</div>
          <div class="flex flex-wrap gap-1 mt-1">
            <span v-for="tag in email.tags" :key="tag.id" 
                  class="px-2 py-1 text-xs rounded-full" 
                  :style="{ backgroundColor: tag.color, color: getContrastColor(tag.color) }">
              {{ tag.name }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="email-body mb-6 p-4 border rounded-lg">
      <div class="prose max-w-none" v-html="formattedBody"></div>
    </div>

    <div v-if="email.notes" class="notes mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400">
      <h3 class="font-medium text-yellow-800 mb-2">Notes</h3>
      <p class="text-yellow-700">{{ email.notes }}</p>
    </div>

    <div v-if="email.status === 'in_progress'" class="reply-section">
      <h3 class="font-medium mb-3">Reply</h3>
      <textarea v-model="replyContent" rows="6" 
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
      <div class="mt-3 flex justify-end space-x-3">
        <button @click="cancelReply" 
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
          Cancel
        </button>
        <button @click="sendReply" 
                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
          Send Reply
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    email: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      replyContent: ''
    }
  },
  computed: {
    formattedStatus() {
      return this.email.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    },
    statusBadgeClass() {
      const classes = {
        'unassigned': 'bg-gray-100 text-gray-800',
        'assigned': 'bg-blue-100 text-blue-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800'
      };
      return `px-3 py-1 rounded-full text-sm font-medium ${classes[this.email.status]}`;
    },
    priorityBadgeClass() {
      const classes = {
        'low': 'bg-green-100 text-green-800',
        'medium': 'bg-blue-100 text-blue-800',
        'high': 'bg-red-100 text-red-800'
      };
      return `px-3 py-1 rounded-full text-sm font-medium ${classes[this.email.priority]}`;
    },
    formattedBody() {
      return this.email.body_preview.replace(/\n/g, '<br>');
    }
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleString();
    },
    getContrastColor(hexColor) {
      const r = parseInt(hexColor.substr(1, 2), 16);
      const g = parseInt(hexColor.substr(3, 2), 16);
      const b = parseInt(hexColor.substr(5, 2), 16);
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
      return luminance > 0.5 ? '#000000' : '#ffffff';
    },
    sendReply() {
      this.$emit('reply', this.replyContent);
      this.replyContent = '';
    },
    cancelReply() {
      this.replyContent = '';
    }
  }
}
</script>
