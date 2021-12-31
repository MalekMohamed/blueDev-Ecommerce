<template>
    <div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <button class="btn btn-dark" @click="newProduct">Add New Product</button>
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
                        <td></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(product,index) in products" v-if="product.brand && product.category" :key="index"
                        @click="editingItem = product">
                        <td>{{ index + 1 }}</td>
                        <td><a href="javascript:void(0)">{{ product.name }}</a></td>
                        <td>{{ product.brand.name }}, {{ product.category.name }}</td>
                        <td v-html="product.sku"></td>
                        <td v-model="product.units">{{ product.qty }}</td>
                        <td v-model="product.price">{{ product.price }}</td>
                        <td>{{ product.user.name }}</td>
                        <td v-model="product.price">{{ product.description }}</td>
                        <td>
                            <button @click="deleteProduct(product)" class="btn btn-danger">Remove</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <modal @productCreate="endEditing" @close="editingItem = null" :product="editingItem"
                       v-show="editingItem != null"></modal>
                <modal @productCreate="addProduct" @close="addingProduct = null" :product="addingProduct"
                       v-show="addingProduct != null"></modal>
                <br>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between">
                <button class="btn btn-primary" @click="showNewBrand = !showNewBrand;showNewCategory = false;">Add New
                    Brand
                </button>
                <button class="btn btn-info" @click="showNewCategory = !showNewCategory; showNewBrand = false;">Add New
                    Category
                </button>

            </div>
            <div class="card-body">
                <div v-if="showNewBrand">
                    Brand Name: <input class="col-md-12 form-control" type="text" v-model="newBrand.name">
                    <button class="my-2 btn btn-primary" @click="addNewBrand">
                        Submit
                    </button>
                </div>
                <div v-if="showNewCategory">
                    Category Name: <input class="col-md-12 form-control" type="text" v-model="newCategory.name">
                    <button class="my-2 btn btn-primary" @click="addNewCategory">
                        Submit
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Brands</h4>
                        <table class="table table-responsive table-striped">
                            <thead>
                            <tr>
                                <td></td>
                                <td>Name</td>
                                <td></td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(brand,index) in brands" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td @click="currentBrand=brand" class="w-100"><a href="javascript:void(0)">{{
                                        brand.name
                                    }}</a></td>
                                <td class="w-50">
                                    <button @click="deleteBrand(brand)" class="btn btn-danger">Remove</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-if="currentBrand.name">
                            Brand Name: <input class="col-md-12 form-control" type="text" v-model="currentBrand.name">
                            <button class="my-2 btn btn-primary" @click="updateBrand">
                                Save
                            </button>
                        </div>
                    </div>
                    <span class="border-right"></span>
                    <div class="col-md-5">
                        <h4>Categories</h4>
                        <table class="table table-responsive table-striped">
                            <thead>
                            <tr>
                                <td></td>
                                <td>Name</td>
                                <td></td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(category,index) in categories" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td @click="currentCategory=category" class="w-100">
                                    <a href="javascript:void(0)">{{ category.name }}</a>
                                </td>
                                <td class="w-50">
                                    <button @click="deleteCategory(category)" class="btn btn-danger">Remove</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-if="currentCategory.name">
                            Category Name: <input class="col-md-12 form-control" type="text"
                                                  v-model="currentCategory.name">
                            <button class="my-2 btn btn-primary" @click="updateCategory">
                                Save
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
import Modal from './ProductModal'

export default {
    data() {
        return {
            products: [],
            newCategory: {name: ''},
            newBrand: {name: ''},
            showNewCategory: false,
            showNewBrand: false,
            editingItem: null,
            addingProduct: null,
            brands: [],
            categories: [],
            currentCategory: {},
            currentBrand: {},
        }
    },
    components: {
        Modal
    },
    beforeMount() {
        axios.get('/api/products')
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
        this.getBrands();
        this.getCategories();

    },
    methods: {
        getBrands() {
            axios.get('/api/brands').then(response => {
                this.brands = response.data.data;
            }).catch(error => {
                this.$notify({
                    type: 'error',
                    title: 'Error',
                    text: error.response.data.message
                })
            })
        },
        getCategories() {
            axios.get('/api/categories').then(response => {
                this.categories = response.data.data;
            }).catch(error => {
                this.$notify({
                    type: 'error',
                    title: 'Error',
                    text: error.response.data.message
                })
            })
        },
        addNewCategory() {
            axios.post("/api/categories", this.newCategory)
                .then(response => {
                    this.showNewCategory = false
                    this.categories.push(response.data.data)
                    this.$notify({
                        type: 'success',
                        title: 'Success',
                        text: response.data.message
                    })
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
        },
        addNewBrand() {
            axios.post("/api/brands", this.newBrand)
                .then(response => {
                    this.showNewBrand = false
                    this.brands.push(response.data.data)
                    this.$notify({
                        type: 'success',
                        title: 'Success',
                        text: response.data.message
                    })
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
        },
        updateCategory() {
            axios.put("/api/categories/" + this.currentCategory.id, {name: this.currentCategory.name})
                .then(response => {
                    this.$notify({
                        type: 'success',
                        title: 'Success',
                        text: response.data.message
                    })
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
        },
        updateBrand() {
            axios.put("/api/brands/" + this.currentBrand.id, {name: this.currentBrand.name})
                .then(response => {
                    this.$notify({
                        type: 'success',
                        title: 'Success',
                        text: response.data.message
                    })
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
        },
        deleteBrand(brand) {
            axios.delete(`/api/brands/${brand.id}`).then(response => {
                this.brands = this.brands.filter(brands => {
                    return brands.id !== brand.id;
                });
                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: response.data.message,
                });
            }).catch(error => {
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
        },
        deleteCategory(category) {
            axios.delete(`/api/categories/${category.id}`).then(response => {
                this.categories = this.categories.filter(categories => {
                    return categories.id !== category.id;
                });
                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: response.data.message,
                });
            }).catch(error => {
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
        },
        deleteProduct(product) {
            axios.delete(`/api/products/${product.id}`).then(response => {
                this.products = this.products.filter(products => {
                    return products.id !== product.id;
                });
                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: 'Product Deleted successfully.',
                });
            }).catch(error => {
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
        },
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
                image: product.image
            })
                .then(response => {
                    this.products[index] = response.data.data
                    this.$notify({
                        type: 'success',
                        title: 'Success',
                        text: response.data.message,
                    });
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
            axios.post("/api/products", {
                name: product.name,
                qty: product.qty,
                category_id: product.category_id,
                brand_id: product.brand_id,
                price: product.price,
                description: product.description,
                image: product.image
            }).then(response => {
                this.products.push(response.data.data)
                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: response.data.message,
                });
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
        },
    }
}
</script>
