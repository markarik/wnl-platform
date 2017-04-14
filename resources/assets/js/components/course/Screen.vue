<template>
	<div class="container">
		<h4>{{name}}</h4>
		<component :is="component" :screenData="screenData" :slide="slide"></component>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import axios from 'axios'
	import End from 'js/components/course/screens/End.vue'
	import Html from 'js/components/course/screens/Html.vue'
	import Slideshow from 'js/components/course/screens/Slideshow.vue'
	import Quiz from 'js/components/course/screens/quiz/Quiz.vue'
	import Widget from 'js/components/course/screens/Widget.vue'
	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'Screen',
		components: {
			'wnl-end': End,
			'wnl-html': Html,
			'wnl-slideshow': Slideshow,
			'wnl-quiz': Quiz,
			'wnl-widget': Widget,
		},
		props: ['screenId', 'slide'],
		data: () => {
			return {
				screenData: {},
				typesToComponents: {
					end: 'wnl-end',
					html: 'wnl-html',
					slideshow: 'wnl-slideshow',
					quiz: 'wnl-quiz',
					widget: 'wnl-widget',
				}
			}
		},
		computed: {
			id() {
				return this.screenData.id
			},
			name() {
				return this.screenData.name
			},
			type() {
				return this.screenData.type
			},
			component() {
				return this.typesToComponents[this.type]
			}
		},
		methods: {
			getScreenData(screenId) {
				axios.get(getApiUrl(`screens/${screenId}`))
					.then((response) => {
						this.screenData = response.data
					})
					.catch(error => console.log(error))
			}
		},
		mounted() {
			this.getScreenData(this.screenId)
		},
		watch: {
			'$route' (to, from) {
				if (to.params.screenId !== from.params.screenId) {
					this.getScreenData(to.params.screenId)
				}
			}
		}
	}
</script>
