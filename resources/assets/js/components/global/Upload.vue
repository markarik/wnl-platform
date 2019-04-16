<template>
	<div class="wnl-upload" @click="click">
		<slot />
		<form @submit.prevent>
			<input
				:id="inputId"
				type="file"
				class="form-control"
				@change="inputChanged"
			>
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
import axios from 'axios';
import { getApiUrl } from 'js/utils/env';

export default {
	props: ['endpoint'],
	data() {
		return {
			inputId: `${this._uid}-input`,
			input: {}
		};
	},
	methods: {
		click() {
			this.input.click();
		},
		inputChanged(){
			let data = new FormData();

			this.$emit('uploadStarted');
			data.append('file', this.input.files[0]);

			axios.post(getApiUrl(this.endpoint), data)
				.then(response => {
					this.$emit('success', response.data);
				})
				.catch(error => {
					$wnl.logger.error(error);
					this.$emit('uploadError', error);
				});
		}
	},
	mounted() {
		this.input = document.getElementById(this.inputId);
	}
};
</script>
