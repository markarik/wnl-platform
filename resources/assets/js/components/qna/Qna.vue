<template>
	<div>
		<div v-if="loading" class="qna-loader">
			<wnl-text-loader />
		</div>
		<div v-if="!loading" class="wnl-qna">
			<div class="wnl-qna-header level">
				<div class="level-left">
					<div>
						<div class="wnl-qna-header" :class="isUserProfileClass">
							<span v-if="icon" class="icon is-big user-profile-icon">
								<i :class="icon" />
							</span>
							<p class="wnl-qna-header-title">
								{{displayedTitle}}&nbsp;
							</p>
							<p v-if="!numbersDisabled" class="wnl-qna-header-title">
								({{howManyQuestions}})
							</p>
						</div>
						<div v-if="contextTags" class="tags">
							<span
								v-for="tag in contextTags"
								:key="tag.id"
								class="tag is-light"
								v-text="tag.name"
							/>
						</div>
					</div>
				</div>
				<div v-if="!readOnly" class="level-right">
					<a
						v-if="showForm"
						class="button is-small"
						@click="showForm = false"
					>
						<span>Ukryj</span>
					</a>
					<a
						v-if="!showForm"
						class="button is-small is-primary"
						@click="showForm = true"
					>
						<span>Zadaj pytanie</span>
						<span id="question-icon" class="icon is-small">
							<i class="fa fa-question-circle-o" />
						</span>
					</a>
				</div>
			</div>
			<transition name="fade">
				<div v-if="showForm && discussionId" class="qna-new-question">
					<wnl-new-question
						:context-tags="contextTags"
						:discussion-id="discussionId"
						@submitSuccess="showForm = false"
					/>
				</div>
			</transition>
			<wnl-qna-sorting v-if="sortingEnabled" />
			<div>
				<div v-if="howManyQuestions > 0">
					<wnl-qna-question
						v-for="question in questionsList"
						:key="question.id"
						:question-id="question.id"
						:read-only="readOnly"
						:reactions-disabled="reactionsDisabled"
						:config="config"
					>
						<router-link
							v-if="showContext && question.meta && question.meta.context"
							slot="context"
							:to="{ name: question.meta.context.name, params: question.meta.context.params }"
						>{{$t('user.userProfile.showContext')}}</router-link>
					</wnl-qna-question>
				</div>
				<div v-else class="qna-no-questions">
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
		/*align-items: center*/
		color: $color-gray
		display: flex
		flex-wrap: wrap
		font-size: $font-size-minus-1
		margin-top: $margin-base

		.modal-activator
			display: inline

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
	mounted() {
		if (!this.sortingEnabled && this.passedQuestions) {
			this.questionsList = this.passedQuestions;
		} else {
			this.questionsList = this.getSortedQuestions(this.currentSorting, this.questions);
		}
	},
	beforeDestroy() {
		this.destroyQna();
	},
	methods: {
		...mapActions('qna', ['destroyQna']),
	},
};
</script>
