<template>
	<div>
		<h4>{{name}}</h4>
		<component :is="component" :screenData="screenData"></component>
		<wnl-qna :sortingEnabled="true" v-if="showQna" :tags="tags" class="wnl-screen-qna"></wnl-qna>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-screen-qna
		margin: $margin-huge 0
</style>

<script>
	import End from 'js/components/course/screens/End'
	import Html from 'js/components/course/screens/Html'
	import MockExam from 'js/components/course/screens/MockExam'
	import Qna from 'js/components/qna/Qna'
	import Quiz from 'js/components/quiz/Quiz'
	import Slideshow from 'js/components/course/screens/slideshow/Slideshow'
	import Widget from 'js/components/course/screens/Widget'
	import {mapGetters, mapActions} from 'vuex';

	const typesToComponents = {
		end: 'wnl-end',
		html: 'wnl-html',
		slideshow: 'wnl-slideshow',
		quiz: 'wnl-quiz',
		widget: 'wnl-widget',
		mockexam: 'wnl-mock-exam',
	}

	export default {
		name: 'Screen',
		components: {
			'wnl-end': End,
			'wnl-html': Html,
			'wnl-mock-exam': MockExam,
			'wnl-qna': Qna,
			'wnl-quiz': Quiz,
			'wnl-slideshow': Slideshow,
			'wnl-widget': Widget,
		},
		props: ['screenId'],
		computed: {
			...mapGetters('course', [
				'getScreen',
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
			},
			showQna() {
				return this.tags.length > 0
			}
		},
		methods: {
			...mapActions('qna', ['fetchQuestionsByTags']),
			...mapActions('course', ['fetchScreenContent']),
			...mapActions(['toggleOverlay']),

			fetchContent() {
				if (this.screenData.hasOwnProperty('content')) return

				this.toggleOverlay({source: 'screens', display: true})
				this.fetchScreenContent(this.screenId)
					.then(() => this.toggleOverlay({source: 'screens', display: false}))
					.catch((error) => {
						this.toggleOverlay({source: 'screens', display: false})
						$wnl.logger.capture(error)
					})
			}
		},
		mounted() {
			this.fetchContent()
			this.showQna && this.fetchQuestionsByTags({tags: this.tags})
		},
		watch: {
			screenId() {
				this.fetchContent()
				this.showQna && this.fetchQuestionsByTags({tags: this.tags})
			}
		}
	}
</script>
