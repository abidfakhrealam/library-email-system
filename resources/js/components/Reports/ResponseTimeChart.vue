<template>
  <div class="response-time-chart bg-white p-4 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-medium">Response Time Trends</h3>
      <select v-model="timeRange" @change="updateChart" 
              class="border-gray-300 rounded-md shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="7">Last 7 Days</option>
        <option value="30">Last 30 Days</option>
        <option value="90">Last 90 Days</option>
      </select>
    </div>
    <div ref="chart" class="h-64"></div>
  </div>
</template>

<script>
import ApexCharts from 'apexcharts';

export default {
  data() {
    return {
      timeRange: '30',
      chart: null
    }
  },
  mounted() {
    this.fetchDataAndRenderChart();
  },
  methods: {
    async fetchDataAndRenderChart() {
      try {
        const response = await axios.get(`/api/reports/response-times?days=${this.timeRange}`);
        this.renderChart(response.data);
      } catch (error) {
        console.error('Error fetching response time data:', error);
      }
    },
    renderChart(data) {
      const options = {
        chart: {
          type: 'line',
          height: '100%',
          toolbar: {
            show: false
          },
          animations: {
            enabled: true
          }
        },
        series: [{
          name: 'Average Response Time',
          data: data.map(item => item.avg_response_time)
        }],
        xaxis: {
          categories: data.map(item => item.date),
          labels: {
            formatter: function(value) {
              return new Date(value).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }
          }
        },
        yaxis: {
          title: {
            text: 'Hours'
          },
          min: 0,
          labels: {
            formatter: function(value) {
              return value.toFixed(1);
            }
          }
        },
        stroke: {
          width: 2,
          curve: 'smooth'
        },
        colors: ['#3b82f6'],
        markers: {
          size: 4
        },
        tooltip: {
          y: {
            formatter: function(value) {
              return value.toFixed(1) + ' hours';
            }
          }
        },
        grid: {
          borderColor: '#e5e7eb'
        }
      };

      if (this.chart) {
        this.chart.updateOptions(options);
      } else {
        this.chart = new ApexCharts(this.$refs.chart, options);
        this.chart.render();
      }
    },
    updateChart() {
      this.fetchDataAndRenderChart();
    }
  }
}
</script>
