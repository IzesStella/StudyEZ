//
// resources/js/bootstrap.js
//

// Lodash
import _ from 'lodash'
window._ = _

// Axios
import axios from 'axios'
window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Inertia Progress (barra de progresso)
import { InertiaProgress } from '@inertiajs/progress'
InertiaProgress.init({
  delay: 250,
  color: '#29d',
  includeCSS: true,
})

// Adicione a configuração do Laravel Echo aqui
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    // Adicione esta linha para garantir que o cluster seja sempre passado
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
});
