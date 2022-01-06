<template>
    <div>
        <div class="container-fluid hero-section d-flex align-content-center justify-content-center flex-wrap ml-auto">
            <h2 class="title">Welcome to the eCommerceApp</h2>
        </div>
        <notifications/>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 " v-for="(product,index) in products" @key="index">
                            <div class="card p-3 my-3">
                                <router-link :to="{ path: '/products/'+product.id}">
                                    <img class="img-thumbnail mb-2"
                                         style="width: 100%;height: 25vh !important;object-fit: cover;"
                                         :src="product.image"
                                         :alt="product.name">
                                    <hr>
                                    <h5><span v-html="product.name"></span>
                                        <span class=" text-muted float-right">$ {{ product.price }}</span>
                                    </h5>

                                </router-link>
                                <div class="card-footer">
                                    <button v-if="product.inCart" @click="removeProductFromCart(product.id)"
                                            class="col-md-6 btn btn-sm btn-outline-danger float-left">Remove from cart
                                    </button>
                                    <button @click="addProductToCart(product.id,1)"
                                            class="col-md-4 btn btn-sm btn-primary float-right">Add to cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            products: [],
            isLoggedIn: localStorage.getItem('token') != null,
            cart: localStorage.getItem('cart') != null
        }
    },
    mounted() {
        axios.get('/api/products').then(response => {
            this.products = response.data.data;
            const cart = JSON.parse(localStorage.getItem('cart'));
            this.products.map(product => {
                product['inCart'] = false;
                if (this.isLoggedIn && this.cart)
                    product['inCart'] = !!cart.find(cartItem => {
                        return cartItem.product_id === product.id;
                    })
            })
            console.log(this.products)
        }).catch(error => {
            this.sendErrorMsg(error)
        })

    }
}
</script>
<style scoped>
.small-text {
    font-size: 14px;
}


.hero-section {
    height: 30vh;
    background: var(--primary);
    align-items: center;
    margin-bottom: 20px;
    margin-top: -20px;
}

.title {
    color: #ffffff;
}
</style>
