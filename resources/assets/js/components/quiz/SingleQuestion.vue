<template>

	<div class="wnl-app-layout">
		<wnl-questions-navigation />
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container single-question">
				<div v-if="isLoaded">
					<div class="question-header" :class="{'is-mobile': isMobile}">
						<span class="question-title">{{title}}</span>
						<a class="question-back" @click="goBack">
							<span class="icon is-small">
								<i class="fa fa-angle-left"></i>
							</span>
							{{$t('quiz.single.back')}}
						</a>
					</div>
					<div v-if="hasError" class="notification">
						{{$t('quiz.single.error', {id: this.id})}} <wnl-emoji name="disappointed"/>
					</div>
					<wnl-quiz-widget v-else
						:isSingle="true"
						:questions="getQuestionsWithAnswers"
						:getReaction="getReaction"
						@selectAnswer="commitSelectAnswer"
						@verify="resolveQuestion"
						@taxonomyTermAttached="onTaxonomyTermAttached"
					/>
				</div>
				<wnl-text-loader v-else/>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.question-header
		align-items: center
		display: flex
		justify-content: space-between
		margin-bottom: $margin-base

		.question-title
			font-size: $font-size-plus-1
			font-weight: $font-weight-bold

		.question-back

			.is-active
				font-weight: $font-weight-regular

		&.is-mobile
			.question-title
				font-size: $font-size-minus-1
				font-weight: $font-weight-regular

			.question-back
				font-size: $font-size-minus-2

	.single-question
		padding: $margin-base

	.wnl-quiz-widget
		margin-bottom: $margin-humongous
		width: 100%
</style>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex';

import WnlQuestionsNavigation from 'js/components/questions/QuestionsNavigation';
import WnlQuizWidget from 'js/components/quiz/QuizWidget';
import emits_events from 'js/mixins/emits-events';
import { QUIZ_QUESTION_ADD_TERM } from 'js/store/mutations-types';

export default {
	name: 'SingleQuestion',
	components: {
		WnlQuizWidget,
		WnlQuestionsNavigation
	},
	mixins: [emits_events],
	props: {
		quizQuestionId: {
			required: true,
			type: [String, Number],
		}
	},
	data() {
		return {
			hasError: false,
		};
	},
	computed: {
		...mapGetters(['isSidenavVisible', 'isSidenavMounted', 'isMobile']),
		...mapGetters('quiz', ['isLoaded', 'getQuestionsWithAnswers', 'getReaction']),
		title() {
			return this.hasError ? this.$t('quiz.single.errorTitle') : this.$t('quiz.single.title', {id: this.quizQuestionId});
		},
	},
	methods: {
		...mapActions('quiz', ['destroyQuiz', 'fetchSingleQuestion', 'commitSelectAnswer', 'resolveQuestion']),
		...mapMutations('quiz', {
			addTerm: QUIZ_QUESTION_ADD_TERM
		}),
		goBack() {
			this.$router.go(-1);
		},
		setupQuestion() {
			if (!this.quizQuestionId) {
				this.hasError = true;
				return;
			}
			this.fetchSingleQuestion(this.quizQuestionId)
				.then(response => {
					if (!response.data) this.hasError = true;
				})
				.catch(error => {
					this.hasError = true;
				});
		},
		onTaxonomyTermAttached(term) {
			this.addTerm({quizQuestion: this.getQuestionsWithAnswers[0], term});
		}
	},
	created() {
		this.destroyQuiz();
	},
	beforeRouteEnter(to, from, next) {
		return next();
	},
	mounted() {
		this.setupQuestion();
	},
	beforeDestroy() {
		this.destroyQuiz();
	},
	watch: {
		quizQuestionId(to) {
			!!to && this.setupQuestion();
		}
	}
};
</script>
