<template>
	<wnl-structure-node-editor-form
		submit-label="Dodaj"
		:on-save="onSave"
		:courseId="courseId"
		:node="node"
		@parentChange="onParentChange"
	/>
</template>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlStructureNodeEditorForm from 'js/admin/components/structure/StructureNodeEditorForm';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

export default {
	props: {
		courseId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapGetters('courseStructure', ['nodeById']),
		...mapState('courseStructure', ['selectedNodes']),
		node() {
			if (this.selectedNodes.length === 0) {
				return null;
			}

			return {
				parent: this.nodeById(this.selectedNodes[0])
			};
		}
	},
	components: {
		WnlStructureNodeEditorForm
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', {
			'createNode': 'create',
			'expandNode': 'expand',
			'selectNodes': 'select',
		}),
		async onSave(node) {
			try {
				await this.createNode(node);

				if (node.parent_id) {
					this.expandNode(node.parent_id);
				}

				this.addAutoDismissableAlert({
					text: 'Dodano pojęcie!',
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
		onParentChange(parent) {
			if (parent) {
				this.selectNodes([parent.id]);
				this.expandNode(parent.id);
				this.scrollToNode(parent);
			} else {
				this.selectNodes([]);
			}
		},

	},
	mixins: [
		scrollToNodeMixin,
	],
};
</script>
