<template>
	<div class="container">
		<h3>{{name}}</h3>
		<component :is="component" :screenData="screenData" :slide="slide"></component>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import axios from 'axios'
	import Html from './Html.vue'
	import Slideshow from './Slideshow.vue'
	import { getApiUrl } from '../utils/env'

	export default {
		name: 'Screen',
		components: {
			'wnl-html': Html,
			'wnl-slideshow': Slideshow
		},
		props: ['screenId', 'slide'],
		data: () => {
			return {
				screenData: {},
				typesToComponents: {
					html: 'wnl-html',
					slideshow: 'wnl-slideshow'
				}
			}
		},
		computed: {
			name() {
				return this.screenData.name
			},
			type() {
				return this.screenData.type
			},
			id() {
				return this.screenData.id
			},
			component() {
				return this.typesToComponents[this.type]
			}
		},
		methods: {
			getScreenData(screenId) {
				axios.get(getApiUrl(`screens/${screenId}`))
					.then((response) => {
						this.screenData = response.data.screen
					})
					.catch(error => console.log(error))
			}
		},
		mounted() {
			this.getScreenData(this.screenId)
		},
		watch: {
			'$route' (to, from) {
				this.getScreenData(to.params.screenId)
			}
		}
	}
</script>
