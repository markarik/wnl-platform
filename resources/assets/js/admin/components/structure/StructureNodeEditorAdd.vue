<template>
	<div>
		<wnl-nested-set-editor-form
			parent-title="Gałąź nadrzędna"
			parent-subtitle="Pozostaw puste, aby dodać gałąź na najwyższym poziomie."
			title="Powiązana jednostka struktury"
			subtitle="Wybierz lekcję/grupę, na podstawie której chcesz utworzyć gałąź struktury, lub utwórz nową."
			submit-label="Dodaj"
			vuex-module-name="courseStructure"
			:on-save="onSave"
			:submit-disabled="submitDisabled"
			@changeNode="onSelectStructurable"
			@changeParent="onSelectParent"
		>
			<wnl-structure-node-editor-node-autocomplete
				slot="parent-autocomplete"
				slot-scope="parentAutocomplete"
				:selected="parent"
				@change="parentAutocomplete.validateAndChangeParent"
			/>

			<wnl-structure-node-editor-structurable-autocomplete
				slot="autocomplete"
				:selected="structurable"
				@change="onSelectStructurable"
			/>
		</wnl-nested-set-editor-form>
	</div>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex';

import WnlStructureNodeEditorNodeAutocomplete from 'js/admin/components/structure/StructureNodeEditorNodeAutocomplete';
import WnlStructureNodeEditorStructurableAutocomplete from 'js/admin/components/structure/StructureNodeEditorStructurableAutocomplete';
import WnlNestedSetEditorForm from 'js/admin/components/nestedSet/NestedSetEditorForm';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

export default {
	components: {
		WnlStructureNodeEditorNodeAutocomplete,
		WnlStructureNodeEditorStructurableAutocomplete,
		WnlNestedSetEditorForm
	},
	mixins: [scrollToNodeMixin],
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
		...mapState('courseStructure', ['selectedNodes', 'isSaving']),
		parent() {
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
			'createNode': 'create',
		}),
		...mapActions('courseStructure', ['select', 'expand']),
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
		onSelectParent(parent) {
			if (parent) {
				this.select([parent.id]);
				this.expand(parent.id);
				this.scrollToNode(parent);
			} else {
				this.select([]);
			}
		}
	},
};
</script>
