<template>
	<wnl-structure-node-editor-form
		v-if="term"
		submit-label="Zapisz"
		:on-save="onSave"
		:courseId="courseId"
		:term="term"
	/>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz pojęcie
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
		...mapGetters('courseStructure', ['termById', 'getAncestorsById']),
		...mapState('courseStructure', ['selectedTerms']),
		term() {
			if (this.selectedTerms.length === 0) {
				return null;
			}

			// TODO figure out multiple terms selected
			const term = this.termById(this.selectedTerms[0]);

			return {
				...term,
				parent: this.getAncestorsById(term.id).slice(-1)[0],
			};
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('courseStructure', {
			'expandTerm': 'expand',
			'updateTerm': 'update',
		}),
		async onSave(term) {
			try {
				await this.updateTerm(term);

				if (term.parent_id) {
					this.expandTerm(term.parent_id);
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
