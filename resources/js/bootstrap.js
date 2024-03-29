window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Notifications
 */
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

window.Notyf = new Notyf();

/**
 * AlpineJS - A rugged, minimal framework for composing JavaScript behavior in your markup.
 */
import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start()
