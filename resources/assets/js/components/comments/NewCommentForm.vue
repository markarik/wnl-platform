<template>
	<wnl-form
		class="qna-new-comment-form margin vertical"
		hideDefaultSubmit="true"
		method="post"
		suppressEnter="true"
		resetAfterSubmit="true"
		resourceRoute="comments"
		:attach="attachedData"
		:name="name"
		:initial-content="newCommentDraft"
		@submitSuccess="onSubmitSuccess">
		<wnl-quill
			class="margin bottom"
			name="text"
			:options="{ placeholder: 'Zacznij swój komentarz...', theme: 'snow' }"
			:value="newCommentDraft"
			@input="updateNewCommentDraft"
		>
		</wnl-quill>

		<div class="level">
			<div class="level-left"></div>
			<div class="level-right">
				<div class="level-item">
					<wnl-submit cssClass="button is-small is-primary">
						Zapisz
					</wnl-submit>
				</div>
			</div>
		</div>
	</wnl-form>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapActions, mapState} from 'vuex';
	import { Form, Quill, Submit } from 'js/components/global/form'

	export default {
		name: 'NewCommentForm',
		components: {
			'wnl-form': Form,
			'wnl-quill': Quill,
			'wnl-submit': Submit,
		},
		props: ['commentableResource', 'commentableId', 'isUnique'],
		computed: {
			...mapState('comments', ['drafts']),
			name() {
				let name = `NewComment-${this.commentableResource}`
				if (!this.isUnique) {
					name = `${name}-${this.commentableId}`
				}
				return name
			},
			attachedData() {
				return {
					commentable_resource: this.commentableResource,
					commentable_id: this.commentableId,
				}
			},
			newCommentDraft() {
				return this.drafts && this.drafts[this.commentableResource]
			}
		},
		methods: {
			...mapActions('comments', ['updateCommentableCommentDraft']),
			onSubmitSuccess(data) {
				this.$emit('submitSuccess', data)
			},
			updateNewCommentDraft(data) {
				_.debounce(() => {
					this.updateCommentableCommentDraft({commentableResource: this.commentableResource, content: data});
				}, 300);
			}
		},
	}
</script>
