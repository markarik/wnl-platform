<template>
	<wnl-form
		class="qna-new-answer-form"
		hide-default-submit="true"
		method="post"
		suppress-enter="true"
		reset-after-submit="true"
		resource-route="qna_answers"
		:attach="attachedData"
		:name="name"
		@submitSuccess="onSubmitSuccess"
	>
		<wnl-quill
			class="margin bottom"
			name="text"
			:options="{ theme: 'snow', placeholder: 'Zacznij swoją odpowiedź...' }"
			:toolbar="toolbar"
		/>

		<div class="level">
			<div class="level-left" />
			<div class="level-right">
				<div class="level-item">
					<wnl-submit css-class="button is-small is-primary">
						Zapisz
					</wnl-submit>
				</div>
			</div>
		</div>
	</wnl-form>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.qna-new-answer-form
		margin: $margin-base $margin-base $margin-small $margin-base

		.ql-container
			height: auto

		.ql-editor,
		.ql-toolbar
			background: $color-white

		.ql-editor
			font-family: $font-family-sans-serif
			font-size: $font-size-base
			line-height: $line-height-base

			&::before
				font-size: $font-size-minus-1
</style>

<script>
import { Form, Quill, Submit } from 'js/components/global/form';
import { fontColors } from 'js/utils/colors';

export default {
	name: 'NewAnswerForm',
	components: {
		'wnl-form': Form,
		'wnl-quill': Quill,
		'wnl-submit': Submit,
	},
	props: ['questionId'],
	computed: {
		name() {
			return `QnaNewAnswer-${this.questionId}`;
		},
		attachedData() {
			return {
				question_id: this.questionId
			};
		},
		toolbar() {
			return [
				['bold', 'italic', 'underline', 'link'],
				[{ color: fontColors }],
				['clean'],
				[{ list: 'ordered' }, { list: 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
			];
		}
	},
	methods: {
		onSubmitSuccess() {
			this.$emit('submitSuccess');
		},
	},
};
</script>
