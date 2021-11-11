import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import LoginGeneral from '../views/LoginGeneral.vue'
import NuevoExpediente from '../views/NuevoExpediente.vue'
import BandejaDeEntrada from '../views/BandejaDeEntrada.vue'
import MisExpedientes from '../views/MisExpedientes.vue'
import RecuperarExpediente from '../views/RecuperarExpediente.vue'
import NuevaReunion from '../views/NuevaReunion.vue'
import NuevoPase from '../views/NuevoPase.vue'
import Seguimientos from '../views/Seguimientos.vue'
import VerSeguimientos from '../views/VerSeguimientos.vue'
import NuevoIniciador from '../views/NuevoIniciador.vue'
import layout from '../layout/Layout'
import Expedientes from "../views/Expedientes";
import Enviados from "../views/Enviados";
import Usuario from "../views/Usuario";

import store from "@/store/index";
import guest from "../middleware/guest";
import auth from "../middleware/auth";
import middlewarePipeline from './middlewarePipeline'

Vue.use(VueRouter)

const routes = [

  {
    path: '',
    name: 'layout',
    component: layout,
    children: [{
        path: '/',
        name: 'Home',
        component: Home,
        meta: {title: 'Inicio',  layout: layout , middleware: [auth]}
      },
      {
        path: '/nuevo-expediente',
        name: 'Nuevo',
        component: NuevoExpediente,
        meta: { title: 'Nuevo Expediente' , middleware: [auth]}
      },
      {
        path: '/expedientes',
        name: 'Expedientes',
        component: Expedientes,
        meta: { title: 'Expedientes' , middleware: [auth] }
      },
      {
        path: '/expedientes-pendientes',
        name: 'Expedientes Pendientes',
        component: BandejaDeEntrada,
        meta: { title: 'Pendientes' ,middleware: [auth]}
      },
      {
        path: '/mis-expedientes',
        name: 'MisExpedientes',
        component: MisExpedientes,
        meta: { title: 'Mis Expedientes', middleware: [auth] }
      },
      {
        path: '/recuperar-expediente',
        name: 'recuperar-exp',
        component: RecuperarExpediente,
        meta: { title: 'Recuperar' , middleware: [auth]}
      },
      {
        path: '/nueva-reunion',
        name: 'Nueva reunion',
        component: NuevaReunion,
        meta: { title: 'Nueva Reunion' , middleware: [auth]}
      },
      {
        path: '/nuevo-pase',
        name: 'NuevoPase',
        component: NuevoPase,
        meta: { title: 'Nuevo Pase', middleware: [auth] }
      },
      {
        path: '/historial',
        name: 'Historial',
        component: Seguimientos,
        meta: { title: 'Historial', middleware: [auth] }
      },
      {
        path: '/ver-historiales',
        name: 'VerHistoriales',
        component: VerSeguimientos,
        meta: { title: 'Ver Historiales' , middleware: [auth]}
      },
      {
        path: '/nuevo-iniciador',
        name: 'NuevoIniciador',
        component: NuevoIniciador,
        meta: { title: 'Nuevo Iniciador', middleware: [auth] }
      },
      {
        path: '/expedientes',
        name: 'Expedientes',
        component: Expedientes,
        meta: { title: 'Nueva Reunion', middleware: [auth] }
      },
      {
        path: '/enviados',
        name: 'Enviados',
        component: Enviados,
        meta: { title: 'Enviados' , middleware: [auth] }
      },
      {
        path: '/usuario',
        name: 'Usuario',
        component: Usuario,
        meta: { title: 'Usuario' ,middleware: [auth]}
      },
    ]
  },
  {
    path: '/login',
    name: 'LoginGeneral',
    component: LoginGeneral,
    meta: { title: 'Ingresar' ,  middleware: [guest] }
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach((to, from, next) => {
  document.title = to.meta.title;
  next();
});

router.beforeEach((to, from, next) => {
  if (!to.meta.middleware) {
    return next()
  }
  const middleware = to.meta.middleware

  const context = {
    to,
    from,
    next,
    store
  }


  return middleware[0]({
    ...context,
    next: middlewarePipeline(context, middleware, 1)
  })

})

export default router
