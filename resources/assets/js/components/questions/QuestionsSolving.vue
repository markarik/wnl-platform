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

		<div v-if="hasCurrentQuestion">
			<!-- Current Question -->
			<div v-if="activeView === 'current'">
				<p>
					{{$t('questions.solving.current', {number: currentQuestionNumber})}}
					<span class="matched-count">{{questionsListCount}}</span>
				</p>
				<wnl-active-question
					:currentQuestion="currentQuestion"
					:module="module"
					:getReaction="getReaction"
					@changeQuestion="changeQuestion"
					@selectAnswer="selectAnswer"
					@verify="onVerify"
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
					<p class="margin bottom" @click="setQuestion(meta.currentPage, index)">{{questionNumber(index)}}. {{question.text}}</p>
				</div>
			</div>

			<!-- Test -->
			<div v-if="activeView === 'test'">
				<section>
					<p>Na ile pytań chcesz odpowiedzieć?</p>
					<input type="radio" name="count" value="30" id="countThirty" v-model="testQuestionsCount"/>
					<label for="countThirty">30 pytań</label>
					<input type="radio" name="count" value="50" id="countFifty" v-model="testQuestionsCount"/>
					<label for="countFifty">50 pytań</label>
					<input type="radio" name="count" value="100" id="countHundred" v-model="testQuestionsCount"/>
					<label for="countHundred">100 pytań</label>
					<input type="radio" name="count" value="150" id="countOneFifty" v-model="testQuestionsCount"/>
					<label for="countNinty">150 pytań</label>
					<input type="radio" name="count" value="120" id="countTwoHundred" v-model="testQuestionsCount"/>
					<label for="countTwoHundred">200 pytań</label>
				</section>
				<section>
					<label for="time">Ile czasu chcesz poświęcić?</label>
					<input type="text" name="time" v-model="estimatedTime"/>
					<span>minut</span>
				</section>
				<button @click="buildTest">No to GO!</button>
			</div>
		</div>

		<div v-else class="has-text-centered margin vertical metadata">
			{{$t('questions.zeroState')}}
		</div>
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

	.matched-count
		color: $color-green
</style>

<script>
	import {isEmpty} from 'lodash'

	import ActiveQuestion from 'js/components/questions/ActiveQuestion'
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
			'wnl-active-question': ActiveQuestion,
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
				estimatedTime: 0,
				testQuestionsCount: 0,
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
			currentQuestionNumber() {
				return (this.currentQuestion.page - 1) * this.meta.perPage + this.currentQuestion.index + 1
			},
			hasCurrentQuestion() {
				return !isEmpty(this.currentQuestion) && !!this.currentQuestion.id
			},
			views() {
				return views
			},
		},
		methods: {
			buildTest() {
				// TODO: Allow to change time
				this.$emit('buildTest', {count: this.testQuestionsCount})
			},
			changeQuestion(direction) {
				this.$emit('changeQuestion', direction)
			},
			changePage(page) {
				this.$emit('changePage', page)
			},
			questionNumber(index) {
				return (this.meta.currentPage - 1) * limit + index + 1
			},
			selectAnswer(payload) {
				this.$emit('selectAnswer', payload)
			},
			setQuestion(page, index) {
				this.$emit('setQuestion', {page, index})
				this.activeView = 'current'
			},
			onVerify(payload) {
				this.$emit('verify', payload)
			},
		}
	}
</script>
