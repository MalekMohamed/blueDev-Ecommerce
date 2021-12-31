<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <img class="img-thumbnail mb-2" style="width: 100%;height: 25vh !important;object-fit: cover;" :src="product.image" :alt="product.name">
                <h3 class="title" v-html="product.name"></h3>
                <h5 class="float-right" v-html="product.sku"></h5>
                <p class="text-muted">{{product.description}}</p>
                <h4>
                    <span class="small-text text-muted float-left">$ {{product.price}}</span>
                    <span class="small-text float-right">Available Quantity: {{product.units}}</span>
                </h4>
                <br>
                <hr>
                <router-link :to="{ name: 'checkout',params: {pid: product.id} }" class="col-md-4 btn btn-sm btn-primary float-right">Buy Now</router-link>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data(){
            return {
                product : []
            }
        },
        beforeMount(){
            axios.get(`/api/products/${this.$route.params.id}`)
            .then(response => {
                this.product = response.data.data
            })
            .catch(error => {
                console.error(error);
            })
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
