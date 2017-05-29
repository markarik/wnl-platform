<template>
	<span class="icon text-dimmed" :class="[sizeClass]" :title="computedTitle" @click="checkSanity">
		<i class="fa fa-trash"></i>
	</span>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.icon
		cursor: pointer

		&:hover
			color: $color-red
</style>

<script>
	import axios from 'axios'

	import { swalConfig } from 'js/utils/swal'
	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'Delete',
		props: ['target', 'requestRoute', 'title'],
		computed: {
			sizeClass() {
				return 'is-small'
			},
			computedTitle() {
				return this.title || 'Usuń'
			},
			targetText() {
				return `Chcesz usunąć ${this.target}?`
			},
		},
		methods: {
			checkSanity() {
				this.$swal(swalConfig({
					showCancelButton: true,
					cancelButtonColor: '#696969',
					cancelButtonText: 'Nie, jednak nie',
					confirmButtonClass: 'button is-danger',
					confirmButtonColor: '#e53d2c',
					confirmButtonText: 'Tak, usuwam',
					text: this.targetText,
					title: 'Na pewno?',
					type: 'question',
				})).then(() => {
					this.sendRequest()
				}, () => false)
			},
			sendRequest() {
				axios.delete(getApiUrl(this.requestRoute))
			},
		}
	}
</script>
