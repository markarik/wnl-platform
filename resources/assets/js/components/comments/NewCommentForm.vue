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
		:value="newCommentDraft"
		@submitSuccess="onSubmitSuccess">
		<wnl-quill
			class="margin bottom"
			name="text"
			:options="{ placeholder: 'Zacznij swój komentarz...', theme: 'snow' }"
			:value="newCommentDraft"
			@input="setNewCommentDraft"
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
import {mapActions, mapState, mapMutations} from 'vuex';
import { Form, Quill, Submit } from 'js/components/global/form';
import {SET_COMMENTS_COMMENTABLE_COMMENT_DRAFT} from 'js/store/mutations-types';

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
			let name = `NewComment-${this.commentableResource}`;
			if (!this.isUnique) {
				name = `${name}-${this.commentableId}`;
			}
			return name;
		},
		attachedData() {
			return {
				commentable_resource: this.commentableResource,
				commentable_id: this.commentableId,
			};
		},
		newCommentDraft() {
			return this.drafts && this.drafts[this.commentableResource];
		}
	},
	methods: {
		...mapActions('comments', ['updateCommentableCommentDraft']),
		...mapMutations('comments', {
			commitNewCommentDraft: SET_COMMENTS_COMMENTABLE_COMMENT_DRAFT
		}),
		onSubmitSuccess(data) {
			this.$emit('submitSuccess', data);
		},
		setNewCommentDraft: _.debounce(function(data) {
			this.commitNewCommentDraft(
				{ commentableResource: this.commentableResource, content: data }
			);
		}, 300)
	},
};
</script>
