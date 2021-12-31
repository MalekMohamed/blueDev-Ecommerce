<template>
    <div>

        <div class="container">
            <div class="card card-body">
            <div class="row">
                <notifications/>
                <div class="col-md-8 offset-md-2">
                    <table v-if="cart.length > 0" class="table">
                        <thead>
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(cartItem,index) in cart" @key="index" v-if="cartItem.product">
                            <td>
                                <div class="d-flex">
                                    <img class="img-thumbnail mr-2"
                                         style="width: 25%;max-height: 5vh !important;object-fit: cover;"
                                         :src="cartItem.product.image" :alt="cartItem.product.name">
                                    <span class="align-self-center" v-html="cartItem.product.name"></span>
                                </div>
                            </td>
                            <td>
                                <span class="align-self-center">{{ cartItem.product.price }}</span>
                            </td>
                            <td>
                                <span class="align-self-center"> {{ cartItem.qty }}</span>
                            </td>
                            <td>
                                <span class="align-self-center"> {{ cartItem.product.price * cartItem.qty }}</span>
                            </td>
                            <td><button @click="removeFromCart(cartItem.product_id);" class="btn btn-danger">Remove</button></td>
                        </tr>

                        </tbody>
                    </table>
                    <div v-if="cart.length === 0">
                        <div class="text-center">
                            Cart Empty
                            <router-link :to="{name: 'home'}" class="text-primary">View Products</router-link>
                        </div>

                    </div>
                </div>
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
            cart: [],
            isLoggedIn: localStorage.getItem('token') != null
        }
    },
    mounted() {
        this.getCart()
    },
    methods: {
        getCart(e) {
            console.log(e)
            if (this.isLoggedIn) {
                axios.get('/api/cart').then(response => {
                    this.cart = response.data.data
                    console.log(this.cart)
                }).catch(error => {

                })
            }
        },
        removeFromCart(productId) {
            axios.delete('/api/cart/' + productId).then(response => {
                this.cart = this.cart.filter(item => {
                    return item.product_id != productId;
                });
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
        }
    }
}
</script>
<style scoped>
.medium-text {
    font-size: 36px;
}

.small-link {
    font-size: 24px;
    text-decoration: underline;
    color: #777;
}

.product-box {
    border: 1px solid #cccccc;
    padding: 10px 15px;
}

.hero-section {
    height: 80vh;
    align-items: center;
    margin-bottom: 20px;
    margin-top: -20px;
}

.title {
    font-size: 60px;
}
</style>
