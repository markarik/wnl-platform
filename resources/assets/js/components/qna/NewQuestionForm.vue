<template>
	<wnl-form
		class="qna-new-question-form"
		name="QnaNewQuestion"
		method="post"
		resourceRoute="qna_questions"
		:attach="attachedData"
		suppressEnter="true">
		<wnl-quill
			class="margin bottom"
			name="text"
			:options="{ placeholder: 'O co chcesz zapytać?' }"
			:toolbar="toolbar">
		</wnl-quill>
	</wnl-form>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.qna-new-question-form
		.ql-container
			height: auto

		.ql-editor
			font-family: $font-family-sans-serif
			font-weight: $font-weight-bold
			font-size: $font-size-plus-1

			strong
				font-weight: $font-weight-black
</style>

<script>
	import Form from 'js/components/global/form/Form'
	import Quill from 'js/components/global/form/Quill'
	import { fontColors } from 'js/utils/colors'

	export default {
		name: 'NewQuestionForm',
		components: {
			'wnl-form': Form,
			'wnl-quill': Quill,
		},
		computed: {
			attachedData() {
				return {
					lesson_id: this.$route.params.lessonId
				}
			},
			toolbar() {
				return [
					['bold', 'italic', 'underline', 'link'],
					[{ color: fontColors }],
					['clean'],
				]
			}
		}
	}
</script>
