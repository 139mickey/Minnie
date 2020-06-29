import Vue from 'vue'
import Router from 'vue-router'
import HelloWorld from '@/components/HelloWorld'

Vue.use(Router)

import Dashboard from '../components/Dashboard.vue';
import MoviesList from '../components/MoviesList.vue';

export default new Router({
  mode: 'history',
  routes: [{
      path: '/',
      name: '首页',
      meta: {
        title: "首页"
      },

    },
    {
      path: '/Dashboard',
      name: 'Dashboard',
      meta: {
        title: "仪表盘"
      },
      component: Dashboard
    },
    {
      path: '/movies',
      name: 'MoviesList',
      meta: {
        title: "影片列表"
      },
      component: MoviesList
    },
  ]
})
