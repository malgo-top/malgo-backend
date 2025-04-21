import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css'
import router from './router'
import store from './store';
import { createAdminOnlyDirective } from './directives/adminOnly'


createApp(App).directive('admin-only', createAdminOnlyDirective(store)).use(router).use(store).mount('#app')
