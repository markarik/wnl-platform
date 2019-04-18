<template>
	<div class="collections-quiz">
		<p class="title is-4">Zapisane pytania kontrolne ({{howManyQuestions}})</p>
		<div v-show="isLoaded">
			<wnl-pagination
				v-if="lastPage && lastPage > 1"
				:current-page="currentPage"
				:last-page="lastPage"
				@changePage="changePage"
			/>
			<wnl-quiz-widget
				v-if="isLoaded && howManyQuestions > 0"
				:questions="getQuestionsWithAnswers"
				:get-reaction="getReaction"
				@changeQuestion="performChangeQuestion"
				@verify="trackAndResolve"
				@selectAnswer="onSelectAnswer"
				@userEvent="onUserEvent"
			/>
			<div v-else class="notification has-text-centered">
				W temacie <span class="metadata">{{rootCategoryName}} <span class="icon is-small"><i class="fa fa-angle-right" /></span> {{categoryName}}</span> nie ma jeszcze zapisanych pytań kontrolnych. Możesz łatwo to zmienić klikając na <span class="icon is-small"><i class="fa fa-star-o" /></span> <span class="metadata">ZAPISZ</span> przy wybranym pytaniu!
			</div>
		</div>
		<wnl-text-loader v-if="!isLoaded" />
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
		margin-bottom: 10px
</style>

<script>
import { mapActions, mapGetters, mapState } from 'vuex';

import QuizWidget from 'js/components/quiz/QuizWidget';
import Pagination from 'js/components/global/Pagination';
import emits_events from 'js/mixins/emits-events';
import features from 'js/consts/events_map/features.json';
import feature_components from 'js/consts/events_map/feature_components.json';

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
		...mapGetters('quiz', ['isLoaded', 'getQuestionsWithAnswers', 'getReaction', 'isComplete', 'getQuestion', 'getAnswer']),
		howManyQuestions() {
			return this.quizQuestionsIds.length;
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
		trackAndResolve(id) {
			const question = this.getQuestion(id);
			const answer = this.getAnswer(question.quiz_answers[question.selectedAnswer]);
			this.onUserEvent({
				feature_component: feature_components.quiz_question.value,
				action: feature_components.quiz_question.actions.check_answer.value,
				value: Number(answer.is_correct)
			});

			this.resolveQuestion(id);
		},
		performChangeQuestion(index) {
			this.shuffleAnswers({ id: this.getQuestionsWithAnswers[index].id });
			this.changeQuestion(index);
		},
		onSelectAnswer({ id, answer }) {
			answer === this.getQuestion(id).selectedAnswer
				? this.trackAndResolve(id)
				: !this.isComplete && this.commitSelectAnswer({ id, answer });
		},
		changePage(page) {
			this.$emit('changeQuizQuestionsPage', page);
		},
		onUserEvent(payload) {
			this.emitUserEvent({
				feature: features.quiz_questions.value,
				...payload,
			});
		},
	}
};
</script>
