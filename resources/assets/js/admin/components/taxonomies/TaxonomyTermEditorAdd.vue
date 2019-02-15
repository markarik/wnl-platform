<template>
	<div>
		<wnl-taxonomy-term-editor-current-term
			v-if="parent"
			:term="parent"
		>
			Dodajesz pojęcie pod pojęciem:
		</wnl-taxonomy-term-editor-current-term>
		<wnl-nested-set-editor-form
			parent-title="Nadrzędne pojęcie"
			parent-subtitle="Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii"
			title="Tag źródłowy"
			subtitle="Wybierz tag, na podstawie którego chcesz utworzyć pojęcie, lub utwórz nowy."
			submit-label="Dodaj pojęcie"
			:submit-disabled="submitDisabled"
			:on-save="onSave"
			vuex-module="taxonomyTerms"
			@changeNode="onSelectTag"
		>
			<wnl-taxonomy-term-autocomplete
				slot="parentAutocomplete"
				slot-scope="parentAutocomplete"
				:selected="parent"
				@change="parentAutocomplete.validateAndChangeParent"
			></wnl-taxonomy-term-autocomplete>

			<wnl-tag-autocomplete
				slot="autocomplete"
				:selected="tag"
				@change="onSelectTag"
			></wnl-tag-autocomplete>

			<div class="field" slot="extraFields">
				<label class="label is-uppercase"><strong>Notatka</strong></label>
				<span class="info">(Opcjonalnie) Dodaj notatkę niewidoczną dla użytkowników.</span>
				<textarea class="textarea margin bottom" v-model="description" placeholder="Wpisz tekst" />
			</div>
		</wnl-nested-set-editor-form>
	</div>
</template>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlTaxonomyTermEditorCurrentTerm from 'js/admin/components/taxonomies/TaxonomyTermEditorCurrentTerm';
import WnlTaxonomyTermAutocomplete from 'js/components/global/taxonomies/TaxonomyTermAutocomplete';
import WnlTagAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTagAutocomplete';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';
import WnlNestedSetEditorForm from 'js/admin/components/nestedSet/NestedSetEditorForm';

export default {
	mixins: [
		scrollToNodeMixin,
	],
	components: {
		WnlNestedSetEditorForm,
		WnlTaxonomyTermEditorCurrentTerm,
		WnlTaxonomyTermAutocomplete,
		WnlTagAutocomplete
	},
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		},
	},
	data() {
		return {
			tag: null,
			description: ''
		};
	},
	computed: {
		...mapGetters('taxonomyTerms', {termById: 'nodeById'}),
		...mapGetters('taxonomyTerms', ['getAncestorsById']),
		...mapState('taxonomyTerms', {selectedTerms: 'selectedNodes'}),
		parent() {
			if (this.selectedTerms.length === 0) {
				return null;
			}

			return this.termById(this.selectedTerms[0]);
		},
		submitDisabled() {
			return !this.tag || this.isSaving;
		},
	},
	methods: {
		...mapActions('taxonomyTerms', {
			'createTerm': 'create',
		}),
		onSelectTag(tag) {
			this.tag = tag;
		},
		onSave() {
			const term = {
				parent_id: this.parent ? this.parent.id : null,
				tag_id: this.tag.id,
				description: this.description,
				taxonomy_id: this.taxonomyId,
			};

			return this.createTerm(term);
		},
	},
};
</script>
