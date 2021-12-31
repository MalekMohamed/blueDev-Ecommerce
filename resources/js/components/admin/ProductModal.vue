<template>
    <div class="modal modal-mask">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 v-html="data.name ? data.name : 'New Product'">
                    </h4>
                    <button class="btn btn-outline-danger float-right" @click="closeModal">Close</button>
                </div>

                <div class="modal-body">
                    <slot name="body">
                        Name: <input class="col-md-12 form-control" type="text" v-model="data.name">
                        <div class="row my-2">
                            <div class="col-md-6 ">
                                <label>Brand:</label>
                                <select class="form-control" v-model="data.brand_id">
                                    <option v-for="(brand,index) in brands" :key="brand.id"
                                            :selected="data.category_id === null && index ===0" :value="brand.id">
                                        {{ brand.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 ">
                                <label>Category:</label>
                                <select class="form-control" v-model="data.category_id">
                                    <option v-for="(category,index) in categories" :key="category.id"
                                            :selected="data.category_id === null"
                                            :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                        </div>
                        Quantity: <input class="col-md-12 form-control" type="number" v-model="data.qty">
                        Price: <input class="col-md-12 form-control" type="number" v-model="data.price">
                        <textarea class="col-md-12 form-control my-2" v-model="data.description"
                                  placeholder="description"></textarea>
                        <span>
              <img class="img-thumbnail mb-2" style="width: 100%;height: 25vh !important;object-fit: cover;" :src="data.image" v-show="data.image != null">
              <input class="col-md-12 form-control my-2" type="file" id="file" @change="attachFile">
            </span>
                    </slot>
                </div>

                <div class="modal-footer">
                    <slot name="footer">
                        <button class="modal-default-button btn btn-primary" @click="submitModalForm">
                           Submit
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['product'],
    data() {
        return {
            attachment: null,
            brands: [],
            categories: [],
        }
    },
    beforeMount() {
        this.getBrands();
        this.getCategories();
    },
    computed: {
        data: function () {
            if (this.product != null) {
                return this.product
            } else {
                return {
                    name: "",
                    qty: "",
                    price: "",
                    category_id: "",
                    brand_id: "",
                    description: "",
                    image: false

                }
            }
        }
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
        attachFile(event) {
            this.attachment = event.target.files[0];
            console.log(event.target.files[0])
        },
        closeModal() {
            this.$emit('close', this.product)
        },
        submitModalForm(event) {
            if (this.attachment != null) {
                var formData = new FormData();
                formData.append("image", this.attachment)
                axios.post("/api/upload-file", formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    this.product.image = response.data
                    this.$emit('productCreate', this.product)
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
            } else {
                this.$emit('close', this.product)
            }
        }
    }
}
</script>
<style scoped>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    display: table;
    transition: opacity .3s ease;
}

.modal-wrapper {
    display: table-cell;
    vertical-align: middle;
}

.modal-container {
    width: 300px;
    margin: 0px auto;
    padding: 20px 30px;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: Helvetica, Arial, sans-serif;
}

.modal-header h3 {
    margin-top: 0;
    color: #42b983;
}

.modal-body {
    margin: 20px 0;
}

.modal-default-button {
    float: right;
}

.modal-enter {
    opacity: 0;
}

.modal-leave-active {
    opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
}
</style>
