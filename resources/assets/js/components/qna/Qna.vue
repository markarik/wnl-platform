<template>
	<div class="wnl-qna">
		<div class="level">
			<div class="level-left">
				<p class="title is-4">
					Pytania i odpowiedzi ({{howManyQuestions}})
				</p>
			</div>
			<div class="level-right">
				<a class="button is-small" @click="showForm = true">
					<span>Zadaj pytanie</span>
					<span id="question-icon" class="icon is-small">
						<i class="fa fa-question-circle-o"></i>
					</span>
				</a>
			</div>
		</div>
		<transition name="slide">
			<div class="qna-new-question" v-if="showForm">
				<wnl-new-question></wnl-new-question>
			</div>
		</transition>
		<div v-if="!loading">
			<wnl-qna-question v-for="question in sortedQuestions"
				:question="question">
			</wnl-qna-question>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.wnl-qna
		margin: $margin-huge 0

		#question-icon
			margin-left: $margin-small
			margin-top: $margin-tiny

	.votes
		flex: 0 auto
		padding: 0 $margin-base

	.qna-container
		flex: 1 auto

	.qna-meta
		align-items: center
		color: $color-gray-dimmed
		display: flex
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
	import { mapActions, mapGetters } from 'vuex'

	import QnaQuestion from 'js/components/qna/QnaQuestion'
	import NewQuestionForm from 'js/components/qna/NewQuestionForm'

	export default {
		name: 'Qna',
		components: {
			'wnl-qna-question': QnaQuestion,
			'wnl-new-question': NewQuestionForm,
		},
		data() {
			return {
				loading: true,
				showForm: false,
			}
		},
		computed: {
			...mapGetters('qna', ['sortedQuestions']),
			howManyQuestions() {
				return this.sortedQuestions.length || '...'
			},
		},
		methods: {
			...mapActions('qna', ['fetchQuestions', 'qnaGetMockData'])
		},
		mounted() {
			this.fetchQuestions()
				.then(() => {
					this.loading = false
				})
		},
	}
</script>
