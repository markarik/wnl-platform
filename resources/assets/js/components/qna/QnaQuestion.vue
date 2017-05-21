<template>
	<div>
		<div class="question-loader" v-if="loading">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div class="qna-question" v-else>
			<div class="votes">
				<wnl-vote type="up" count="0"></wnl-vote>
			</div>
			<div class="qna-container">
				<div class="qna-question-content" v-html="content"></div>
				<div class="qna-question-meta qna-meta">
					<wnl-avatar
						:username="author.full_name"
						:url="author.avatar"
						size="medium">
					</wnl-avatar>
					<span class="qna-meta-info">
						{{author.full_name}} ·
					</span>
					<span class="qna-meta-info">
						{{time}}
					</span>
				</div>
				<div class="qna-answers">
					<p class="qna-title">Odpowiedzi ({{answersFromLatest.length}})</p>
					<wnl-qna-answer v-if="hasAnswers" :answer="latestAnswer"></wnl-qna-answer>
					<wnl-qna-answer v-if="allAnswers"
						v-for="answer in otherAnswers"
						:answer="answer">
					</wnl-qna-answer>
					<a class="button is-small is-wide qna-answers-show-all"
						v-if="!allAnswers && otherAnswers.length > 0"
						@click="allAnswers = true">
						Pokaż pozostałe odpowiedzi
					</a>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.question-loader
		border-top: $border-light-gray
		margin: $margin-big
		padding: $margin-big

	.qna-question
		border-top: $border-light-gray
		margin: $margin-base 0
		padding: $margin-big 0

	.qna-question-content
		font-weight: $font-weight-bold
		font-size: $font-size-plus-1

		strong
			font-weight: $font-weight-black

	.qna-answers
		margin-top: $margin-big

	.qna-answers-show-all
		margin-top: $margin-huge
</style>

<script>
	import _ from 'lodash'
	import { mapGetters, mapActions } from 'vuex'

	import QnaAnswer from 'js/components/qna/QnaAnswer'
	import Vote from 'js/components/qna/Vote'

	import { timeFromS } from 'js/utils/time'

	export default {
		name: 'QnaQuestion',
		components: {
			'wnl-vote': Vote,
			'wnl-qna-answer': QnaAnswer,
		},
		props: ['question'],
		data() {
			return {
				loading: true,
				allAnswers: false,
			}
		},
		computed: {
			...mapGetters('qna', [
				'profile',
				'questionAnswersFromLatest',
			]),
			id() {
				return this.question.id
			},
			content() {
				return this.question.text
			},
			author() {
				return this.profile(this.question.profiles[0])
			},
			time() {
				return timeFromS(this.question.created_at)
			},
			answersFromLatest() {
				return this.questionAnswersFromLatest(this.id)
			},
			hasAnswers() {
				return this.answersFromLatest.length > 0
			},
			latestAnswer() {
				return _.head(this.answersFromLatest) || {}
			},
			otherAnswers() {
				return _.tail(this.answersFromLatest) || []
			},
		},
		methods: {
			...mapActions('qna', ['fetchQuestion'])
		},
		mounted() {
			this.fetchQuestion(this.id)
				.then(() => {
					this.loading = false
				})
				.catch((error) => {
					$wnl.logger.error(error)
				})
		}
	}
</script>
