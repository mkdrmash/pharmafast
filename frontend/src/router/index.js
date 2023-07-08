import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '@/views/LoginView.vue'
import HomeView from '@/views/HomeView.vue'
import FindMedicineView from '@/views/FindMedicineView.vue'
import OrderView from '@/views/OrderView.vue'
import StandbyView from '@/views/StandbyView.vue'
import PharmacyView from '@/views/PharmacyView.vue'
import UserView from '@/views/UserView.vue'
import axios from 'axios'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView
    },
    {
      path: '/home',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/user',
      name: 'user',
      component: UserView
    },
    {
      path: '/find-medicine',
      name: 'find-medicine',
      component: FindMedicineView
    },
    {
      path: '/order',
      name: 'order',
      component: OrderView
    },
    {
      path: '/standby',
      name: 'standby',
      component: StandbyView
    },
    {
      path: '/pharmacy',
      name: 'pharmacy',
      component: PharmacyView
    },
  ]
})

router.beforeEach((to, from) => {
  if (to.name === 'login') {
    return true
  }

  if (!localStorage.getItem('token')) {
    return {
      name: 'login'
    }
  }

  checkTokenAuthenticity()
})

const checkTokenAuthenticity = () => {
  axios.get('http://127.0.0.1:8000/api/user', {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  })
    .then((response) => {})
    .catch((error) => {
      localStorage.removeItem('token')
      router.push({
        name: 'login'
      })
    })
}

export default router
