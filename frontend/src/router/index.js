import { createRouter, createWebHistory } from 'vue-router'
import { useStore } from 'vuex';


const routes = [
  {
    path: '/:pathMatch(.*)*',
    name: "404",
    component: () => import('../views/404.vue'),
    meta: {
      hideNavBar: true
    }
    // redirect: (to) => {
    //   // const userRole = useStore().getters.userRole

    //   // Avoid infinite redirect loop
    //   if (to.path === '/malgo-frontend/' || to.path === '/malgo-frontend/facturas') {
    //     return to.path
    //   }

    //   return "userRole" ? '/malgo-frontend/facturas' : '/malgo-frontend/'
    // }
  },{
    path: '/malgo-frontend/',
    name: 'login',
    component: () => import('../views/login.vue'),
    meta: {
      // icon: 'fa-regular fa-rectangle-list ',
      // label: 'Formulario',
      hideNavBar: true
    }
  },
  {
    path: '/malgo-frontend/form/:propertysku',
    name: 'formulario',
    component: () => import('../views/application_form.vue'),
    meta: {
      // icon: 'fa-regular fa-rectangle-list ',
      // label: 'Formulario',
      hideNavBar: true
    }
  },
  {
    path: '/malgo-frontend/aplicaciones',
    name: 'aplicaciones',
    component: () => import('../views/applications.vue'),
    meta: {
      icon: 'fa-regular fa-folder-open',
      label: 'Aplicaciones',
      roles: ['admin']
    }
  },
  {
    path: '/malgo-frontend/propiedades',
    name: 'properties',
    component: () => import('../views/properties.vue'),
    meta: {
      icon: 'fa-solid fa-building',
      label: 'Propiedades',
      roles: ['admin']
    }
  },
  {
    path: '/malgo-frontend/facturas',
    name: 'facturas',
    component: () => import('../views/bills.vue'),
    meta: {
      icon: 'fa-solid fa-file-invoice-dollar',
      label: 'Facturas',
      roles: ['admin', "tenant"]
    }
  },
  {
    path: '/malgo-frontend/contratos',
    name: 'contratos',
    component: () => import('../views/contracts.vue'),
    meta: {
      icon: 'fa-solid fa-file-signature',
      label: 'Contratos',
      roles: ['admin']
    }
  }, {
    path: '/malgo-frontend/pagos',
    name: 'pagos',
    component: () => import('../views/payments.vue'),
    meta: {
      icon: 'fa-solid fa-money-bill-wave',
      label: 'Pagos',
      roles: ['admin', "tenant"]
    }
  },
  {
    path: '/malgo-frontend/servicios',
    name: 'servicios',
    component: () => import('../views/servicios.vue'),
    meta: {
      icon: 'fa-solid fa-hand-holding-droplet',
      label: 'Servicios',
      roles: ['admin']
    }
  },
  {
    path: '/malgo-frontend/perfil',
    name: 'perfil',
    component: () => import('../views/profile.vue'),
    meta: {
      icon: 'fa-solid fa-user',
      label: 'Perfil',
      roles: ['admin', "tenant"]
    }
  },
]


const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const userRole = useStore().getters.userRole
  const roles = to.meta.roles

  if (to.path === '/malgo-frontend/' && userRole) {
    return next('/malgo-frontend/facturas')
  }

  if (roles && !roles.includes(userRole)) {
    return next('/malgo-frontend/') // or some fallback
  }

  next()
})


export default router


