<template>
	<div class="wnl-qna-mock">
		<p class="title is-4">Pytania i odpowiedzi</p>
		<div class="qna-questions">
			<div class="qna-question-container" v-for="question in questions">
				<div class="qna-question">
					<div class="qna-question-votes">
						{{question.votes}}
					</div>
					<div class="qna-question-content">
						{{question.text}}
						<div class="qna-question-meta">
							<wnl-avatar
								:username="question.author.username"
								:url="question.author.avatarUrl"
								size="small">
							</wnl-avatar>
							{{question.author.username}}
							{{question.timestamp}}
						</div>
					</div>
				</div>
				<div class="qna-answers">
					<div class="qna-answer" v-for="answer in question.answers">
						<div class="qna-answer-votes">
							{{answer.votes}}
						</div>
						<div class="qna-answer-container">
							<div class="qna-answer-content">
								{{answer.text}}
							</div>
							<div class="qna-answer-meta">
								<wnl-avatar
									:username="answer.author.username"
									:url="answer.author.avatarUrl"
									size="small">
								</wnl-avatar>
								{{answer.author.username}}
								{{answer.timestamp}}
							</div>
							<div class="qna-answer-comments">
								<div class="qna-comment" v-for="comment in answer.comments">
									<div class="qna-comment-votes">
										{{comment.votes}}
									</div>
									<div class="qna-comment-container">
										<div class="qna-comment-content">
											{{comment.text}}
										</div>
										<div class="qna-comment-meta">
											{{comment.author.username}}
											{{comment.timestamp}}
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
</style>

<script>
	import {mapGetters} from 'vuex'

	export default {
		name: 'QnaMock',
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
