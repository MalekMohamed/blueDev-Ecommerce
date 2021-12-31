<template>
    <div class="container">
        <div class="row">
            <notifications/>
            <div class="col-md-8 offset-md-2">
                <div class="order-box">
                    <img class="img-thumbnail mb-2" style="width: 100%;height: 25vh !important;object-fit:cover;"
                         :src="product.image" :alt="product.name">
                    <h3 class="title" v-html="product.name"></h3>
                    <h5 class="float-right" v-html="product.sku"></h5>
                    <p class="text-muted">{{ product.description }}</p>
                    <p class="small-text text-muted float-left">$ {{ product.price }}</p>
                    <p class="small-text text-muted float-right">Available Units: {{ product.qty }}</p>

                    <br>
                    <hr>
                    <div class="row">
                        <h5 class="small-text text-danger float-left col-md-2">Total : $
                            {{ product.price * quantity }}</h5>
                    </div>
                    <div class="row">
                        <label class="col-md-2 float-left">Quantity: </label>
                        <input type="number"
                               name="units" min="1"
                               :max="product.qty"
                               class="col-md-6 form-control float-left"
                               v-model="quantity"
                               @change="checkQty">
                        <div v-if="isLoggedIn" class="col-md-4 ">
                            <button  @click="addToCart(product.id,quantity)"
                                    class="btn btn-primary float-right">Add to
                                cart
                            </button>
                            <button hidden v-if="isInCart" @click="removeFromCart(product.id)"
                                    class="btn btn-danger float-right">Remove from
                                cart
                            </button>
                        </div>

                    </div>
                    <div class="card-body m-5" v-if="!isLoggedIn">
                        <h2>You need to login to continue</h2>
                        <button class="col-md-4 btn btn-primary float-left" @click="login">Login</button>
                        <button class="col-md-4 btn btn-danger float-right" @click="register">Create an account</button>
                    </div>

                </div>
                <br>
                <div>


                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "../axios";

export default {
    data() {
        return {
            pid: this.$route.params.pid ? this.$route.params.pid : null,
            address: "",
            quantity: 1,
            isLoggedIn: null,
            product: [],
            isInCart: false
        }
    },
    mounted() {
        this.isLoggedIn = localStorage.getItem('token') != null
    },
    beforeMount() {
        axios.get(`/api/products/${this.pid}`)
            .then(response => {
                this.product = response.data.data
            })
            .catch(error => {
                console.error(error);
            })
        if (localStorage.getItem('token') != null) {
            this.user = JSON.parse(localStorage.getItem('user'))
        }
    },
    methods: {
        login() {
            this.$router.push({name: 'login', params: {nextUrl: this.$route.fullPath}})
        },
        register() {
            this.$router.push({name: 'register', params: {nextUrl: this.$route.fullPath}})
        },
        addToCart(product, quantity) {
            axios.post('/api/cart', {'product_id': product, 'qty': quantity}).then(response => {
                this.isInCart = true;
                this.$emit('cartUpdate')
                this.$notify({
                    type: 'success',
                    title: 'Cart Updated',
                    text: response.data.message
                })
            }).catch(error => {
                this.$notify({
                    type: 'error',
                    title: 'Cart Error',
                    text: error.response.data.message
                })
            });
        },
        removeFromCart(product) {
            axios.delete('/api/cart/' + product).then(response => {
                this.isInCart = false;
                this.$emit('cartUpdate')
                this.$notify({
                    type: 'success',
                    title: 'Cart Updated',
                    text: response.data.message
                })
            }).catch(error => {
                this.$notify({
                    type: 'error',
                    title: 'Cart Error',
                    text: error.response.data.message
                })
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

.order-box {
    border: 1px solid #cccccc;
    padding: 10px 15px;
}

.title {
    font-size: 36px;
}
</style>
