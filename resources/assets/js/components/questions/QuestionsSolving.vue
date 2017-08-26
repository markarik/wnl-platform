<template>
	<div>
		<div class="tabs">
			<ul>
				<li
					v-for="view in views"
					:class="{'is-active': view.name === activeView}"
					@click="activeView = view.name"
				>
					<a>
						<span class="icon is-small"><i class="fa" :class="view.icon"></i></span>
						{{$t(`questions.solving.tabs.${view.name}`, {count: questionsListCount})}}
					</a>
				</li>
			</ul>
		</div>
		<div class="active-filters">
			{{activeFiltersForDisplay}}
		</div>

		<!-- Current Question -->
		<div v-if="activeView === 'current'">
			<wnl-quiz-widget
				v-if="hasCurrentQuestion"
				:questions="[currentQuestion]"
				:module="module"
				:getReaction="getReaction"
			/>
		</div>

		<!-- List -->
		<div v-if="activeView === 'list'">
			<wnl-pagination v-if="meta.lastPage"
				:initialPage="meta.currentPage"
				:lastPage="meta.lastPage"
				@changePage="changePage"
			/>
			<div v-if="questionsCurrentPage.length > 0" v-for="(question, index) in questionsCurrentPage" :key="index">
				<p class="margin bottom">{{questionNumber(index)}}. {{question.text}}</p>
			</div>
		</div>

		<!-- Test -->

	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.active-filters
		font-size: $font-size-minus-2
		font-style: italic
		color: $color-background-gray
		margin: -$margin-base 0 $margin-medium

	.tabs
		font-size: $font-size-minus-1

		.is-active
			font-weight: $font-weight-regular
</style>

<script>
	import {isEmpty} from 'lodash'

	import QuizWidget from 'js/components/quiz/QuizWidget'
	import Pagination from 'js/components/global/Pagination'

	const views = [
		{
			name: 'current',
			icon: 'fa-check'
		},
		{
			name: 'list',
			icon: 'fa-list'
		},
		{
			name: 'test',
			icon: 'fa-clock-o'
		},
	]

	const limit = 30

	export default {
		name: 'QuestionsSolving',
		components: {
			'wnl-quiz-widget': QuizWidget,
			'wnl-pagination': Pagination,
		},
		props: {
			activeFilters: {
				default: () => [],
				type: Array,
			},
			currentQuestion: {
				default: () => {},
				type: Object,
			},
			questionsListCount: {
				default: 0,
				type: Number,
			},
			questionsCurrentPage: {
				default: () => [],
				type: Array,
			},
			getReaction: {
				default: () => {},
				type: Function,
			},
			meta: {
				default: () => {},
				type: Object,
			},
			module: {
				default: 'questions',
				type: String,
			},
			questions: {
				default: () => [],
				type: Array,
			},
		},
		data() {
			return {
				activeView: 'current',
			}
		},
		computed: {
			activeFiltersForDisplay() {
				if (this.activeFilters.length === 0) return ''

				return this.$t('questions.filters.activeFiltersReview', {
					filters: this.activeFilters.join(', ')
				})
			},
			count() {
				return this.questions.length
			},
			hasCurrentQuestion() {
				return !isEmpty(this.currentQuestion)
			},
			views() {
				return views
			},
		},
		methods: {
			changeQuestion(direction) {
				this.$emit('changeQuestion', direction)
			},
			changePage(page) {
				this.$emit('changePage', page)
			},
			questionNumber(index) {
				return (this.meta.currentPage - 1) * limit + index + 1
			},
		}
	}
</script>
