<template>
	<ul>
		<vue-draggable
			:options="{handle: '.structure-node-item__action__drag'}"
			@end="onNodeDrag"
		>
			<wnl-structure-node-item
				v-for="node in nodes"
				:node="node"
				:key="node.id"
			/>
		</vue-draggable>
	</ul>
</template>

<script>
import { mapActions } from 'vuex';

import VueDraggable from 'vuedraggable';
import WnlStructureNodeItem from 'js/admin/components/structure/StructureNodeItem';

export default {
	components: {
		VueDraggable,
		WnlStructureNodeItem
	},
	props: {
		nodes: {
			type: Array,
			required: true
		}
	},
	methods: {
		...mapActions('courseStructure', ['moveNode', 'reorderSiblings']),
		...mapActions(['addAutoDismissableAlert']),
		async submitMove({ direction, ...args }) {
			if (direction === 0) {
				return;
			}

			try {
				await this.moveNode({ direction, ...args });
				this.addAutoDismissableAlert({
					type: 'success',
					text: 'Zapisano!'
				});
			} catch (e) {
				this.addAutoDismissableAlert({
					type: 'error',
					text: 'Nie udało się zapisać zmiany. Odśwież stronę i spróbuj ponownie. Być może Twoje drzewo jest nieaktulane.'
				});
				$wnl.logger.error(e);
				throw (e);
			}
		},
		async onNodeDrag({ newIndex, oldIndex }) {
			const direction = newIndex - oldIndex;
			const node = this.nodes[oldIndex];
			try {
				await this.submitMove({ direction, node });
			} catch (e) {
				await this.reorderSiblings({ direction: oldIndex - newIndex, node });
			}
		},
	}
};
</script>
