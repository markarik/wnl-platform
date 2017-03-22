<template>
	<article class="media wnl-chat-message">
		<figure class="media-left">
			<wnl-avatar :username="user.full_name"></wnl-avatar>
		</figure>
		<div class="media-content">
			<div class="content">
				<p class="wnl-message-meta">
					<strong>{{ user.full_name }}</strong>
					<small>{{ answer.time }}</small>
				</p>
				<p class="wnl-message-content">
					{{ answer.text }}
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
		name: 'Answer',
		props: ['answer', 'questionId', 'lessonId'],
		computed: {
			...mapGetters(['qnaGetUser']),
			user() {
				return this.qnaGetUser(this.lessonId, this.answer.users[0])
			},
		},
		methods: {},
		mounted() {
		},
		components: {}
	}
</script>
