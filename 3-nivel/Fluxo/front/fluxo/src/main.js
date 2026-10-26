import { createApp } from 'vue'
import App from './App.vue'
import router from './router/Router.js'
import './asset/theme.css'

createApp(App).use(router).mount('#app')