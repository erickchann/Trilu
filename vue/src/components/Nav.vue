<template>
    <div>
        <div class="header">
            <div>
                <router-link to="/home">Home</router-link>
            </div>
            <div class="right">
                <router-link to="">John Doe</router-link>
                <a @click.prevent="logout" href="">Logout</a>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    data() {
        return {
            token: localStorage.getItem('token')
        }
    },
    methods: {
        logout() {
            axios.get(`auth/logout?token=${this.token}`)
                .then(res => {
                    localStorage.removeItem('token');
                    this.$router.push('/');
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        }
    },
}
</script>

<style scoped>
.header{
	height: 40px;
	position: relative;
	background: gray;
	display: flex;
	align-items: center;
	padding: 0 1em;
	background: #248ea9;
	color: #ffe;
	font-weight: bold;
}

.header a, a:visited, a:link {
    text-decoration: none;
    color: #ffe;
    cursor: inherit;
}

.header a:hover{
	color: #aee7e8;
    cursor: pointer;
}

.header div.right{
	display: flex;
	justify-content: flex-end;
	flex-grow: 1;
}

.header div a{
	margin: 0 0.3em;
}
</style>