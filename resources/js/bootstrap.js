import _ from 'lodash';
window._ = _;

// jQuery — expose globally so inline Blade <script> blocks can use $
//import jQuery from 'jquery';
//window.$ = window.jQuery = jQuery;

// Bootstrap 5 bundles Popper internally, so no separate popper.js import needed
import 'bootstrap';

// Axios
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';