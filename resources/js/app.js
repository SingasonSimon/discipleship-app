import './bootstrap';
import './theme';
import './notifications';

import Alpine from 'alpinejs';
import { initDashboardCharts } from './charts';

window.Alpine = Alpine;
window.initDashboardCharts = initDashboardCharts;

Alpine.start();
