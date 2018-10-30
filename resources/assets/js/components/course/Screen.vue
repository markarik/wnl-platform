<template>
	<div>
		<h4>{{name}}</h4>
		<component :is="component" :screenData="screenData" :key="screenData.id" @userEvent="proxyUserEvent"></component>
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
	import Flashcards from 'js/components/course/screens/Flashcards'
	import Qna from 'js/components/qna/Qna'
	import Quiz from 'js/components/quiz/Quiz'
	import Slideshow from 'js/components/course/screens/slideshow/Slideshow'
	import Widget from 'js/components/course/screens/Widget'
	import emits_events from 'js/mixins/emits-events';
	import {mapGetters, mapActions} from 'vuex';

	const TYPES_MAP = {
		end: {
			component: 'wnl-end',
			feature_component: 'bonuses'
		},
		html: {
			component: 'wnl-html',
			feature_component: 'html'
		},
		slideshow: {
			component: 'wnl-slideshow',
			feature_component: 'slideshow'
		},
		quiz: {
			component: 'wnl-quiz',
			feature_component: 'quiz_set'
		},
		widget: {
			component: 'wnl-widget',
			feature_component: 'widget'
		},
		mockexam: {
			component: 'wnl-mock-exam',
			feature_component: 'mockexam'
		},
		flashcards: {
			component: 'wnl-flashcards',
			feature_component: 'flashcards'
		},
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
			'wnl-flashcards': Flashcards
		},
		mixins: [emits_events],
		data() {
			return {
				feature: 'screen'
			}
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
				return TYPES_MAP[this.type].component
			},
			showQna() {
				return this.tags.length > 0
			},
			eventFeatureComponent() {
				return TYPES_MAP[this.type].feature_component
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
			},
			trackScreenOpen() {
				this.emitUserEvent({
					feature: this.feature,
					feature_component: this.eventFeatureComponent,
					action: 'open',
					target: this.screenId
				})
			}
		},
		mounted() {
			this.fetchContent()
			this.showQna && this.fetchQuestionsByTags({tags: this.tags})
			this.trackScreenOpen()
		},
		watch: {
			screenId() {
				this.fetchContent()
				this.showQna && this.fetchQuestionsByTags({tags: this.tags})
				this.trackScreenOpen()
			}
		}
	}
</script>
