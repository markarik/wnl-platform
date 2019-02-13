<template>
	<div class="wnl-active-question">
		<p v-if="!isMobile" class="tip">{{$t('questions.solving.activeQuestionTip')}}</p>
		<div class="active-question-controls">
			<div class="widget-control">
				<a class="small unselectable" @click="previousQuestion()">
					<span class="icon is-small"><i class="fa fa-angle-left"></i></span> Poprzednie
				</a>
			</div>
			<div class="widget-control">
				{{$t('questions.solving.current', {number: questionNumber})}}
				<span class="matched-count">{{allQuestionsCount}}</span>
			</div>
			<div class="widget-control">
				<a class="small unselectable" @click="nextQuestion()">
					Następne <span class="icon is-small"><i class="fa fa-angle-right"></i></span>
				</a>
			</div>
		</div>
		<wnl-quiz-question
			v-if="question"
			:class="`quiz-question-${question.id}`"
			:id="question.id"
			:question="question"
			:showComments="displayResults"
			:getReaction="getReaction"
			:module="module"
			@selectAnswer="selectAnswer"
			@answerDoubleclick="onAnswerDoubleclick"
			@userEvent="proxyUserEvent"
		/>
		<wnl-content-item-classifier-editor
			class="quiz-question__content-item-classifier-editor"
			:content-item-id="question.id"
			:content-item-type="CONTENT_TYPES.QUIZ_QUESTION"
		/>
		<p class="active-question-button has-text-centered">
			<a v-if="!question.isResolved" class="button is-primary" :disabled="!hasAnswer" @click="verify">
				Sprawdź odpowiedź
			</a>
			<a v-else class="button is-primary is-outlined" @click="nextQuestion()">
				Następne
			</a>
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.tip
		color: $color-gray
		font-size: $font-size-minus-2
		margin-bottom: $margin-base

	.active-question-button
		margin-bottom: $margin-big * 6

	.active-question-controls
		display: flex
		font-size: $font-size-base
		justify-content: space-between
		margin-bottom: $margin-base

	.matched-count
		color: $color-green

	.quiz-question__content-item-classifier-editor
		margin-top: -$margin-base
		margin-bottom: $margin-big
</style>

<script>
import _ from 'lodash';
import { mapGetters, mapActions } from 'vuex';

import QuizQuestion from 'js/components/quiz/QuizQuestion.vue';
import { scrollToElement } from 'js/utils/animations';
import emits_events from 'js/mixins/emits-events';
import {KEYS} from 'js/consts/keys';
import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';
import {CONTENT_TYPES} from 'js/consts/contentClassifier';

export default {
	name: 'ActiveQuestion',
	components: {
		'wnl-quiz-question': QuizQuestion,
		WnlContentItemClassifierEditor
	},
	mixins: [emits_events],
	props: {
		allQuestionsCount: {
			default: 0,
			type: Number,
		},
		getReaction: {
			default: () => {},
			type: Function,
		},
		module: {
			type: String,
			default: 'questions'
		},
		question: {
			type: Object,
			default: () => {},
		},
		questionNumber: {
			type: Number,
			default: 0,
		},
	},
	data() {
		return {
			hasErrors: false,
			allowDoubleclick: true,
			timeout: 0,
			CONTENT_TYPES
		};
	},
	computed: {
		...mapGetters(['isMobile']),
		...mapGetters('questions', ['getQuestion']),
		hasAnswer() {
			return _.isNumber(this.question.selectedAnswer);
		},
		selectedAnswerIndex () {
			if (this.hasAnswer) {
				return this.question.selectedAnswer;
			} else {
				return -1;
			}
		},
		isSubmitDisabled() {
			return !this.hasAnswer;
		},
		displayResults() {
			return this.question.isResolved;
		},
	},
	methods: {
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
		nextQuestion() {
			this.$emit('changeQuestion', 1);
			scrollToElement(this.$el, 63);
		},
		onAnswerDoubleclick() {
			this.allowDoubleclick && this.displayResults && this.nextQuestion();
		},
		previousQuestion() {
			this.$emit('changeQuestion', -1);
			scrollToElement(this.$el, 63);
		},
		selectAnswer(data) {
			this.allowDoubleclick = false;
			this.$emit('selectAnswer', data);
			this.timeout = setTimeout(() => {
				this.allowDoubleclick = true;
			}, 500);
		},
		verify() {
			this.hasAnswer && this.$emit('verify', this.question.id);
		},
		keyDown(e) {
			if (e.keyCode === KEYS.arrowLeft) {
				this.previousQuestion();
			}

			if (e.keyCode === KEYS.arrowUp) {
				if(this.question.isResolved) {
					return false;
				}
				if (this.selectedAnswerIndex < 1) {
					this.selectAnswer({id: this.question.id, answer: this.question.answers.length - 1});
				} else {
					this.selectAnswer({id: this.question.id, answer: this.selectedAnswerIndex - 1});
				}
				return false;
			}

			if (e.keyCode === KEYS.arrowRight) {
				this.nextQuestion();
			}

			if (e.keyCode === KEYS.arrowDown) {
				if(this.question.isResolved) {
					return false;
				}
				if (this.selectedAnswerIndex > this.question.answers.length - 2) {
					this.selectAnswer({id: this.question.id, answer: 0});
				} else {
					this.selectAnswer({id: this.question.id, answer: this.selectedAnswerIndex + 1});
				}
				return false;
			}

			if (e.keyCode === KEYS.enter) {
				if (this.question.isResolved) {
					this.nextQuestion();
				} else {
					this.verify();
				}
			}
		},
	},
	mounted() {
		window.addEventListener('keydown', this.keyDown);
	},
	beforeDestroy() {
		window.removeEventListener('keydown', this.keyDown);
	}
};
</script>
