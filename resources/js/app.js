import './bootstrap';
import Chart from 'chart.js/auto';

// Global Chart.js configuration
Chart.defaults.responsive = true;
Chart.defaults.maintainAspectRatio = false;

// Export Chart for global use
window.Chart = Chart;
