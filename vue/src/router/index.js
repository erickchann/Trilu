import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
	{
		path: '/',
		component: () => import('@/views/Login.vue')
	},
	{
		path: '/home',
		component: () => import('@/views/Home.vue')
	},
	{
		path: '/board/:id',
		component: () => import('@/views/Board.vue')
	}
];

const router = new VueRouter({
	mode: 'history',
	routes
});

export default router