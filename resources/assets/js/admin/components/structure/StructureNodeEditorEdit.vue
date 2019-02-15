<template>
	<wnl-nested-set-editor-form
		v-if="node"
		parent-title="Gałąź nadrzędna"
		parent-subtitle="Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii"
		title="Powiązana jednostka struktury"
		subtitle="Wybierz lekcję/grupę, na podstawie której chcesz utworzyć gałąź struktury, lub utwórz nową."
		vuex-module="courseStructure"
		:on-save="onSave"
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
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz gałąź struktury
	</div>
</template>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

import WnlNodeAutocomplete from 'js/admin/components/structure/StructureNodeEditorNodeAutocomplete';
import WnlStructurableAutocomplete from 'js/admin/components/structure/StructureNodeEditorStructurableAutocomplete';
import WnlNestedSetEditorForm from 'js/admin/components/nestedSet/NestedSetEditorForm';


export default {
	components: {
		WnlNestedSetEditorForm,
		WnlStructurableAutocomplete,
		WnlNodeAutocomplete
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
		...mapGetters('courseStructure', ['nodeById', 'getAncestorsById']),
		...mapState('courseStructure', ['selectedNodes']),
		node() {
			if (this.selectedNodes.length === 0) {
				return null;
			}

			return this.nodeById(this.selectedNodes[0]);
		}
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
		this.parent = this.getAncestorsById(this.node.id).slice(-1)[0];
		this.structurable = this.node.structurable;
	},
	watch: {
		term() {
			this.structurable = this.node.structurable;
			this.parent = this.getAncestorsById(this.node.id).slice(-1)[0];
		}
	}
};
</script>
