import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import ApexCharts from 'apexcharts';

// Components
import EmailList from './components/Email/EmailList.vue';
import EmailViewer from './components/Email/EmailViewer.vue';
import ResponseTimeChart from './components/Reports/ResponseTimeChart.vue';
import Dashboard from './pages/Dashboard.vue';
import Settings from './pages/Settings.vue';

// Create router
const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Dashboard },
        { path: '/dashboard', component: Dashboard },
        { path: '/settings', component: Settings },
    ]
});

// Create Vue app
const app = createApp(App);

// Register components
app.component('EmailList', EmailList);
app.component('EmailViewer', EmailViewer);
app.component('ResponseTimeChart', ResponseTimeChart);

// Provide ApexCharts to components
app.provide('apexcharts', ApexCharts);

// Use router
app.use(router);

// Mount app
app.mount('#app');

// Echo setup for real-time updates
window.Echo.private(`user.${window.Laravel.user.id}`)
    .listen('NewEmailAssigned', (data) => {
        // Handle new email assignment notification
        console.log('New email assigned:', data);
    });
