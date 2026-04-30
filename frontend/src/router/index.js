import { createRouter, createWebHistory } from 'vue-router'

import CreatePoll from '../views/CreatePoll.vue'
import PollPage from '../views/PollPage.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/create' },
    { path: '/create', component: CreatePoll },
    { path: '/poll/:code', component: PollPage },
  ],
})

export default router

