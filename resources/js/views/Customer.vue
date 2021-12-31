<template>
    <div>
        <div class="container-fluid hero-section d-flex align-content-center justify-content-center flex-wrap ml-auto">
            <h2 class="title">Customer Dashboard</h2>
        </div>
        <notifications/>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-2" v-if="!isUserVerified">
                        <div class="card-body" v-if="user">
                            <p>Your Account is now verified, Request new Verification mail and gain access to our
                                products</p>
                            <button @click="sendVerificationEmail"
                                    class="btn btn-danger">Verify Your Account Now
                            </button>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body" v-if="user">
                            Name: <input class="col-md-12 form-control" type="text" v-model="user.name">
                            Email: <input class="col-md-12 form-control" type="email" v-model="user.email">
                            Password: <input class="col-md-12 form-control" type="password" v-model="user.password">
                            Confirm Password: <input class="col-md-12 form-control" type="password"
                                                     v-model="user.password_confirmation">
                            <br>
                            <button @click="updateUser"
                                    class="mx-2 btn btn-success">Save
                            </button>

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
            user: null,
            isUserVerified: false
        }
    },
    beforeMount() {
        this.user = JSON.parse(localStorage.getItem('user'))
        this.isUserVerified = !!(this.user.email_verified_at)
        if (!this.isUserVerified) {
            this.$notify({
                type: 'error',
                title: 'Not verified',
                text: 'You are not verified please verify your account.',
            });
        }
    },
    methods: {
        updateUser() {
            axios.post(`/api/update-profile`, this.user).then(response => {
                this.user = response.data.data
                localStorage.setItem('user', JSON.stringify(this.user))
                this.$emit('profileUpdate')
                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: 'User Updated successfully.',
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
        sendVerificationEmail() {
            axios.get(`/api/user/send-verification-email`).then(response => {
                this.user = response.data.data
                localStorage.setItem('user', JSON.stringify(this.user))
                this.$emit('profileUpdate')
                this.$notify({
                    type: response.data.status ? 'success' : 'error',
                    title: response.data.status ? 'Success' : 'Error',
                    text: response.data.message,
                });
            }).catch(error => {
                console.log(error.response.data.message)
            })
        }
    }
}
</script>
<style scoped>
.small-text {
    font-size: 14px;
}

.product-box {
    border: 1px solid #cccccc;
    padding: 10px 15px;
}

.hero-section {
    height: 20vh;
    background: #ababab;
    align-items: center;
    margin-bottom: 20px;
    margin-top: -20px;
}

.title {
    font-size: 60px;
    color: #ffffff;
}
</style>
