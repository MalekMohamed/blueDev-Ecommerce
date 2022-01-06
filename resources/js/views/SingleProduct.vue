<template>
    <div class="container">
        <div class="row">
            <notifications/>
            <div class="col-md-12">
                <div class="card p-3">
                    <img class="img-thumbnail mb-2" style="width: 100%;height: 25vh !important;object-fit: cover;"
                         :src="product.image" :alt="product.name">
                    <div class="card-header">
                        <span class="badge badge-info float-left">$ {{ product.price }}</span>
                        <span class="small-text float-right">Available Quantity: {{ product.qty }}</span>
                    </div>
                    <div class="card-body">
                    <h3 class="title" v-html="product.name"></h3>
                    <h5 class="float-right p-4" v-html="product.sku"></h5>
                    <p class="text-muted bg-light p-4">{{ product.description }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <label class="col-md-1 align-self-center">Quantity: </label>
                            <input type="number"
                                   name="units" min="1"
                                   :max="product.qty"
                                   class="col-md-1 form-control "
                                   v-model="quantity"
                                   @change="checkQty">
                            <h5 class="small-text text-danger col-md-4">Total : $
                                {{ product.price * quantity }}</h5>
                            <div v-if="isLoggedIn" class="col-md-6 d-flex justify-content-end">
                                <button @click="addToCart(product.id,quantity)"
                                        class="btn btn-primary mr-1">Add to
                                    cart
                                </button>
                                <button v-if="product.inCart" @click="removeFromCart(product.id)"
                                        class="btn btn-danger">Remove from
                                    cart
                                </button>
                            </div>
                            <div class="col-md-6" v-if="!isLoggedIn">
                                <div class="alert alert-danger">
                                    <h2>You need to login to continue</h2>
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-primary float-left" @click="login">Login</button>
                                        <button class=" btn btn-danger float-right" @click="register">Create an
                                            account
                                        </button>
                                    </div>
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
            product: [],
            quantity: 1,
            isLoggedIn: localStorage.getItem('token') != null,
            cart: localStorage.getItem('cart') != null,
        }
    },
    beforeMount() {
        axios.get(`/api/products/${this.$route.params.id}`)
            .then(response => {
                this.product = response.data.data
                const cart = JSON.parse(localStorage.getItem('cart'));
                this.product['inCart'] = false;
                if (this.isLoggedIn && this.cart)
                    this.product['inCart'] = !!cart.find(cartItem => {
                        return cartItem.product_id === this.product.id;
                    })
            })
            .catch(error => {
                console.error(error);
            })
    },
    methods: {
        login() {
            this.$router.push({name: 'login', params: {nextUrl: this.$route.fullPath}})
        },
        register() {
            this.$router.push({name: 'register', params: {nextUrl: this.$route.fullPath}})
        },
        addToCart(product, quantity) {
            this.addProductToCart(product, quantity).then(response => {
                this.product.inCart = true;
                this.$emit('cartUpdate')
                this.$forceUpdate()
                this.$notify({
                    type: 'success',
                    title: 'Cart Updated',
                    text: response.data.message
                })
            }).catch(error => {
                this.sendErrorMsg(error)
            });
        },
        removeFromCart(product) {
            this.removeProductFromCart(product).then(response => {
                this.product.inCart = false;
                this.$forceUpdate()
                this.$emit('cartUpdate')
                this.$notify({
                    type: 'success',
                    title: 'Cart Updated',
                    text: response.data.message
                })
            }).catch(error => {
                this.sendErrorMsg(error)
            });
        },
        checkQty(e) {
            if (this.quantity > this.product.qty) {
                this.quantity = this.product.qty
                this.$notify({
                    type: 'error',
                    title: 'Cart Error',
                    text: 'Product is Out of Stock'
                })
            }
        }
    }
}
</script>
<style scoped>
.small-text {
    font-size: 18px;
}

.title {
    font-size: 36px;
}
</style>
