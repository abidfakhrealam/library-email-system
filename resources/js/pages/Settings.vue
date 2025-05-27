<template>
  <div class="settings">
    <h2 class="text-2xl font-bold mb-6">Account Settings</h2>
    
    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <h3 class="text-lg font-medium mb-4">Profile Information</h3>
      <form @submit.prevent="updateProfile">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input v-model="form.name" type="text" required
                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input v-model="form.email" type="email" required
                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
          </div>
        </div>
        <button type="submit" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
          Save Profile
        </button>
      </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <h3 class="text-lg font-medium mb-4">Change Password</h3>
      <form @submit.prevent="updatePassword">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input v-model="passwordForm.current_password" type="password" required
                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input v-model="passwordForm.new_password" type="password" required
                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
          </div>
        </div>
        <button type="submit" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
          Change Password
        </button>
      </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium mb-4">Notification Preferences</h3>
      <div class="space-y-3">
        <div class="flex items-center">
          <input v-model="notifications.email" type="checkbox" id="email-notifications"
                 class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
          <label for="email-notifications" class="ml-2 block text-sm text-gray-700">
            Email Notifications
          </label>
        </div>
        <div class="flex items-center">
          <input v-model="notifications.slack" type="checkbox" id="slack-notifications"
                 class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
          <label for="slack-notifications" class="ml-2 block text-sm text-gray-700">
            Slack Notifications
          </label>
        </div>
        <div class="flex items-center">
          <input v-model="notifications.sms" type="checkbox" id="sms-notifications"
                 class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
          <label for="sms-notifications" class="ml-2 block text-sm text-gray-700">
            SMS Notifications
          </label>
        </div>
        <button @click="saveNotifications" 
                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
          Save Preferences
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        name: this.$page.props.user.name,
        email: this.$page.props.user.email
      },
      passwordForm: {
        current_password: '',
        new_password: ''
      },
      notifications: {
        email: this.$page.props.user.email_notifications,
        slack: this.$page.props.user.slack_notifications,
        sms: this.$page.props.user.sms_notifications
      }
    }
  },
  methods: {
    async updateProfile() {
      try {
        await axios.put('/settings/profile', this.form);
        this.$toast.success('Profile updated successfully');
      } catch (error) {
        this.$toast.error('Failed to update profile');
      }
    },
    async updatePassword() {
      try {
        await axios.put('/settings/password', this.passwordForm);
        this.$toast.success('Password changed successfully');
        this.passwordForm = {
          current_password: '',
          new_password: ''
        };
      } catch (error) {
        this.$toast.error(error.response?.data?.message || 'Failed to change password');
      }
    },
    async saveNotifications() {
      try {
        await axios.put('/settings/notifications', this.notifications);
        this.$toast.success('Notification preferences saved');
      } catch (error) {
        this.$toast.error('Failed to save preferences');
      }
    }
  }
}
</script>
