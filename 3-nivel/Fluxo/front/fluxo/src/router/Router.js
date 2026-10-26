import { createRouter, createWebHistory } from 'vue-router'

//user
import Login from '../login/Login.vue'
import CriarUsuario from '../login/CreateUser.vue'
import AtualizarSenha from '../login/UpdatePassword.vue'

//publico
import principal from '../principal/pagePrincipal.vue'
import publics from '../principal/public.vue'

//ganho
import indexGanho from '../income/index.vue'
import listGanho from '../income/list.vue'
import createGanho from '../income/insert.vue'
import updateGanho from '../income/update.vue'

//transacao
import indexTransacao from '../transaction/index.vue'
import listTransacao from '../transaction/list.vue'
import createTransacao from '../transaction/insert.vue'
import updateTransacao from '../transaction/update.vue'

const routes = [
  //publico
  { path: '/', component: publics },
  { path: '/pagina-principal', component: principal },

  //users
  { path: '/login', component: Login },
  { path: '/cadastro', component: CriarUsuario },
  { path: '/atualizar-senha', component: AtualizarSenha },

  //ganhos
  { path: '/income', component: indexGanho },
  { path: '/income/list', component: listGanho },
  { path: '/income/create', component: createGanho },
  { path: '/income/update/:id', component: updateGanho },

  //transacoes
  { path: '/transaction', component: indexTransacao },
  { path: '/transaction/list', component: listTransacao },
  { path: '/transaction/create', component: createTransacao },
  { path: '/transaction/update/:id', component: updateTransacao },

  //endereço que não existe volta para o início
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router