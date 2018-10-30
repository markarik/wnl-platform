<template>
	<div class="collections-quiz">
		<p class="title is-4">Zapisane pytania kontrolne ({{howManyQuestions}})</p>
		<div v-if="isLoaded || howManyQuestions === 0">
			<div class="pagination-container">
				<wnl-pagination v-if="lastPage && lastPage > 1"
					:currentPage="currentPage"
					:lastPage="lastPage"
					@changePage="changePage"
				/>
			</div>
			<wnl-quiz-widget
				v-if="howManyQuestions > 0"
				:questions="getQuestionsWithAnswers"
				:getReaction="getReaction"
				@changeQuestion="performChangeQuestion"
				@verify="resolveQuestion"
				@selectAnswer="onSelectAnswer"
				@userEvent="onUserEvent"
			></wnl-quiz-widget>
			<div v-else class="notification has-text-centered">
				W temacie <span class="metadata">{{rootCategoryName}} <span class="icon is-small"><i class="fa fa-angle-right"></i></span> {{categoryName}}</span> nie ma jeszcze zapisanych pytań kontrolnych. Możesz łatwo to zmienić klikając na <span class="icon is-small"><i class="fa fa-star-o"></i></span> <span class="metadata">ZAPISZ</span> przy wybranym pytaniu!
			</div>
		</div>
		<wnl-text-loader v-else></wnl-text-loader>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.collections-quiz
		padding: $margin-base 0
</style>

<style lang="sass">
	.collections-quiz .pagination-list
		justify-content: center
</style>

<script>
	import {mapActions, mapGetters, mapState} from 'vuex'

	import QuizWidget from 'js/components/quiz/QuizWidget'
	import Pagination from 'js/components/global/Pagination'
	import emits_events from 'js/mixins/emits-events'

	export default {
		name: 'QuizCollection',
		components: {
			'wnl-quiz-widget': QuizWidget,
			'wnl-pagination': Pagination
		},
		mixins: [emits_events],
		props: ['categoryName', 'rootCategoryName', 'quizQuestionsIds'],
		computed: {
			...mapState('quiz', ['pagination']),
			...mapGetters('quiz', ['isLoaded', 'getQuestionsWithAnswers', 'getReaction', 'isComplete', 'getQuestion']),
			howManyQuestions() {
				return this.quizQuestionsIds.length
			},
			lastPage() {
				return this.pagination.last_page;
			},
			currentPage() {
				return this.pagination.current_page;
			}
		},
		methods: {
			...mapActions('quiz', ['shuffleAnswers', 'changeQuestion', 'resolveQuestion', 'commitSelectAnswer']),
			performChangeQuestion(index) {
				this.shuffleAnswers({id: this.getQuestionsWithAnswers[index].id})
				this.changeQuestion(index)
			},
			onSelectAnswer({id, answer}) {
				answer === this.getQuestion(id).selectedAnswer
					? this.resolveQuestion(id)
					: !this.isComplete && this.commitSelectAnswer({id, answer})
			},
			changePage(page) {
				this.$emit('changeQuizQuestionsPage', page)
			},
			onUserEvent(payload) {
				this.emitUserEvent({
					...payload,
					feature: 'quiz_questions'
				})
			}
		}
	}
</script>
