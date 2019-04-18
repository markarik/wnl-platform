<template>
	<wnl-nested-set-panel-right :active-mode="editorMode" @setEditorMode="setEditorMode">
		<component
			:is="activeComponent"
			slot="activeView"
			:course-id="courseId"
		/>
	</wnl-nested-set-panel-right>
</template>

<script>
import { mapActions, mapState } from 'vuex';

import WnlStructureNodeEditorAdd from 'js/admin/components/structure/StructureNodeEditorAdd';
import WnlStructureNodeEditorDelete from 'js/admin/components/structure/StructureNodeEditorDelete';
import WnlStructureNodeEditorEdit from 'js/admin/components/structure/StructureNodeEditorEdit';
import { NESTED_SET_EDITOR_MODES } from 'js/consts/nestedSet';
import WnlNestedSetPanelRight from 'js/admin/components/nestedSet/NestedSetPanelRight';

export default {
	components: {
		WnlNestedSetPanelRight
	},
	props: {
		courseId: {
			type: [String, Number],
			required: true,
		}
	},
	data() {
		return {
			viewComponents: {
				[NESTED_SET_EDITOR_MODES.ADD]: WnlStructureNodeEditorAdd,
				[NESTED_SET_EDITOR_MODES.EDIT]: WnlStructureNodeEditorEdit,
				[NESTED_SET_EDITOR_MODES.DELETE]: WnlStructureNodeEditorDelete
			}
		};
	},
	computed: {
		...mapState('courseStructure', ['editorMode']),
		activeComponent() {
			return this.viewComponents[this.editorMode];
		}
	},
	methods: {
		...mapActions('courseStructure', {
			setEditorMode: 'setEditorMode',
		}),
	},
};
</script>
