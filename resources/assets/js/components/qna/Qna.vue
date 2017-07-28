<template>
	<div>
		<div class="qna-loader" v-if="loading">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div class="wnl-qna" v-if="!loading">
			<div class="wnl-qna-header level">
				<div class="level-left">
					<div>
						<p class="title is-4">
							{{displayedTitle}} ({{howManyQuestions}})
						</p>
						<div class="tags">
							<span v-for="tag, key in tagsFiltered" class="tag is-light" v-text="tag.name"></span>
						</div>
					</div>
				</div>
				<div class="level-right" v-if="!readOnly && tags && tags.length">
					<a class="button is-small" @click="showForm = false" v-if="showForm">
						<span>Ukryj</span>
					</a>
					<a class="button is-small is-primary is-outlined" @click="showForm = true" v-if="!showForm">
						<span>Zadaj pytanie</span>
						<span id="question-icon" class="icon is-small">
							<i class="fa fa-question-circle-o"></i>
						</span>
					</a>
				</div>
			</div>
			<transition name="fade">
				<div class="qna-new-question" v-if="showForm">
					<wnl-new-question :tags="tags" @submitSuccess="showForm = false"></wnl-new-question>
				</div>
			</transition>
			<wnl-qna-sorting v-if="howManyQuestions > 0"></wnl-qna-sorting>
			<wnl-qna-question v-for="question in questionsList"
				:key="question.id"
				:questionId="question.id"
				:readOnly="readOnly"
				:reactionsDisabled="reactionsDisabled"
			></wnl-qna-question>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.wnl-qna
		#question-icon
			margin: $margin-tiny $margin-tiny 0 $margin-small

	.wnl-qna-header
		.title
			margin-bottom: $margin-small

		.tag
			margin-right: $margin-small

	.votes
		flex: 0 auto
		margin-right: $margin-base

	.qna-container
		flex: 1 auto
		overflow: hidden
		word-wrap: break-word

	.qna-meta
		align-items: center
		color: $color-gray-dimmed
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
		color: $color-gray-dimmed
		margin-bottom: $margin-tiny
		margin-top: $margin-base

	.qna-new-question
		margin: $margin-big 0
</style>

<script>
	import {join} from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import QnaSorting from 'js/components/qna/QnaSorting'
	import QnaQuestion from 'js/components/qna/QnaQuestion'
	import NewQuestionForm from 'js/components/qna/NewQuestionForm'

	import * as types from 'js/store/mutations-types'
	import {invisibleTags} from 'js/utils/config'

	export default {
		name: 'Qna',
		components: {
			'wnl-qna-question': QnaQuestion,
			'wnl-new-question': NewQuestionForm,
			'wnl-qna-sorting': QnaSorting,
		},
		props: ['tags', 'ids', 'readOnly', 'title', 'reactionsDisabled'],
		data() {
			return {
				ready: false,
				showForm: false,
				questionsList: []
			}
		},
		computed: {
			...mapGetters('qna', [
				'loading',
				'currentSorting',
				'questions',
				'getSortedQuestions'
			]),
			howManyQuestions() {
				return Object.keys(this.questions).length || 0
			},
			tagsFiltered() {
				if (!this.tags) return [];
				return this.tags.filter(tag => invisibleTags.indexOf(tag.name) === -1)
			},
			displayedTitle() {
				return this.title || 'Pytania i odpowiedzi'
			},
		},
		methods: {
			...mapActions('qna', ['destroyQna']),
		},
		mounted() {
			this.questionsList = this.getSortedQuestions(this.currentSorting, this.questions);
		},
		watch: {
			'currentSorting' (newValue) {
				this.questionsList = this.getSortedQuestions(newValue, this.questions);
			},
			'questions' (newValue) {
				this.questionsList = this.getSortedQuestions(this.currentSorting, newValue);
			}
		},
		beforeDestroy() {
		   this.destroyQna()
		}
	}
</script>
