<template>
	<div>
		<wnl-nested-set-editor-form
			parent-title="Gałąź nadrzędna"
			parent-subtitle="Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii"
			title="Powiązana jednostka struktury"
			subtitle="Wybierz lekcję/grupę, na podstawie której chcesz utworzyć gałąź struktury, lub utwórz nową."
			vuex-module="courseStructure"
			:on-save="onSave"
			@changeNode="onSelectStructurable"
		>
			<wnl-node-autocomplete
				slot="parentAutocomplete"
				slot-scope="parentAutocomplete"
				:selected="parent"
				@change="parentAutocomplete.validateAndChangeParent"
			></wnl-node-autocomplete>

			<wnl-structurable-autocomplete
				slot="autocomplete"
				:selected="structurable"
				@change="onSelectStructurable"
			></wnl-structurable-autocomplete>
		</wnl-nested-set-editor-form>
	</div>
</template>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

import WnlNodeAutocomplete from 'js/admin/components/structure/StructureNodeEditorNodeAutocomplete';
import WnlStructurableAutocomplete from 'js/admin/components/structure/StructureNodeEditorStructurableAutocomplete';
import WnlNestedSetEditorForm from 'js/admin/components/nestedSet/NestedSetEditorForm';

export default {
	components: {
		WnlNodeAutocomplete,
		WnlStructurableAutocomplete,
		WnlNestedSetEditorForm
	},
	props: {
		courseId: {
			type: [String, Number],
			required: true,
		},
	},
	data() {
		return {
			structurable: null
		};
	},
	computed: {
		...mapGetters('courseStructure', ['nodeById']),
		...mapState('courseStructure', ['selectedNodes']),
		parent() {
			if (this.selectedNodes.length === 0) {
				return null;
			}

			return this.nodeById(this.selectedNodes[0]);
		}
	},
	methods: {
		...mapActions('courseStructure', {
			'createNode': 'create',
		}),
		onSave() {
			const node = {
				parent_id: this.parent ? this.parent.id : null,
				structurable_id: this.structurable.id,
				structurable_type: this.structurable.type,
				course_id: this.courseId,
			};

			return this.createNode(node);
		},
		onSelectStructurable(structurable) {
			this.structurable = structurable;
		},
	},
};
</script>
