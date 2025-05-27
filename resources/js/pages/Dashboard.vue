<template>
  <div class="dashboard">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium mb-2">Total Emails Today</h3>
        <p class="text-3xl font-bold text-blue-600">{{ stats.total_emails || 0 }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium mb-2">Unassigned Emails</h3>
        <p class="text-3xl font-bold text-yellow-600">{{ stats.unassigned_emails || 0 }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium mb-2">Avg Response Time</h3>
        <p class="text-3xl font-bold text-green-600">
          {{ stats.avg_response_time ? stats.avg_response_time.toFixed(1) + 'h' : 'N/A' }}
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white p-6 rounded-lg shadow">
        <ResponseTimeChart />
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium mb-4">Recent Emails</h3>
        <EmailList :emails="recentEmails" @select="selectEmail" />
      </div>
    </div>

    <EmailViewer v-if="selectedEmail" :email="selectedEmail" @reply="handleReply" 
                 class="mt-6" @close="selectedEmail = null" />
  </div>
</template>

<script>
import EmailList from '@/Components/Email/EmailList.vue';
import EmailViewer from '@/Components/Email/EmailViewer.vue';
import ResponseTimeChart from '@/Components/Reports/ResponseTimeChart.vue';

export default {
  components: {
    EmailList,
    EmailViewer,
    ResponseTimeChart
  },
  data() {
    return {
      stats: {},
      recentEmails: [],
      selectedEmail: null
    }
  },
  mounted() {
    this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      try {
        const response = await axios.get('/api/dashboard');
        this.stats = response.data.stats;
        this.recentEmails = response.data.recent_emails;
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
      }
    },
    selectEmail(email) {
      this.selectedEmail = email;
    },
    async handleReply(content) {
      try {
        await axios.post(`/api/emails/${this.selectedEmail.id}/reply`, { content });
        this.$toast.success('Reply sent successfully');
        this.fetchDashboardData();
        this.selectedEmail = null;
      } catch (error) {
        this.$toast.error('Failed to send reply');
        console.error('Error sending reply:', error);
      }
    }
  }
}
</script>
