<template lang="html">
	<div @click="click" class="wnl-upload">
		<slot></slot>
		<form @submit.prevent>
			<input :id="inputId" @change="inputChanged" type="file" class="form-control"/>
		</form>
	</div>

</template>

<style lang="sass" rel="stylesheet/sass">
	.wnl-upload
		display: inline-block

		form
			display: none
			position: fixed
			top: -999px
			z-index: -999
</style>

<script>
	import axios from 'axios'

	export default {
		data() {
			return {
				inputId: `${this._uid}-input`,
				input: {}
			}
		},
		methods: {
			click() {
				this.input.click();
			},
			inputChanged(){
				let data = new FormData();
				data.append('file', this.input.files[0]);

				let config = {
					onUploadProgress: function (progressEvent) {
						console.log(Math.round((progressEvent.loaded * 100) / progressEvent.total));
					}
				}

				axios.post('/papi/v1/users/current/avatar', data, config)
						.then(response => {
							this.$emit('success')
						})
			}
		},
		mounted() {
			this.input = document.getElementById(this.inputId)
		}
	}
</script>
