<template>
	<div>
		<div class="field">
			<label class=" label is-uppercase"><strong>Nadrzędne pojęcie</strong></label>
			<span class="info small">Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii.</span>
			<wnl-taxonomy-term-autocomplete
				@change="onSelectParent"
				:selected="parent"
			/>
		</div>

		<div class="field">
			<label class="label is-uppercase"><strong>Tag źródłowy</strong></label>
			<span class="info">Wybierz tag, na podstawie którego chcesz utworzyć pojęcie, lub utwórz nowy.</span>
			<wnl-tag-autocomplete
				@change="onSelectTag"
				:selected="tag"
			/>
		</div>

		<div class="field">
			<label class="label is-uppercase"><strong>Notatka</strong></label>
			<span class="info">(Opcjonalnie) Dodaj notatkę niewidoczną dla użytkowników.</span>
			<textarea class="textarea margin bottom" v-model="description" placeholder="Wpisz tekst" />
			<div class="has-text-centered">
				<button class="button" @click="onSubmitClick" :disabled="submitDisabled">{{submitLabel}}</button>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.info
		color: $color-gray

	.field
		margin-bottom: $margin-big
</style>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlTaxonomyTermAutocomplete from 'js/components/global/taxonomies/TaxonomyTermAutocomplete';
import WnlTagAutocomplete from 'js/admin/components/global/TagAutocomplete';
import {ALERT_TYPES} from 'js/consts/alert';

const initialState = {
	description: '',
	id: null,
	tag: null,
	parent: null,
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
		WnlTaxonomyTermAutocomplete,
		WnlTagAutocomplete
	},
	computed: {
		...mapState('taxonomyTerms', {terms: 'nodes', isSaving: 'isSaving'}),
		...mapGetters('taxonomyTerms', ['getAncestorsById']),
		submitDisabled() {
			return !this.tag || this.isSaving;
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onSubmitClick() {
			this.onSave({
				id: this.id,
				parent_id: this.parent ? this.parent.id : null,
				tag_id: this.tag.id,
				description: this.description,
				taxonomy_id: this.taxonomyId,
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
		onSelectTag(tag) {
			this.tag = tag;
		},
		onTermUpdated(term) {
			if (term === null) {
				term = {
					...initialState
				};
			}

			const {description, id, tag, parent} = term;
			this.description = description;
			this.id = id;
			this.tag = tag;
			this.parent = parent;
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
