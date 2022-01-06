import axios from './axios'
import Vue from 'vue'
import VueRouter from 'vue-router'
import Notifications from 'vue-notification'
window.axios = axios

Vue.use(VueRouter)
Vue.use(Notifications)
Vue.mixin({
    methods: {
        sendErrorMsg: error => {
            if (typeof error.response.data.message === 'object') {
                Object.keys(error.response.data.message).forEach(fieldError => {
                    Vue.notify({
                        type: 'error',
                        title: fieldError,
                        text: error.response.data.message[fieldError]
                    })
                })
            } else {
                Vue.notify({
                    type: 'error',
                    title: 'Error',
                    text: error.response.data.message
                })
            }
        },
        getUserCart() {
          return axios.get('/api/cart')
        },
        addProductToCart(product,qty) {
            this.$emit('cartUpdate')
            return axios.post('/api/cart', {'product_id': product, 'qty': qty})
        },
        removeProductFromCart(product) {
            this.$emit('cartUpdate')
            return axios.delete('/api/cart/' + product)
        },
    }
})
import App from './views/App'
import Home from './views/Home'
import Login from './views/Login'
import ResetPassword from './views/ResetPassword'
import ForgetPassword from './views/ForgetPassword'
import Register from './views/Register'
import SingleProduct from './views/SingleProduct'
import Cart from './views/Cart'
import Customer from './views/Customer'
import Admin from './views/Admin'
import Checkout from "./views/Checkout";
const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/forget-password',
            name: 'forget-password',
            component: ForgetPassword
        },
        {
            path: '/password/reset/:token',
            name: 'reset-password',
            component: ResetPassword
        },
        {
            path: '/register',
            name: 'register',
            component: Register
        },
        {
            path: '/products/:id',
            name: 'single-products',
            component: SingleProduct
        },
        {
            path: '/checkout/:pid',
            name: 'checkout',
            component: Checkout,
        },
        {
            path: '/cart',
            name: 'cart',
            component: Cart,
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/dashboard',
            name: 'userboard',
            component: Customer,
            meta: {
                requiresAuth: true,
                is_customer: true
            }
        },
        {
            path: '/admin/:page',
            name: 'admin-pages',
            component: Admin,
            meta: {
                requiresAuth: true,
                is_admin: true
            }
        },
        {
            path: '/admin',
            name: 'admin',
            component: Admin,
            meta: {
                requiresAuth: true,
                is_admin: true
            }
        },
    ],
})
router.beforeEach((to, from, next) => {

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (localStorage.getItem('token') == null) {
            next({
                path: '/login',
                params: {nextUrl: to.fullPath}
            })
        } else {
            let user = JSON.parse(localStorage.getItem('user'))
            if (to.matched.some(record => record.meta.is_admin)) {
                if (user.role === 'Admin') {
                    next()
                } else {
                    next({name: 'userboard'})
                }
            } else if (to.matched.some(record => record.meta.is_customer)) {
                if (user.role === 'Customer') {
                    next()
                } else {
                    next({name: 'admin'})
                }
            }
            next()
        }
    } else {
        next()
    }
})

const app = new Vue({
    el: '#app',
    components: {App},
    router,
});
