// resources/js/app.js

import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faHome,
  faUser,
  faTableCellsLarge,
  faPlus,
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// — Adicione aqui todos os ícones que vai usar no seu projeto
library.add(
  faHome,
  faUser,
  faTableCellsLarge,
  faPlus,
  // se precisar de outros ícones, importe e adicione aqui:
  // faBell, faLock, etc.
);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
  title: (title) => `${title} – ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .component('font-awesome-icon', FontAwesomeIcon)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
