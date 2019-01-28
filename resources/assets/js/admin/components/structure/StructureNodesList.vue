<template>
	<ul>
		<vue-draggable
			:options="{handle: '.structure-node-item__action--drag'}"
			@end="onTermDrag"
		>
			<wnl-structure-node-item
				v-for="term in terms"
				:term="term"
				:key="term.id"
				@moveTerm="onChildTermArrowMove"
			/>
		</vue-draggable>
	</ul>
</template>

<script>
import {mapActions} from 'vuex';

import VueDraggable from 'vuedraggable';
import WnlStructureNodeItem from 'js/admin/components/structure/StructureNodeItem';

export default {
	components: {
		VueDraggable,
		WnlStructureNodeItem
	},
	props: {
		terms: {
			type: Array,
			required: true
		}
	},
	methods: {
		...mapActions('courseStructure', ['moveTerm', 'reorderSiblings']),
		...mapActions(['addAutoDismissableAlert']),
		async submitMove({direction, ...args}) {
			if (direction === 0) {
				return;
			}

			try {
				await this.moveTerm({direction, ...args});
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
		async onTermDrag({newIndex, oldIndex}) {
			const direction = newIndex - oldIndex;
			const term = this.terms[oldIndex];
			try {
				await this.submitMove({direction, term});
			} catch (e) {
				await this.reorderSiblings({direction: oldIndex - newIndex, term});
			}
		},
		async onChildTermArrowMove({term, direction}) {
			try {
				await this.submitMove({direction, term});
			} catch (e) {
				await this.reorderSiblings({direction: -direction, term});
			}
		},
	}
};
</script>
