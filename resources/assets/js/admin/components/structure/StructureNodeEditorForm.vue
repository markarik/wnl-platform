<template>
	<div>
		<div class="field">
			<label class=" label is-uppercase"><strong>Gałąź nadrzędna</strong></label>
			<span class="info small">Pozostaw puste, aby dodać pojęcie na najwyższym poziomie.</span>
			<wnl-node-autocomplete
				@change="onSelectParent"
				:selected="parent"
			/>
		</div>

		<div class="field">
			<label class="label is-uppercase"><strong>Tag źródłowy</strong></label>
			<span class="info">Wybierz lekcję/grupę, na podstawie której chcesz utworzyć gałąź struktury, lub utwórz nową.</span>
			<wnl-structurable-autocomplete
				@change="onSelectStructurable"
				:selected="structurable"
			/>
		</div>

		<div class="field">
			<div class="has-text-centered">
				<button class="button" @click="onSubmitClick" :disabled="submitDisabled">{{submitLabel}}</button>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.info
		color: $color-gray-dimmed

	.field
		margin-bottom: $margin-big
</style>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlNodeAutocomplete from 'js/admin/components/structure/StructureNodeEditorNodeAutocomplete';
import WnlStructurableAutocomplete from 'js/admin/components/structure/StructureNodeEditorStructurableAutocomplete';
import {ALERT_TYPES} from '../../../consts/alert';

const initialState = {
	id: null,
	parent: null,
	structurable: null,
};

export default {
	props: {
		onSave: {
			type: Function,
			required: true,
		},
		submitLabel: {
			type: String,
			required: true,
		},
		courseId: {
			type: [String, Number],
			required: true,
		},
		node: {
			type: Object,
			default: null,
		},
	},
	data() {
		return {
			...initialState
		};
	},
	components: {
		WnlNodeAutocomplete,
		WnlStructurableAutocomplete,
	},
	computed: {
		...mapState('courseStructure', ['Nodes', 'isSaving']),
		...mapGetters('courseStructure', ['getAncestorsById']),
		submitDisabled() {
			return !this.structurable || this.isSaving;
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onSubmitClick() {
			this.onSave({
				id: this.id,
				parent_id: this.parent ? this.parent.id : null,
				structurable_id: this.structurable.id,
				structurable_type: this.structurable.type,
				course_id: this.courseId,
			});
		},
		onSelectParent(Node) {
			if (Node && this.getAncestorsById(Node.id).find(t => t.id === this.id)) {
				this.addAutoDismissableAlert({
					text: 'Nie możesz przenieść pojęcia do jego potomka.',
					type: ALERT_TYPES.ERROR,
				});
				return;
			}
			this.parent = Node;
			this.$emit('parentChange', Node);
		},

		onSelectStructurable(structurable) {
			this.structurable = structurable;
		},

		onNodeUpdated(Node) {
			if (Node === null) {
				Node = {
					...initialState
				};
			}

			const {id, parent, structurable} = Node;
			this.id = id;
			this.parent = parent;
			this.structurable = structurable;
		}
	},
	watch: {
		Node(Node) {
			this.onNodeUpdated(Node);
		}
	},
	mounted() {
		this.onNodeUpdated(this.node);
	}
};
</script>
