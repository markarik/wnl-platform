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
		color: $color-gray-dimmed

	.field
		margin-bottom: $margin-big
</style>

<script>
import {mapActions, mapState} from 'vuex';

import WnlTermAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTermAutocomplete';
import WnlTagAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTagAutocomplete';

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
			type: String|Number,
			required: true,
		},
		term: {
			type: Object,
			default: () => ({}),
		},
	},
	data() {
		return {
			description: '',
			id: null,
			tag: null,
			parent: null,
		};
	},
	computed: {
		...mapState('taxonomyTerms', ['terms', 'isSaving']),
		submitDisabled() {
			return !this.tag || this.isSaving;
		},
	},
	components: {
		WnlTermAutocomplete,
		WnlTagAutocomplete
	},
	methods: {
		onSubmitClick() {
			this.onSave({
				id: this.id,
				parent_id: this.parent ? this.parent.id : null,
				tag_id: this.tag.id,
				description: this.description,
				taxonomy_id: this.taxonomyId
			});
		},
		onSelectParent(term) {
			this.parent = term;
		},
		onSelectTag(tag) {
			this.tag = tag;
		},
		onTermUpdated({description, id, tag, parent}) {
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
