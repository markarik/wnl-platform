<template>
	<div class="wnl-quiz-widget">
		<div class="level">
			<div class="level-left">
				<a class="small unselectable" @click="changeQuestion(lastIndex)">
					<span class="icon is-small"><i class="fa fa-angle-left"></i></span> Poprzednie
				</a>
			</div>
			<div class="level-right">
				<a class="small unselectable" @click="changeQuestion(1)">
					Następne <span class="icon is-small"><i class="fa fa-angle-right"></i></span>
				</a>
			</div>
		</div>
		<wnl-quiz-question
			:class="`quiz-question-${currentQuestion.id}`"
			:id="currentQuestion.id"
			:answers="currentQuestion.quiz_answers"
			:text="currentQuestion.text"
			:total="currentQuestion.total_hits"
			:showComments="true"
			v-if="currentQuestion"
		></wnl-quiz-question>
		<p class="has-text-centered">
			<a v-if="!currentQuestion.isResolved" class="button is-primary" :disabled="isSubmitDisabled" @click="verify">
				Sprawdź odpowiedź
			</a>
			<a v-else class="button is-primary is-outlined" @click="changeQuestion(1)">
				Następne
			</a>
		</p>
		<div class="other-questions">
			<p class="notification small">
				Możesz wybrać dowolne pytanie z listy klikając na jego tytuł
			</p>
			<wnl-quiz-question
				v-for="question, index in otherQuestions"
				:headerOnly="true"
				:answers="question.quiz_answers"
				:class="`clickable quiz-question-${currentQuestion.id}`"
				:key="index"
				:id="question.id"
				:text="question.text"
				:total="question.total_hits"
				@headerClicked="selectQuestionFromList(index)"
			></wnl-quiz-question>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.other-questions
		border-top: $border-light-gray
		margin-top: $margin-big
		padding-top: $margin-base
</style>

<script>
	import _ from 'lodash'
	import { mapGetters, mapActions } from 'vuex'

	import QuizQuestion from 'js/components/course/screens/quiz/QuizQuestion.vue'
	import { scrollToTop, scrollToElement } from 'js/utils/animations'
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizWidget',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		props: ['readOnly'],
		data() {
			return {
				hasErrors: false,
			}
		},
		computed: {
			...mapGetters('quiz', [
				'getQuestions',
			]),
			currentQuestion() {
				return this.getQuestions[0]
			},
			otherQuestions() {
				return _.tail(this.getQuestions) || []
			},
			lastIndex() {
				return this.getQuestions.length - 1
			},
			hasAnswer() {
				return this.currentQuestion.selectedAnswer !== null
			},
			isSubmitDisabled() {
				return !this.hasAnswer
			},
			displayResults() {
				return this.currentQuestion.isResolved
			},
		},
		methods: {
			...mapActions('quiz', [
				'changeQuestion',
				'resolveQuestion',
				'resetState',
			]),
			verify() {
				if (this.hasAnswer) {
					this.resolveQuestion(this.currentQuestion.id)
				}
			},
			getQuestionElement(resource) {
				return this.$el.getElementsByClassName(`quiz-question-${resource.id}`)[0]
			},
			selectQuestionFromList(index) {
				let fullIndex = index + 1
				this.changeQuestion(fullIndex)
				scrollToTop()
			},
		},
	}
</script>
