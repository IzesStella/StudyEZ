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
