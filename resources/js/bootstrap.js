import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Lodash (required for SASS)
 */
import _ from 'lodash';
window._ = _;

/**
 * Notifications
 */
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

window.Notyf = new Notyf();

/**
 * AlpineJS
 */
import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start()
