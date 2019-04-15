<template>
	<div>
		<div class="qna-loader" v-if="loading">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div class="wnl-qna" v-if="!loading">
			<div class="wnl-qna-header level">
				<div class="level-left">
					<div>
						<div class="wnl-qna-header" :class="isUserProfileClass">
							<span v-if="icon" class="icon is-big user-profile-icon">
								<i :class="icon"></i>
							</span>
							<p v-if="!hideTitle" class="wnl-qna-header-title" >
								{{displayedTitle}}&nbsp;
							</p>
							<p class="wnl-qna-header-title" v-if="!numbersDisabled">
								({{howManyQuestions}})
							</p>
						</div>
						<div class="tags" v-if="contextTags">
							<span v-for="tag in contextTags" :key="tag.id" class="tag is-light" v-text="tag.name"></span>
						</div>
					</div>
				</div>
				<div class="level-right" v-if="!readOnly">
					<a class="button is-small" @click="showForm = false" v-if="showForm">
						<span>Ukryj</span>
					</a>
					<a class="button is-small is-primary" @click="showForm = true" v-if="!showForm">
						<span>Zadaj pytanie</span>
						<span id="question-icon" class="icon is-small">
							<i class="fa fa-question-circle-o"></i>
						</span>
					</a>
				</div>
			</div>
			<transition name="fade">
				<div class="qna-new-question" v-if="showForm && discussionId">
					<wnl-new-question
						:context-tags="contextTags"
						@submitSuccess="showForm = false"
						:discussion-id="discussionId"
					/>
				</div>
			</transition>
			<wnl-qna-sorting v-if="sortingEnabled"/>
			<div>
				<div v-if="howManyQuestions > 0">
					<wnl-qna-question v-for="question in questionsList"
						:key="question.id"
						:question-id="question.id"
						:read-only="readOnly"
						:reactions-disabled="reactionsDisabled"
						:config="config"
					>
						<router-link v-if="showContext && question.meta && question.meta.context" slot="context" :to="{ name: question.meta.context.name, params: question.meta.context.params }">{{$t('user.userProfile.showContext')}}</router-link>
					</wnl-qna-question>
				</div>
				<div class="qna-no-questions" v-else>
					Ten filtr nie zawiera żadnych pytań.
				</div>
			</div>

		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.wnl-qna
		#question-icon
			margin: 0 $margin-tiny 0 $margin-small

		.title
			margin-bottom: $margin-small

		.tag
			margin-right: $margin-small

	.wnl-qna-header
		display: flex
		margin-bottom: $margin-small
		&.is-user-profile
			.icon
				color: $color-dark-blue-opacity
			.wnl-qna-header-title
				color: $color-dark-blue
		.wnl-qna-header-title
			font-size: $font-size-plus-2

		.user-profile-icon
			margin-right: $margin-small

	.qna-container
		flex: 1 auto
		overflow: hidden
		word-wrap: break-word

	.qna-meta
		align-items: center
		color: $color-gray
		display: flex
		flex-wrap: wrap
		font-size: $font-size-minus-1
		margin-top: $margin-base

	.qna-meta-info
		display: inline-block
		margin-left: $margin-small
		white-space: nowrap

	.qna-question,
	.qna-answer,
	.qna-comment
		display: flex

	.qna-title
		color: $color-gray
		margin-bottom: $margin-tiny
		margin-top: $margin-base

	.qna-new-question
		margin: $margin-big 0

	.qna-no-questions
		background: $color-background-lighter-gray
		border-bottom: $border-light-gray
		padding: $margin-base

</style>

<script>
import { mapActions, mapGetters } from 'vuex';

import QnaSorting from 'js/components/qna/QnaSorting';
import QnaQuestion from 'js/components/qna/QnaQuestion';
import NewQuestionForm from 'js/components/qna/NewQuestionForm';

export default {
	name: 'Qna',
	components: {
		'wnl-qna-question': QnaQuestion,
		'wnl-new-question': NewQuestionForm,
		'wnl-qna-sorting': QnaSorting
	},
	props: {
		hideTitle: {
			type: Boolean,
			default: false,
		},
		icon: String,
		isUserProfileClass: String,
		contextTags: Array,
		numbersDisabled: Boolean,
		passedQuestions: Array,
		reactionsDisabled: Boolean,
		readOnly: Boolean,
		title: [String, Boolean],
		showContext: Boolean,
		sortingEnabled: {
			type: Boolean,
			default: true
		},
		config: {
			type: Object,
			default: () => ({ highlighted: {} })
		},
		discussionId: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			ready: false,
			showForm: false,
			questionsList: [],
			name: 'watch',
		};
	},
	computed: {
		...mapGetters('qna', [
			'loading',
			'currentSorting',
			'questions',
			'getSortedQuestions'
		]),
		howManyQuestions() {
			return Object.keys(this.questionsList).length || 0;
		},
		displayedTitle() {
			return this.title || this.$t('qna.title.titleToDisplay');
		},
	},
	methods: {
		...mapActions('qna', ['destroyQna']),
	},
	mounted() {
		if (!this.sortingEnabled && this.passedQuestions) {
			this.questionsList = this.passedQuestions;
		} else {
			this.questionsList = this.getSortedQuestions(this.currentSorting, this.questions);
		}
	},
	watch: {
		'currentSorting' (newValue) {
			this.questionsList = this.getSortedQuestions(newValue, this.questions);
		},
		'questions' () {
			if (this.sortingEnabled && !this.passedQuestions) {
				this.questionsList = this.getSortedQuestions(this.currentSorting, this.questions);
			}
		}
	},
	beforeDestroy() {
		this.destroyQna();
	}
};
</script>
