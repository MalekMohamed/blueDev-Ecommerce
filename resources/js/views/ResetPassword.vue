<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Reset Password?</div>
                    <notifications/>
                    <div class="card-body">
                        <form>
                            <input type="hidden" v-model="token">
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" v-model="email" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" v-model="password"
                                           required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">Password
                                    Confirmation</label>

                                <div class="col-md-6">
                                    <input id="password_confirmation" type="password" class="form-control"
                                           v-model="password_confirmation" required>
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
            token: this.$route.params.token ? this.$route.params.token : '',
            password: "",
            password_confirmation: "",
        }
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()
            if (this.token) {
                axios.post('/api/reset-password', {
                    email: this.email,
                    token: this.token,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                }).then(response => {
                        this.$router.push('/login').then(resp => {
                            this.$notify({
                                type: 'success',
                                title: 'Success',
                                text: response.data.message,
                            });
                        })
                    }).catch(error => {
                    this.sendErrorMsg(error)
                    });
            } else {
                this.$router.push('/login')
                this.$notify({
                    type: 'error',
                    title: 'Error',
                    text: 'Invalid Token',
                });
            }
        }
    }
}
</script>
