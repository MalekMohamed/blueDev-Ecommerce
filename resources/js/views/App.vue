<template>
    <div>
        <nav class="navbar navbar-expand-md navbar-dark navbar-laravel">
            <div class="container">
                <router-link :to="{name: 'home'}" class="navbar-brand" title="eCommerceApp">
                    <img alt="eCommerceApp" src="/images/logo.png">
                </router-link>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav w-75 mr-auto">
                        <li class="nav-item w-100">
                            <div class="search-form form-inline">
                                <input @keyup="searchForProduct" type="text" class="form-control w-100 search-input"
                                       placeholder="Search for product">
                            </div>
                            <ul :class="searchResults.length > 0 ? 'is-open' : 'is-closed'" class="Typeahead-menu">
                                <li v-if="searchResults.length > 0" v-for="(product,index) in searchResults"
                                    :key="index" @click="closeSearchResults" class="result-item">
                                    <router-link :to="{ path: '/products/'+product.id}">{{ product.name }}</router-link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <router-link :to="{ name: 'cart' }" class="nav-link" v-if="isLoggedIn && cart.length > 0">
                            Cart <span class="badge badge-danger">{{ cart.length }}</span>
                        </router-link>
                        <router-link :to="{ name: 'login' }" class="nav-link" v-if="!isLoggedIn">Login</router-link>
                        <router-link :to="{ name: 'register' }" class="nav-link" v-if="!isLoggedIn">Register
                        </router-link>
                        <span v-if="isLoggedIn">
                          <router-link :to="{ name: 'userboard' }" class="nav-link" v-if="user_type === 'Customer'"> Hi, {{
                                  name
                              }}</router-link>
                          <router-link :to="{ name: 'admin' }" class="nav-link"
                                       v-if="user_type === 'Admin'"> Hi, {{ name }}</router-link>
                        </span>
                        <li class="nav-link" v-if="isLoggedIn" @click="logout"> Logout</li>
                    </ul>
                </div>
            </div>
        </nav>
        <main @click="closeSearchResults" class="py-4">
            <router-view :key="$route.fullPath" @cartUpdate="getCart" @profileUpdate="change" @loggedIn="change"></router-view>
        </main>
        <footer class="footer">
            <div class="container">
                <div class="footer__inner">
                    <div class="footer__item text-center">
                        <p class="hidden-sm-down d-none d-lg-block">Made By <a target="_blank"
                                                                               href="https://www.linkedin.com/in/malek-mohamed/">Malek
                            Mohamed.</a></p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
<script>
export default {
    data() {
        return {
            cart: [],
            searchResults: [],
            name: null,
            user_type: 0,
            isLoggedIn: localStorage.getItem('token') != null
        }
    },
    mounted() {
        this.setDefaults()
        this.getCart()
    },
    methods: {
        getCart(e) {
            console.log(e)
            if (this.isLoggedIn) {
                axios.get('/api/cart').then(response => {
                    this.cart = response.data.data
                    let mappedCart = [];
                    let cart = this.cart.map(item => {
                        mappedCart[item.product_id] = item.qty;
                    })
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                }).catch(error => {

                })
            }
        },
        setDefaults() {
            if (this.isLoggedIn) {
                let user = JSON.parse(localStorage.getItem('user'))
                this.name = user.name
                this.user_type = user.role
            }
        },
        change() {
            this.isLoggedIn = localStorage.getItem('token') != null
            this.setDefaults()
        },
        logout() {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            localStorage.removeItem('cart')
            this.change()
            this.$router.push('/')
        },
        searchForProduct(e) {
            this.searchResults = [];
            if (e.target.value !== '') {
                axios.get('/api/search/' + e.target.value).then(response => {
                    this.searchResults = response.data.data;
                }).catch(error => {

                })
            }
        },
        closeSearchResults() {
            this.searchResults = [];
        }
    }
}
</script>
