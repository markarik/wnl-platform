<template>
	<wnl-structure-node-editor-form
		v-if="node"
		submit-label="Zapisz"
		:on-save="onSave"
		:courseId="courseId"
		:node="node"
	/>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz gałąź struktury
	</div>
</template>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';
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
		...mapGetters('courseStructure', ['nodeById', 'getAncestorsById']),
		...mapState('courseStructure', ['selectedNodes']),
		node() {
			if (this.selectedNodes.length === 0) {
				return null;
			}

			const node = this.nodeById(this.selectedNodes[0]);

			return {
				...node,
				parent: this.getAncestorsById(node.id).slice(-1)[0],
			};
		}
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
					text: 'Zapisano!',
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
