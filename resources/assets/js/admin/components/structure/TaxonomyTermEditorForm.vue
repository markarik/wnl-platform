<template>
	<div>
		<div class="field">
			<label class=" label is-uppercase"><strong>Nadrzędne pojęcie</strong></label>
			<span class="info small">Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii.</span>
			<wnl-term-autocomplete
				@change="onSelectParent"
				:selected="parent"
			/>
		</div>

		<div class="field">
			<label class="label is-uppercase"><strong>Tag źródłowy</strong></label>
			<span class="info">Wybierz tag, na podstawie którego chcesz utworzyć pojęcie, lub utwórz nowy.</span>
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

import WnlTermAutocomplete from 'js/admin/components/structure/TaxonomyTermEditorTermAutocomplete';
import WnlStructurableAutocomplete from 'js/admin/components/structure/TaxonomyTermEditorStructurableAutocomplete';
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
		taxonomyId: {
			type: [String, Number],
			required: true,
		},
		term: {
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
		WnlTermAutocomplete,
		WnlStructurableAutocomplete,
	},
	computed: {
		...mapState('courseStructure', ['terms', 'isSaving']),
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
				course_id: this.taxonomyId,
			});
		},
		onSelectParent(term) {
			if (term && this.getAncestorsById(term.id).find(t => t.id === this.id)) {
				this.addAutoDismissableAlert({
					text: 'Nie możesz przenieść pojęcia do jego potomka.',
					type: ALERT_TYPES.ERROR,
				});
				return;
			}
			this.parent = term;
			this.$emit('parentChange', term);
		},

		onSelectStructurable(structurable) {
			this.structurable = structurable;
		},

		onTermUpdated(term) {
			if (term === null) {
				term = {
					...initialState
				};
			}

			const {id, parent, structurable} = term;
			this.id = id;
			this.parent = parent;
			this.structurable = structurable;
		}
	},
	watch: {
		term(term) {
			this.onTermUpdated(term);
		}
	},
	mounted() {
		this.onTermUpdated(this.term);
	}
};
</script>
