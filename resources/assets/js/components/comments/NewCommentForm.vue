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
		@submitSuccess="onSubmitSuccess">
		<wnl-quill
			class="margin bottom"
			name="text"
			:options="{ placeholder: 'Zacznij swój komentarz...', theme: 'bubble' }"
			:toolbar="toolbar">
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
			toolbar() {
				return [
					['bold', 'italic', 'underline', 'link'],
					['clean'],
				]
			}
		},
		methods: {
			onSubmitSuccess(data) {
				this.$emit('submitSuccess', data)
			}
		},
	}
</script>
