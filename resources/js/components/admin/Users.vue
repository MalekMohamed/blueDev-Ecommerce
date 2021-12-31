<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary float-right" @click="newUser = !newUser">Add New User</button>
        </div>
        <notifications/>
        <div class="card-body">


        <table class="table table-responsive table-striped">
            <thead>
                <tr>
                    <td></td>
                    <td>Name</td>
                    <td>Email</td>
                    <td>Role</td>
                    <td>Joined</td>
                    <td>Verified</td>
                    <td>Owned Products</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(user,index) in users" :key="index">
                    <td>{{index+1}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.role}}</td>
                    <td class="w-25">{{user.created_at}}</td>
                    <td class="w-25">{{user.email_verified_at}}</td>
                    <td>{{user.products.length}}</td>
                    <td class="w-100">
                        <button @click="currentUser = user;newUser=false;" class="btn btn-info">Edit</button>
                        <button @click="deleteUser(user)" class="btn btn-outline-danger">Remove</button>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
        <div class="card-footer" v-if="currentUser.name !== '' || newUser">

                Name: <input class="col-md-12 form-control" type="text" v-model="currentUser.name">
                Email: <input class="col-md-12 form-control" type="email" v-model="currentUser.email">
                Password: <input class="col-md-12 form-control" type="password" v-model="currentUser.password">
                Confirm Password: <input class="col-md-12 form-control" type="password" v-model="currentUser.password_confirmation">
                <div class="row my-2">
                    <div class="col-md-12 ">
                        <label>Role:</label>
                        <select class="form-control" v-model="currentUser.role">
                            <option value="Customer" selected>Customer</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <button @click="createNewUser" v-if="newUser" class="btn btn-success">Create</button>
                <button @click="updateUser" v-if="currentUser.name !== '' && !newUser" class="btn btn-success">Save</button>

        </div>
    </div>
</template>
<script>
	export default {
        data(){
            return {
                users : [],
                newUser: false,
                currentUser: {
                    name:'',
                    email:'',
                    password:'',
                    role:'Customer',
                }
            }
        },
        beforeMount(){
            axios.get('/api/users/')
            .then(response => {
                this.users = response.data.data
            })
            .catch(error => {
                console.error(error);
            })
        },
        methods: {
            createNewUser() {
                axios.post('/api/register',this.currentUser).then(response => {
                    this.users.push(response.data)
                    this.$notify({
                        type: 'success',
                        title: 'Success',
                        text: 'Account created successfully. A verification mail sent to '+this.currentUser.email+'',
                    });
                    this.currentUser = {};
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
            updateUser () {
                let index = this.users.indexOf(this.currentUser);
                axios.put(`/api/users/${this.currentUser.id}`,this.currentUser).then(response => {
                    this.users[index] = response.data.data
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
            deleteUser (user) {
                axios.delete(`/api/users/${user.id}`).then(response => {
                    this.users = this.users.filter(newUsers => {
                        return newUsers.id !== user.id;
                    });
                    this.$notify({
                        type: 'success',
                        title: 'Success',
                        text: 'User Deleted successfully.',
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
            }
        }
    }
</script>
