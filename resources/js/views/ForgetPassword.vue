<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Forget Password?</div>
                    <notifications/>
                    <div class="card-body">
                        <form>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" v-model="email" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary" @click="handleSubmit">
                                        Reset Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['nextUrl'],
    data() {
        return {
            email: "",
        }
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()
            axios.post('/api/forget-password', {
                email: this.email,
            })
                .then(response => {
                    this.$router.push('/login').then(resp => {
                        this.$notify({
                            type: 'success',
                            title: 'Success',
                            text: 'Please Check Your Email to continue the process',
                        });
                    })
                })
                .catch(error => {
                    this.sendErrorMsg(error)
                });
        }
    }
}
</script>
