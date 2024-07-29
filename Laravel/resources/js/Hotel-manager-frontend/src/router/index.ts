import HomeView from '@/views/HomeView.vue'
import path from 'path'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: HomeView
    },
    {
      path: '/hotel',
      name: 'Hotel',
      component: () => import('@/components/Hotel/Index.vue'),
    },
    {
      path: '/hotel/create',
      name: 'HotelCreate',
      component: () => import('@/components/Hotel/CreateView.vue')
    },
    {
      path: '/hotel/:id/edit',
      name: 'HotelEdit',
      component: () => import('@/components/Hotel/EditView.vue'),
      props: true,
    },
    {
      path: '/room',
      name: 'Room',
      component: HomeView
    },
    {
      path: '/guest',
      name: 'Guest',
      component: HomeView
    },
    {
      path: '/service',
      name: 'Service',
      component: HomeView
    },
    {
      path: '/category',
      name: 'Category',
      component: HomeView
    },
    {
      path: '/type',
      name: 'Type',
      component: HomeView
    },
  ]
})

export default router
