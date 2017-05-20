<template>
	<div>
		<div v-if="loading">
			Ładuję pytanie
		</div>
		<div class="qna-question" v-else>
			<div class="votes">
				<wnl-vote type="up" count="0"></wnl-vote>
			</div>
			<div class="qna-container">
				<div class="qna-question-content" v-text="content"></div>
				<div class="qna-question-meta qna-meta">
					<wnl-avatar
						:username="authorFullName"
						:url="author.avatar"
						size="medium">
					</wnl-avatar>
					<span class="qna-meta-info">
						{{authorFullName}} ·
					</span>
					<span class="qna-meta-info">
						{{timestamp}}
					</span>
				</div>
				<div class="qna-answers">
					<p class="qna-title">Odpowiedzi ({{answersFromLatest.length}})</p>
					<!-- <wnl-qna-answer v-for="answer in answers" :answer="answer"></wnl-qna-answer> -->
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.qna-question
		border-top: $border-light-gray
		margin: $margin-base 0
		padding: $margin-big 0

	.qna-question-content
		font-weight: $font-weight-bold
		font-size: $font-size-plus-1

	.qna-answers
		margin-top: $margin-big
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import QnaAnswer from 'js/components/qna/QnaAnswer'
	import Vote from 'js/components/qna/Vote'

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
			}
		},
		computed: {
			...mapGetters('qna', [
				'questionContent',
				'questionAuthor',
				'questionTimestamp',
				'questionAnswersFromLatest',
			]),
			id() {
				return this.question.id
			},
			content() {
				return this.questionContent(this.id)
			},
			author() {
				return this.questionAuthor(this.id)
			},
			authorFullName() {
				return `${this.author.first_name} ${this.author.last_name}`
			},
			timestamp() {
				return this.questionTimestamp(this.id)
			},
			answersFromLatest() {
				return this.questionAnswersFromLatest(this.id)
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
