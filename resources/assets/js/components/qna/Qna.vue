<template>
	<div class="wnl-qna">
		<div v-if="hasQuestions">
			<wnl-message v-for="(message, index) in messages"
						 :showAuthor="isAuthorUnique[index]"
						 :username="message.username"
						 :time="message.time">
				{{ message.content }}
			</wnl-message>
		</div>
		<div v-else>
			Nie ma jeszcze pytań do tego tematu. Zadaj pytanie...
		</div>

		<div class="wnl-chat-form">
			<wnl-qna-form></wnl-qna-form>
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.wnl-qna
		display: flex
		flex: 1
		flex-direction: column
		justify-content: flex-end
		padding-right: 20px
		padding-top: 20px

</style>
<script>
	import Form from './Form.vue'
	import Question from './Question.vue'

	export default {
		name: 'Qna',
		props: [],
		data() {
			return {
				questions: {}
			}
		},
		components: {
			'wnl-qna-form': Form,
			'wnl-question': Question
		},
		computed: {
			hasQuestions() {
				return false
			}
		},
		methods: {},
		mounted() {

		}
	}

</script>
