<template>
	<wnl-nested-set-panel-right :active-mode="editorMode" @setEditorMode="setEditorMode">
		<component slot="activeView" :is="activeComponent" :taxonomy-id="taxonomyId" />
	</wnl-nested-set-panel-right>
</template>

<script>
import {mapActions, mapState} from 'vuex';

import WnlTaxonomyTermEditorAdd from 'js/admin/components/taxonomies/TaxonomyTermEditorAdd';
import WnlTaxonomyTermEditorDelete from 'js/admin/components/taxonomies/TaxonomyTermEditorDelete';
import WnlTaxonomyTermEditorEdit from 'js/admin/components/taxonomies/TaxonomyTermEditorEdit';
import {NESTED_SET_EDITOR_MODES} from 'js/consts/nestedSet';
import WnlNestedSetPanelRight from 'js/admin/components/nestedSet/NestedSetPanelRight';

export default {
	components: {WnlNestedSetPanelRight},
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		}
	},
	data() {
		return {
			viewComponents: {
				[NESTED_SET_EDITOR_MODES.ADD]: WnlTaxonomyTermEditorAdd,
				[NESTED_SET_EDITOR_MODES.EDIT]: WnlTaxonomyTermEditorEdit,
				[NESTED_SET_EDITOR_MODES.DELETE]: WnlTaxonomyTermEditorDelete
			}
		};
	},
	computed: {
		...mapState('taxonomyTerms', ['editorMode']),
		activeComponent() {
			return this.viewComponents[this.editorMode];
		}
	},
	methods: {
		...mapActions('taxonomyTerms', {
			setEditorMode: 'setEditorMode',
		}),
	},
};
</script>
