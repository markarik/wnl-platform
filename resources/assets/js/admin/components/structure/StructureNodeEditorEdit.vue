<template>
	<wnl-structure-node-editor-form
		v-if="currentNode"
		submit-label="Zapisz"
		:on-save="onSave"
		:courseId="courseId"
		:node="currentNode"
	/>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz gałąź struktury
	</div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import WnlStructureNodeEditorForm from 'js/admin/components/structure/StructureNodeEditorForm';

export default {
	components: {
		WnlStructureNodeEditorForm
	},
	props: {
		courseId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapGetters('courseStructure', ['nodeById', 'currentNode']),
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', {
			'expandNode': 'expand',
			'updateNode': 'update',
		}),
		async onSave(node) {
			try {
				await this.updateNode(node);

				if (node.parent_id) {
					this.expandNode(node.parent_id);
				}

				this.addAutoDismissableAlert({
					text: 'Zapisano pojęcie!',
					type: 'success'
				});
			} catch (error) {
				$wnl.logger.capture(error);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error',
				});
			}
		},
	},
};
</script>
