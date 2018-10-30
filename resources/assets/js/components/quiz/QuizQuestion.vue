<template>
	<div class="wnl-quiz-question-container">
		<div class="wnl-quiz-question card"
			:class="{
				'is-correct': displayResults && !isUnanswered && isCorrect,
				'is-incorrect': displayResults && !isUnanswered && !isCorrect,
				'is-unresolved': !displayResults,
				'is-unanswered': isUnanswered,
				'is-large-desktop': isLargeDesktop,
				'is-mobile': isMobile,
			}">
			<header class="quiz-header card-header">
				<div class="quiz-header-top">
					<div class="card-header-title" :class="{'clickable': headerOnly, 'is-short-form': headerOnly}" @click="$emit('headerClicked')">
						<div v-html="question.text"></div>
					</div>
					<div class="card-header-icons">
						<wnl-bookmark
							v-if="reactionState"
							:reactableId="question.id"
							:reactableResource="reactableResource"
							:state="reactionState"
							:module="module"
						></wnl-bookmark>
					</div>
				</div>
			</header>
			<div class="quiz-answers card-content" v-if="!headerOnly">
				<ul>
					<wnl-quiz-answer v-for="(answer, answerIndex) in answers"
						:answer="answer"
						:index="answerIndex"
						:questionId="question.id"
						:totalHits="question.total_hits"
						:key="answerIndex"
						:readOnly="readOnly"
						:isSelected="question.selectedAnswer === answerIndex"
						:answersStats="displayResults && question.answersStats"
						@answerSelected="selectAnswer(answerIndex)"
						@dblclick.native="$emit('answerDoubleclick', {answer: answerIndex})"
					></wnl-quiz-answer>
				</ul>
				<div class="quiz-question-meta">
					<div class="quiz-question-tags">
						<span v-if="displayResults && question.tags">{{$t('questions.question.tags')}}:</span>
						<span v-if="displayResults" v-for="(tag, index) in question.tags"
							class="quiz-question-tag"
							:key="index"
						>
							{{trim(tag.name)}}
						</span>
					</div>
					<div class="quiz-question-id">
						#{{question.id}}
					</div>
				</div>
				<div class="question-edit-link" v-if="isAdmin">
					<a
						class="small"
						target="_blank"
						:href="`/admin/app/quizes/edit/${question.id}`"
					>
						{{$t('questions.question.edit')}}
						<span class="icon is-small">
							<i class="fa fa-pencil"></i>
						</span>
					</a>
				</div>
			</div>
			<div class="card-footer" v-if="!hideComments && ((!headerOnly && displayResults) || showComments)">
				<div v-if="question.explanation" class="card-item relative">
					<header>
						<span class="icon is-small comment-icon"><i class="fa fa-info"></i></span>
						<span v-t="'quiz.annotations.explanation.header'"/>&nbsp;·&nbsp;<a class="secondary-link" @click="toggleExplanation">{{showExplanation ? $t('ui.action.hide') : $t('ui.action.show')}}</a>
					</header>
					<div :class="{'collapsed': !showExplanation}" v-html="explanation"></div>
				</div>
				<div v-if="hasSlides" class="card-item">
					<header @click="toggleSlidesList">
						<span class="icon is-small comment-icon"><i class="fa fa-caret-square-o-right"></i></span>
						{{$t('quiz.annotations.slides.header')}} ({{slides.length}})
						&nbsp;·&nbsp;
						<a class="secondary-link">{{slidesExpanded ? $t('ui.action.hide') : $t('ui.action.show')}}</a>
					</header>
					<a class="slide-list-item" v-if="slidesExpanded" v-for="(slide, index) in slides" :key="index" @click="currentSlideIndex = index">
						{{slideLink(slide)}}
					</a>
					<wnl-slide-preview
							:showModal="show"
							:content="slideContent"
							:slidesCount="hasSlides"
							@closeModal="hideSlidePreview"
							@switchSlide="changeSlide" v-if="slideContent && currentModalSlide.id"
							@userEvent="onRelatedSlideUserEvent"
					>
						<span slot="header">{{slideLink(currentModalSlide)}}</span>
						<wnl-slide-link
							class="button is-primary is-outlined is-small"
							slot="footer"
							:context="currentModalSlide.context"
							:blankPage="blankPage">
								{{$t('quiz.slideModal.goToPrezentation')}}
						</wnl-slide-link>
					</wnl-slide-preview>
				</div>
				<div class="card-item">
					<wnl-comments-list
						commentableResource="quiz_questions"
						urlParam="quiz_question"
						:module="module"
						:commentableId="question.id"
						:isUnique="showComments">
					</wnl-comments-list>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.relative
		position: relative
	.card-item
		border-bottom: 1px solid #dbdbdb
		padding: $margin-small $margin-big $margin-base
		width: 100%

		header
			color: $color-gray-dimmed
			font-size: $font-size-minus-1
			margin-bottom: $margin-base
			margin-top: $margin-base

		.slide-list-item
			font-size: 0.825em
			padding-left: $margin-base

		.collapsed
			height: 1em
			overflow: hidden

		.collapsed:after
			content: ''
			position: absolute
			width: 100%
			height: 2em
			display: block
			left: 0
			bottom: $margin-base
			+gradient-vertical(rgba(255,255,255,1) 0%, rgba(255,255,255,0.1) 100%)


	.card-content ul
		counter-reset: list

	.card-footer
		flex-direction: column

	.quiz-question-icon
		display: block
		font-size: $font-size-minus-3
		padding: $margin-tiny 0
		text-align: center
		text-transform: uppercase

		.icon
			display: block
			margin: 0 auto -0.2em

	.quiz-header
		align-items: flex-start
		flex-direction: column

		.card-header-icons
			display: flex

	.card-header-title.is-short-form
		font-size: $font-size-minus-1

	.quiz-header-top
		display: flex
		width: 100%

	.quiz-question-meta
		+flex-space-between()
		align-items: flex-start
		color: $color-gray-dimmed
		font-size: $font-size-minus-2
		line-height: $line-height-minus
		padding: $margin-base $margin-base 0
		width: 100%

		.quiz-question-tags
			margin-right: $margin-base

			.quiz-question-tag
				display: inline-block
				padding-right: $margin-tiny * 2

	.wnl-quiz-question
		margin-bottom: $margin-huge

		&.is-correct
			box-shadow: 0 2px 3px $color-correct-shadow, 0 0 0 1px $color-correct-shadow

		&.is-incorrect
			box-shadow: 0 2px 3px $color-incorrect-shadow, 0 0 0 1px $color-incorrect-shadow

		.quiz-header,
		.quiz-answers
			padding: $margin-medium

		.card-header-title,
		.card-header-icons
			font-weight: $font-weight-bold
			padding: 0 $margin-medium

		&.is-large-desktop
			.quiz-header,
			.quiz-answers
				padding: $margin-base

				.card-header-title,
				.card-header-icons
					padding: 0 $margin-base

			.quiz-header
				font-size: $font-size-base

			.quiz-answer
				font-size: $font-size-base

		&.is-mobile
			.quiz-question-tags
				margin-right: $margin-small

			.quiz-header,
			.quiz-answers
				padding: $margin-small

				.card-header-title,
				.card-header-icons
					line-height: $line-height-minus
					padding: $margin-small

			.quiz-header
				font-size: $font-size-minus-1

			.quiz-answer
				font-size: $font-size-minus-1

	.question-edit-link
		margin: $margin-medium 0
		text-align: center

		.button
			.icon:first-child
				margin-left: $margin-small


	.has-errors .is-unanswered
		color: $color-orange

</style>
<script>
	import { isNumber, trim } from 'lodash'
	import { mapGetters, mapActions } from 'vuex'
	import { getApiUrl } from 'js/utils/env'

	import QuizAnswer from 'js/components/quiz/QuizAnswer'
	import CommentsList from 'js/components/comments/CommentsList'
	import Bookmark from 'js/components/global/reactions/Bookmark'
	import SlideLink from 'js/components/global/SlideLink'
	import SlidePreview from 'js/components/global/SlidePreview'
	import emits_events from 'js/mixins/emits-events';
	export default {
		name: 'QuizQuestion',
		components: {
			'wnl-quiz-answer': QuizAnswer,
			'wnl-comments-list': CommentsList,
			'wnl-bookmark': Bookmark,
			'wnl-slide-link': SlideLink,
			'wnl-slide-preview': SlidePreview
		},
		mixins: [emits_events],
		props: ['index', 'readOnly', 'headerOnly', 'hideComments', 'showComments', 'question', 'getReaction', 'isQuizComplete', 'module'],
		data() {
			return {
				blankPage: '_blank',
				reactableResource: "quiz_questions",
				slidesExpanded: false,
				showExplanation: false,
				show: false,
				slideContent: '',
				currentSlideIndex: -1,
				alertError: {
					text: this.$i18n.t('quiz.errorAlert'),
					type: 'error',
				}
			}
		},
		computed: {
			...mapGetters(['isMobile', 'isLargeDesktop', 'isAdmin']),
			...mapGetters('course', ['getLesson', 'getSection']),
			answers() {
				return this.question.answers
			},
			displayResults() {
				return this.readOnly || this.isQuizComplete || this.question.isResolved
			},
			isCorrect() {
				const selected = this.question.selectedAnswer
				return isNumber(selected) && this.answers[selected].is_correct
			},
			isUnanswered() {
				return !isNumber(this.question.selectedAnswer)
			},
			reactionState() {
				if (typeof this.getReaction === 'function') {
					return this.getReaction(this.reactableResource, this.question.id, "bookmark")
				}
			},
			watchState() {
				if (typeof this.getReaction === 'function') {
					return this.getReaction(this.reactableResource, this.question.id, "watch")
				}
			},
			slides() {
				return this.question.slides
			},
			hasSlides() {
				return (this.question.slides || []).length
			},
			explanation() {
				return this.question.explanation
			},
			currentModalSlide() {
				if (this.currentSlideIndex < 0) {
					return {id: 0}
				}
				// return 0
				return this.slides[this.currentSlideIndex]
			},
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			hideSlidePreview() {
				this.show = false
				this.slideContent = ''
				this.currentSlideIndex = -1
			},
			changeSlide(direction) {
				let nextSlideIndex = this.currentSlideIndex + direction
				if (nextSlideIndex < 0) {
					nextSlideIndex = this.slides.length -1
				} else if (nextSlideIndex >= this.slides.length) {
					nextSlideIndex = 0
				}
				this.currentSlideIndex = nextSlideIndex
			},
			selectAnswer(answerIndex) {
				const data = {id: this.question.id, answer: answerIndex}
				const eventName = !this.question.isResolved ? 'selectAnswer' : 'resultsClicked'

				this.$emit(eventName, data)
			},
			trim(text) {
				return trim(text)
			},
			toggleSlidesList() {
				this.slidesExpanded = !this.slidesExpanded
			},
			toggleExplanation() {
				this.showExplanation = !this.showExplanation
			},
			slideLink(slide) {
				let linkText = ''

				if (_.get(slide, 'context.lesson.id')) {
					linkText += this.getLesson(slide.context.lesson.id).name

					if (_.get(slide, 'context.section.id')) {
						linkText += ` / ${this.getSection(slide.context.section.id).name}`
					}
				}
				return linkText || this.$t('quiz.annotations.slides.defaultLink')
			},
			onRelatedSlideUserEvent(payload) {
				this.emitUserEvent({
					...payload,
					value: this.question.id,
					target: this.currentModalSlide.id,
					feature_component: 'related_slides'
				})
			}
		},
		watch: {
			'currentModalSlide.id'(slideId) {
				if (!slideId) return
				axios.get(getApiUrl(`slideshow_builder/slide/${slideId}`))
				.then(({data}) => {
					this.slideContent = data
				}).then(() => {
					this.show = true
				}).catch(error => {
					$wnl.logger.capture(error)
					this.addAutoDismissableAlert(this.alertError)
				})
			}
		}
	}
</script>
