<template>
	<article class="media wnl-chat-message">
		<figure class="media-left">
			<wnl-avatar :username="user.full_name"></wnl-avatar>
		</figure>
		<div class="media-content">
			<div class="content">
				<p class="wnl-message-meta">
					<strong>{{ user.full_name }}</strong>
					<small>{{ question.time }}</small>
				</p>
				<p class="wnl-message-content">
					{{ question.text }}
				</p>
			</div>
		</div>
	</article>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'


</style>
<script>
	import {mapActions, mapGetters} from 'vuex'

	export default {
		name: 'Question',
		props: ['question', 'lessonId'],
		computed: {
			...mapGetters(['qnaGetUser', 'qnaGetAnswers']),
			user() {
				return this.qnaGetUser(this.lessonId, this.question.users[0])
			},
			answers() {
				return this.qnaGetAnswers(this.lessonId, this.question.answers)
			}
		},
		methods: {},
		mounted() {
			console.log(this.answers)
		},
		components: {}
	}
</script>
