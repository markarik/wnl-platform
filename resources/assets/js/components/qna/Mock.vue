<template>
	<div class="wnl-qna-mock">
		<p class="title is-4">Pytania i odpowiedzi</p>
		<div class="qna-questions">
			<div class="qna-question" v-for="question in questions">
				<div class="votes">
					<wnl-vote type="up" :count="question.votes"></wnl-vote>
				</div>
				<div class="qna-container">
					<div class="qna-question-content">
						{{question.text}}
					</div>
					<div class="qna-question-meta qna-meta">
						<wnl-avatar
							:username="question.author.username"
							:url="question.author.avatarUrl"
							size="medium">
						</wnl-avatar>
						<span class="qna-meta-info">
							{{question.author.username}} ·
						</span>
						<span class="qna-meta-info">
							{{question.timestamp}}
						</span>
					</div>
					<div class="qna-answers">
						<p class="qna-title">Odpowiedzi ({{question.answers.length}})</p>
						<div v-for="answer in question.answers">
							<div class="qna-answer">
								<div class="votes">
									<wnl-vote type="up" :count="answer.votes"></wnl-vote>
								</div>
								<div class="qna-container">
									<div class="qna-answer-content" v-html="answer.text"></div>
									<div class="qna-meta">
										<wnl-avatar
											:username="answer.author.username"
											:url="answer.author.avatarUrl"
											size="medium">
										</wnl-avatar>
										<span class="qna-meta-info">
											{{answer.author.username}} ·
										</span>
										<span class="qna-meta-info">
											{{answer.timestamp}}
										</span>
									</div>
								</div>
							</div>
							<div class="qna-answer-comments">
								<p class="qna-title">Komentarze ({{answer.comments.length}})</p>
								<div class="qna-comment" v-for="comment in answer.comments">
									<div class="qna-container">
										<div class="qna-meta qna-comment-meta">
											<wnl-avatar
											:username="comment.author.username"
											:url="comment.author.avatarUrl"
											size="small">
											</wnl-avatar>
											<span class="qna-meta-info">
												{{comment.author.username}} ·
											</span>
											<span class="qna-meta-info">
												{{comment.timestamp}}
											</span>
										</div>
										<div class="qna-comment-content">
											{{comment.text}}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-qna-mock
		margin: $margin-huge 0

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

	.qna-question
		border-top: $border-light-gray
		margin: $margin-base 0
		padding: $margin-big 0

	.qna-question-content
		font-weight: $font-weight-bold
		font-size: $font-size-plus-1

	.qna-answers
		margin-top: $margin-big

	.qna-answer
		background: $color-background-lighter-gray
		padding: $margin-base
		margin-top: $margin-base

	.qna-answer-comments
		margin-left: 4em

		.qna-title
			font-size: $font-size-minus-1

	.qna-comment
		border-top: $border-light-gray
		font-size: $font-size-minus-1
		margin-bottom: $margin-base
		padding-top: $margin-base

		&:first-child
			border: 0

	.qna-comment-meta
		font-size: $font-size-minus-2
		margin-top: 0
		margin-bottom: $margin-small
</style>

<script>
	import Vote from 'js/components/qna/Vote.vue'
	import {mapGetters} from 'vuex'

	export default {
		name: 'QnaMock',
		components: {
			'wnl-vote': Vote,
		},
		computed: {
			...mapGetters(['qnaGetMockData']),
			lessonId() {
				return this.$route.params.lessonId
			},
			questions() {
				return this.qnaGetMockData(this.lessonId).questions
			},
		}
	}
</script>
