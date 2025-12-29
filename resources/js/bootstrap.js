import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Set CSRF header from meta tag for all axios requests
const csrfTokenMeta = document.head.querySelector('meta[name="csrf-token"]');
if (csrfTokenMeta && csrfTokenMeta.content) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfTokenMeta.content;
}
