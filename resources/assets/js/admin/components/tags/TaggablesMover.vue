<template>
	<wnl-form
		:before-submit="beforeSubmit"
		:resource-route="resourceRoute"
		hide-default-submit="true"
		name="TaggableMover"
		method="post"
		@submitSuccess="onSubmitSuccess"
	>
		<wnl-form-text
			name="target_tag_id"
		>ID taga docelowego</wnl-form-text>
		<wnl-submit css-class="button is-primary">
			<slot />
		</wnl-submit>
	</wnl-form>
</template>

<script>
import { mapActions } from 'vuex';
import { Form as WnlForm, Text as WnlFormText, Submit as WnlSubmit } from 'js/components/global/form';

export default {
	name: 'TaggablesMover',
	props: {
		beforeSubmit: {
			type: Function,
			default: () => true,
		},
		sourceTagId: {
			type: [String, Number],
			required: true,
		},
	},
	components: {
		WnlFormText,
		WnlForm,
		WnlSubmit,
	},
	computed: {
		resourceRoute() {
			return `taggables/batch_move/${this.sourceTagId}`;
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onSubmitSuccess({
			taggables_deleted: taggablesDeleted,
			taggables_moved: taggablesMoved
		}) {
			this.addAutoDismissableAlert({
				text: `${taggablesMoved} powiązań zostało przeniesionych do wybranego taga. ${taggablesDeleted} zduplikowanych powiązań zostało usuniętych.`,
				type: 'success',
			});

			this.$emit('taggablesMoved');
		},
	}
};
</script>
