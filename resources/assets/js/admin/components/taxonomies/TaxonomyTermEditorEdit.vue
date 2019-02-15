<template>
	<div v-if="term">
		<wnl-taxonomy-term-editor-current-term :term="term">
			Edytujesz pojęcie:
		</wnl-taxonomy-term-editor-current-term>
		<wnl-nested-set-editor-form
			parent-title="Nadrzędne pojęcie"
			parent-subtitle="Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii"
			title="Tag źródłowy"
			subtitle="Wybierz tag, na podstawie którego chcesz utworzyć pojęcie, lub utwórz nowy."
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
	<div v-else class="notification is-info">
		<span class="icon">
			<i class="fa fa-info-circle"></i>
		</span>
		Najpierw wybierz pojęcie
	</div>
</template>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

import WnlTaxonomyTermEditorCurrentTerm from 'js/admin/components/taxonomies/TaxonomyTermEditorCurrentTerm';
import WnlTaxonomyTermAutocomplete from 'js/components/global/taxonomies/TaxonomyTermAutocomplete';
import WnlTagAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTagAutocomplete';
import WnlNestedSetEditorForm from 'js/admin/components/nestedSet/NestedSetEditorForm';


export default {
	components: {
		WnlTaxonomyTermEditorCurrentTerm,
		WnlTaxonomyTermAutocomplete,
		WnlTagAutocomplete,
		WnlNestedSetEditorForm
	},
	data() {
		return {
			description: '',
			tag: null,
			parent: null
		};
	},
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		}
	},
	computed: {
		...mapGetters('taxonomyTerms', {
			termById: 'nodeById',
			getAncestorsById: 'getAncestorsById',
		}),
		...mapState('taxonomyTerms', {selectedTerms: 'selectedNodes', isSaving: 'isSaving'}),
		term() {
			if (this.selectedTerms.length === 0) {
				return null;
			}

			// TODO figure out multiple terms selected
			return this.termById(this.selectedTerms[0]);
		},
		submitDisabled() {
			return !this.tag || this.isSaving;
		},
	},
	methods: {
		...mapActions('taxonomyTerms', {
			'updateTerm': 'update',
		}),
		onSave() {
			const term = {
				...this.term,
				taxonomy_id: this.taxonomyId,
				tag_id: this.tag.id,
				description: this.description,
				parent_id: this.parent ? this.parent.id : null,
			};
			return this.updateTerm(term);
		},
		onSelectTag(tag) {
			this.tag = tag;
		},
	},
	created() {
		this.description = this.term.description;
		this.tag = this.term.tag;
		this.parent = this.getAncestorsById(this.term.id).slice(-1)[0];
	},
	watch: {
		term() {
			this.description = this.term.description;
			this.tag = this.term.tag;
			this.parent = this.getAncestorsById(this.term.id).slice(-1)[0];
		}
	}
};
</script>
