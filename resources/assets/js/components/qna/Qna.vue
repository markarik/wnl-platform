<template>
	<div>
		<div class="qna-loader" v-if="!ready">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div class="wnl-qna" v-if="ready">
			<div class="wnl-qna-header level">
				<div class="level-left">
					<div>
						<p class="title is-4">
							Pytania i odpowiedzi ({{howManyQuestions}})
						</p>
						<div class="tags">
							<span v-for="tag, key in tagsFiltered" class="tag is-light" v-text="tag.name"></span>
						</div>
					</div>
				</div>
				<div class="level-right">
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
			<wnl-qna-question v-for="question in sortedQuestions"
				:key="question.id"
				:questionId="question.id">
			</wnl-qna-question>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.qna-loader
		margin-top: $margin-huge

	.wnl-qna
		margin: $margin-huge 0

		#question-icon
			margin: $margin-tiny $margin-tiny 0 $margin-small

	.wnl-qna-header
		.title
			margin-bottom: $margin-small

		.tag
			margin-right: $margin-small

	.votes
		flex: 0 auto
		padding: 0 $margin-base

	.qna-container
		flex: 1 auto

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
	import { mapActions, mapGetters, mapMutations } from 'vuex'

	import QnaQuestion from 'js/components/qna/QnaQuestion'
	import NewQuestionForm from 'js/components/qna/NewQuestionForm'

	import * as types from 'js/store/mutations-types'
	import {invisibleTags} from 'js/utils/config'

	export default {
		name: 'Qna',
		components: {
			'wnl-qna-question': QnaQuestion,
			'wnl-new-question': NewQuestionForm,
		},
		props: ['tags'],
		data() {
			return {
				ready: false,
				showForm: false,
			}
		},
		computed: {
			...mapGetters('qna', ['sortedQuestions', 'loading']),
			howManyQuestions() {
				return this.sortedQuestions.length || 0
			},
			tagsFiltered() {
				return this.tags.filter(tag => invisibleTags.indexOf(tag.name) === -1)
			},
		},
		methods: {
			...mapActions('qna', ['fetchQuestions', 'destroyQna']),
		},
		mounted() {
			this.fetchQuestions(this.tags).then(() => {
				this.ready = true
			})
		},
		beforeDestroy() {
			this.destroyQna()
		},
		watch: {
			'tags' (newValue, oldValue) {
				if (newValue !== oldValue) {
					this.ready = false
					this.destroyQna()
					this.fetchQuestions(newValue).then(() => {
						this.ready = true
					})
				}
			}
		}
	}
</script>
