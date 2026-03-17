// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import Greenhouses from '../views/Greenhouses.vue';
import GreenhouseForm from '../views/GreenhouseForm.vue';
import Bays from '../views/Bays.vue';
import BayForm from '../views/BayForm.vue';
import JobTypes from '../views/JobTypes.vue';
import JobTypeForm from '../views/JobTypeForm.vue';
import JobFields from '../views/JobFields.vue';
import JobFieldForm from '../views/JobFieldForm.vue';
import Dropdowns from '../views/Dropdowns.vue';
import DropdownForm from '../views/DropdownForm.vue';
import DropdownValues from '../views/DropdownValues.vue';
import Reports from '../views/Reports.vue';
import ChemicalHistory from '../views/ChemicalHistory.vue';
import SoilTrends from '../views/SoilTrends.vue';
import PestIncidents from '../views/PestIncidents.vue';
import JobEntries from '../views/JobEntries.vue';
import JobEntryView from '../views/JobEntryView.vue';
import Users from '../views/Users.vue';

const routes = [
   { path: '/login', name: 'login', component: Login, meta: { public: true } },
  { path: '/', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  
  // Greenhouses
  { path: '/greenhouses', name: 'greenhouses', component: Greenhouses, meta: { requiresAuth: true } },
  { path: '/greenhouses/create', name: 'greenhouses.create', component: GreenhouseForm, meta: { requiresAuth: true } },
  { path: '/greenhouses/:id/edit', name: 'greenhouses.edit', component: GreenhouseForm, meta: { requiresAuth: true } },
  
  // Bays
  { path: '/bays', name: 'bays', component: Bays, meta: { requiresAuth: true } },
  { path: '/bays/create', name: 'bays.create', component: BayForm, meta: { requiresAuth: true } },
  { path: '/bays/:id/edit', name: 'bays.edit', component: BayForm, meta: { requiresAuth: true } },
  
  // Job Types
  { path: '/job-types', name: 'job-types', component: JobTypes, meta: { requiresAuth: true } },
  { path: '/job-types/create', name: 'job-types.create', component: JobTypeForm, meta: { requiresAuth: true } },
  { path: '/job-types/:id/edit', name: 'job-types.edit', component: JobTypeForm, meta: { requiresAuth: true } },
  
  // Job Fields
  { path: '/job-types/:jobTypeId/fields', name: 'job-fields', component: JobFields, meta: { requiresAuth: true } },
  { path: '/job-types/:jobTypeId/fields/create', name: 'job-fields.create', component: JobFieldForm, meta: { requiresAuth: true } },
  { path: '/job-types/:jobTypeId/fields/:id/edit', name: 'job-fields.edit', component: JobFieldForm, meta: { requiresAuth: true } },
  
  // Dropdowns
  { path: '/dropdowns', name: 'dropdowns', component: Dropdowns, meta: { requiresAuth: true } },
  { path: '/dropdowns/create', name: 'dropdowns.create', component: DropdownForm, meta: { requiresAuth: true } },
  { path: '/dropdowns/:id/edit', name: 'dropdowns.edit', component: DropdownForm, meta: { requiresAuth: true } },
  { path: '/dropdowns/:id/values', name: 'dropdowns.values', component: DropdownValues, meta: { requiresAuth: true } },
  
  // Reports
  { path: '/reports', name: 'reports', component: Reports, meta: { requiresAuth: true } },
  { path: '/reports/chemicals', name: 'reports.chemicals', component: ChemicalHistory, meta: { requiresAuth: true } },
  { path: '/reports/soil-trends', name: 'reports.soil-trends', component: SoilTrends, meta: { requiresAuth: true } },
  { path: '/reports/pest-incidents', name: 'reports.pest-incidents', component: PestIncidents, meta: { requiresAuth: true } },
  
  // Entries
  { path: '/entries', name: 'entries', component: JobEntries, meta: { requiresAuth: true } },
  { path: '/entries/:id', name: 'entries.view', component: JobEntryView, meta: { requiresAuth: true } },
  { path: '/users', component: Users, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const isAuthenticated = !!token;
  
  // If trying to access login while authenticated, redirect to home
  if (to.path === '/login' && isAuthenticated) {
    next('/');
    return;
  }
  
  // If trying to access protected route without auth, redirect to login
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
    return;
  }
  
  // Otherwise allow
  next();
});

export default router;