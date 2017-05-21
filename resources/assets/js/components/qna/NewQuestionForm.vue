<template>
	<wnl-form
		name="QnaNewQuestion" method="post" resourceRoute="qna_questions"
		hideSubmit="true" :attach="attachedData">
		<wnl-bubble name="text">Twoje pytanie</wnl-bubble>
	</wnl-form>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import Form from 'js/components/global/form/Form'
	import Bubble from 'js/components/global/form/Bubble'

	export default {
		name: 'NewQuestionForm',
		components: {
			'wnl-form': Form,
			'wnl-bubble': Bubble,
		},
		computed: {
			attachedData() {
				return {
					lesson_id: this.$route.params.lessonId
				}
			},
		}
	}
</script>
