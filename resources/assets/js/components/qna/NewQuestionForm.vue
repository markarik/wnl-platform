<template>
	<wnl-form
		class="qna-new-question-form"
		hide-default-submit="true"
		name="QnaNewQuestion"
		method="post"
		suppress-enter="true"
		reset-after-submit="true"
		resource-route="qna_questions"
		:attach="attachedData"
		@submitSuccess="onSubmitSuccess"
	>
		<wnl-quill
			class="margin bottom"
			name="text"
			:options="{ theme: 'snow', placeholder: 'O co chcesz zapytać?' }"
		>
		</wnl-quill>

		<div class="level">
			<div class="level-left"></div>
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

	.qna-new-question-form
		.ql-container
			height: auto

		.ql-editor
			font-family: $font-family-sans-serif
			font-size: $font-size-plus-1

			strong
				font-weight: $font-weight-black

			&::before
				font-weight: $font-weight-regular
				font-size: $font-size-minus-1
				line-height: $line-height-plus
</style>

<script>
import { mapActions } from 'vuex';

import { Form, Quill, Submit } from 'js/components/global/form';

export default {
	name: 'NewQuestionForm',
	components: {
		'wnl-form': Form,
		'wnl-quill': Quill,
		'wnl-submit': Submit,
	},
	props: {
		contextTags: {
			type: Array,
			default: () => []
		},
		discussionId: {
			type: Number,
			required: true
		}
	},
	computed: {
		attachedData() {
			return {
				tags: this.contextTags.map((tag) => tag.id),
				context: {
					name: this.$route.name,
					params: this.$route.params
				},
				discussion_id: this.discussionId
			};
		},
	},
	methods: {
		...mapActions('qna', ['fetchQuestionsForDiscussion']),
		onSubmitSuccess() {
			this.$emit('submitSuccess');
			this.fetchQuestionsForDiscussion({ discussionId: this.discussionId });
		},
	},
	watch: {
		discussionId() {
			this.fetchQuestionsForDiscussion({ discussionId: this.discussionId });
		}
	}
};
</script>
