<template>
	<div>
		<h4>{{name}}</h4>
		<component :is="component" :screenData="screenData"></component>
		<wnl-qna v-if="tags.length > 0"></wnl-qna>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import axios from 'axios'
	import End from 'js/components/course/screens/End.vue'
	import Html from 'js/components/course/screens/Html.vue'
	import Slideshow from 'js/components/course/screens/slideshow/Slideshow.vue'
	import Quiz from 'js/components/course/screens/quiz/Quiz.vue'
	import Widget from 'js/components/course/screens/Widget.vue'
	import Qna from 'js/components/qna/Qna'
	import {getApiUrl} from 'js/utils/env'
	import {mapGetters} from 'vuex';

	const typesToComponents = {
		end: 'wnl-end',
		html: 'wnl-html',
		slideshow: 'wnl-slideshow',
		quiz: 'wnl-quiz',
		widget: 'wnl-widget',
	}

	export default {
		name: 'Screen',
		components: {
			'wnl-end': End,
			'wnl-html': Html,
			'wnl-slideshow': Slideshow,
			'wnl-qna': Qna,
			'wnl-quiz': Quiz,
			'wnl-widget': Widget,
		},
		props: ['screenId'],
		computed: {
			...mapGetters('course', [
				'getScreen',
				'getScreenSectionsCheckpoints',
			]),
			screenData() {
				return this.getScreen(this.screenId)
			},
			name() {
				return this.screenData.name
			},
			type() {
				return this.screenData.type
			},
			tags() {
				return this.screenData.tags
			},
			component() {
				return typesToComponents[this.type]
			}
		},
	}
</script>
