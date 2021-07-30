<template>
	<div>
		<div class="form">
			<header>Login</header>
			<form @submit.prevent="login">
				<input type="text" placeholder="Username" v-model="loginData.username">
				<input type="password" placeholder="Password" v-model="loginData.password">
				<input type="submit" value="Login">
			</form>
		</div>
		<div class="form">
			<header>Register</header>
			<form @submit.prevent="register">
				<input type="text" placeholder="First Name" v-model="registerData.first_name">
				<input type="text" placeholder="Last Name" v-model="registerData.last_name">
				<input type="text" placeholder="Username" v-model="registerData.username">
				<input type="password" placeholder="Password" v-model="registerData.password">
				<input type="password" placeholder="Confirm Password" v-model="registerData.confirm">
				<input type="submit" value="Register">
			</form>
		</div>
	</div>
</template>

<script>
import axios from 'axios'

export default {
	data() {
		return {
			loginData: {
				username: '',
				password: ''
			},
			registerData: {
				first_name: '',
				last_name: '',
				username: '',
				password: '',
				confirm: ''
			}
		}
	},
	methods: {
		login() {
			axios.post('auth/login', this.loginData)
				.then(res => {
					localStorage.setItem('token', res.data.token);
					this.$router.push('/home');
				})
				.catch(err => {
					console.log(err.response.data.message);
				});
		},
		register() {
			axios.post('auth/register', this.registerData)
				.then(res => {
					localStorage.setItem('token', res.data.token);
					this.$router.push('/home');
				})
				.catch(err => {
					console.log(err.response.data.message);
				});
		}
	},
}
</script>

<style scoped>
* {
	margin: 0px;
	padding: 0px;
	font-family: Arial;
}

.form {
	display: flex;
	margin: 5em;
	align-items: center;
	justify-content: center;
	flex-flow: column;
}

.form input {
	display: flex;
	margin: 0.5em;
	padding: 0.2em;
	font-size: 1.2em;
}

.form header {
	display: block;
	font-weight: bold;
	font-size: 1.5em;
}
</style>