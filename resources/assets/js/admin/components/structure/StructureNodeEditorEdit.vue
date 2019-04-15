<template>
	<wnl-nested-set-editor-form
		v-if="node"
		parent-title="Gałąź nadrzędna"
		parent-subtitle="Pozostaw puste, aby dodać gałąź na najwyższym poziomie."
		title="Powiązana jednostka struktury"
		subtitle="Wybierz lekcję/grupę, na podstawie której chcesz utworzyć gałąź struktury, lub utwórz nową."
		vuex-module-name="courseStructure"
		:on-save="onSave"
		:submit-disabled="submitDisabled"
		@changeParent="parent = $event"
	>
		<wnl-structure-node-editor-node-autocomplete
			slot="parent-autocomplete"
			slot-scope="parentAutocomplete"
			:selected="parent"
			@change="parentAutocomplete.validateAndChangeParent($event, node)"
		></wnl-structure-node-editor-node-autocomplete>

		<wnl-structure-node-editor-structurable-autocomplete
			slot="autocomplete"
			:selected="structurable"
			@change="onSelectStructurable"
		></wnl-structure-node-editor-structurable-autocomplete>
	</wnl-nested-set-editor-form>
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz gałąź struktury
	</div>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex';

import WnlStructureNodeEditorNodeAutocomplete from 'js/admin/components/structure/StructureNodeEditorNodeAutocomplete';
import WnlStructureNodeEditorStructurableAutocomplete from 'js/admin/components/structure/StructureNodeEditorStructurableAutocomplete';
import WnlNestedSetEditorForm from 'js/admin/components/nestedSet/NestedSetEditorForm';


export default {
	components: {
		WnlNestedSetEditorForm,
		WnlStructureNodeEditorStructurableAutocomplete,
		WnlStructureNodeEditorNodeAutocomplete
	},
	props: {
		courseId: {
			type: [String, Number],
			required: true,
		},
	},
	data() {
		return {
			parent: null,
			structurable: null
		};
	},
	computed: {
		...mapGetters('courseStructure', ['nodeById', 'getAncestorNodesById']),
		...mapState('courseStructure', ['selectedNodes', 'isSaving']),
		node() {
			if (this.selectedNodes.length === 0) {
				return null;
			}

			return this.nodeById(this.selectedNodes[0]);
		},
		submitDisabled() {
			return !this.structurable || this.isSaving;
		},
	},
	methods: {
		...mapActions('courseStructure', {
			'updateNode': 'update',
		}),
		onSave() {
			const node = {
				...this.node,
				parent_id: this.parent ? this.parent.id : null,
				structurable_id: this.structurable.id,
				structurable_type: this.structurable.type,
				course_id: this.courseId,
			};
			return this.updateNode(node);
		},
		onSelectStructurable(structurable) {
			this.structurable = structurable;
		},
	},
	created() {
		if (!this.node) return;

		this.parent = this.getAncestorNodesById(this.node.id).slice(-1)[0];
		this.structurable = this.node.structurable;
	},
	watch: {
		node() {
			if (!this.node) return;

			this.structurable = this.node.structurable;
			this.parent = this.getAncestorNodesById(this.node.id).slice(-1)[0];
		}
	}
};
</script>
