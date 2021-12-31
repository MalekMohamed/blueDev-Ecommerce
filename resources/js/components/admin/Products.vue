<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary float-right" @click="newProduct">Add New Product</button>
        </div>
        <div class="card-body">

            <notifications/>
            <table class="table table-responsive table-striped">
                <thead>
                <tr>
                    <td></td>
                    <td>Product</td>
                    <td>Brand, Category</td>
                    <td>SKU</td>
                    <td>Quantity</td>
                    <td>Price</td>
                    <td>Item Owner</td>
                    <td>Description</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(product,index) in products" :key="index" @click="editingItem = product">
                    <td>{{ index + 1 }}</td>
                    <td><a href="javascript:void(0)">{{product.name}}</a></td>
                    <td>{{ product.brand.name }}, {{ product.category.name}}</td>
                    <td v-html="product.sku"></td>
                    <td v-model="product.units">{{ product.qty }}</td>
                    <td v-model="product.price">{{ product.price }}</td>
                    <td>{{ product.user.name }}</td>
                    <td v-model="product.price">{{ product.description }}</td>
                </tr>
                </tbody>
            </table>
            <modal @productCreate="endEditing" @close="editingItem = null" :product="editingItem" v-show="editingItem != null"></modal>
            <modal @productCreate="addProduct"  @close="addingProduct = null" :product="addingProduct" v-show="addingProduct != null"></modal>
            <br>

        </div>
    </div>

</template>
<script>
import Modal from './ProductModal'

export default {
    data() {
        return {
            products: [],
            editingItem: null,
            addingProduct: null
        }
    },
    components: {
        Modal
    },
    beforeMount() {
        axios.get('/api/products/')
            .then(response => {
                this.products = response.data.data
            })
            .catch(error => {
                this.$notify({
                    type: 'error',
                    title: 'Error',
                    text: error.response.data.message
                })
            })
    },
    methods: {
        newProduct() {
            this.addingProduct = {
                name: null,
                qty: null,
                category_id: "",
                brand_id: "",
                price: null,
                description: null,
                image: null
            }
        },
        endEditing(product) {
            this.editingItem = null
            let index = this.products.indexOf(product)
            axios.put(`/api/products/${product.id}`, {
                name: product.name,
                qty: product.qty,
                category_id: product.category_id,
                brand_id: product.brand_id,
                price: product.price,
                description: product.description,
            })
                .then(response => {
                    this.products[index] = response.data.data
                })
                .catch(error => {
                    this.$notify({
                        type: 'error',
                        title: 'Error',
                        text: error.response.data.message
                    })
                })
        },
        addProduct(product) {
            this.addingProduct = null
            axios.post("/api/products/", {
                name: product.name,
                qty: product.qty,
                category_id: product.category_id,
                brand_id: product.brand_id,
                price: product.price,
                description: product.description,
                image: product.image
            })
                .then(response => {
                    this.products.push(response.data.data)
                })
                .catch(error => {
                    if (typeof error.response.data.message === 'object') {
                        Object.keys(error.response.data.message).forEach(fieldError => {
                            this.$notify({
                                type: 'error',
                                title: fieldError,
                                text: error.response.data.message[fieldError]
                            })
                        })
                    } else {
                        this.$notify({
                            type: 'error',
                            title: 'Error',
                            text: error.response.data.message
                        })
                    }
                })
        }
    }
}
</script>
