<template>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Login</div>
                    <notifications/>
                    <div class="card-body">

                        <form>
                            <div class="form-group row">
                                <label for="email" class="col-sm-4 col-form-label text-md-right">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" v-model="email" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" v-model="password" required>
                                </div>
                            </div>

                            <div class="form-group col-md-8 offset-md-4 row mb-0">
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary" @click="handleSubmit">
                                        Login
                                    </button>

                                </div>
                                <router-link class="col-md-6 float-right align-self-center" :to="{name:'forget-password'}">Forget Password?</router-link>

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
        data(){
            return {
                email : "",
                password : ""
            }
        },
        methods : {
            handleSubmit(e){
                e.preventDefault()

                if (this.password.length > 0) {
                    axios.post('api/login', {
                        email: this.email,
                        password: this.password
                      })
                      .then(response => {
                        localStorage.setItem('user',JSON.stringify(response.data.user))
                        localStorage.setItem('token',response.data.token)

                        if (localStorage.getItem('token') != null){
                            this.$emit('loggedIn')
                            if(this.$route.params.nextUrl != null){
                                this.$router.push(this.$route.params.nextUrl)
                            }
                            else {
                                if(response.data.user.role === 'Admin'){
                                    this.$router.push('admin').then(resp=> {
                                        this.$notify({
                                            type: 'success',
                                            title: 'Login Successful',
                                            text: response.data.message,
                                        });
                                    })
                                }
                                else {
                                    this.$router.push('dashboard').then(resp=> {
                                        this.$notify({
                                            type: 'success',
                                            title: 'Login Successful',
                                            text: response.data.message,
                                        });
                                    })
                                }
                            }
                        }
                      })
                      .catch(error => {
                          this.sendErrorMsg(error)
                      });
                }
            },
            sendErrorMsg () {

            }
        },
    }
</script>
