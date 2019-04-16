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
			vuex-module-name="taxonomyTerms"
			@changeNode="onSelectTag"
			@changeParent="onSelectParent"
		>
			<wnl-taxonomy-term-autocomplete
				slot="parent-autocomplete"
				slot-scope="parentAutocomplete"
				:selected="parent"
				@change="parentAutocomplete.validateAndChangeParent"
			></wnl-taxonomy-term-autocomplete>

			<wnl-tag-autocomplete
				slot="autocomplete"
				:selected="tag"
				@change="onSelectTag"
			></wnl-tag-autocomplete>

			<div slot="extra-fields" class="field">
				<label class="label is-uppercase"><strong>Notatka</strong></label>
				<span class="info">(Opcjonalnie) Dodaj notatkę niewidoczną dla użytkowników.</span>
				<textarea
					v-model="description"
					class="textarea margin bottom"
					placeholder="Wpisz tekst"
				/>
			</div>
		</wnl-nested-set-editor-form>
	</div>
</template>

<script>
import { mapActions, mapState, mapGetters } from 'vuex';

import WnlTaxonomyTermEditorCurrentTerm from 'js/admin/components/taxonomies/TaxonomyTermEditorCurrentTerm';
import WnlTaxonomyTermAutocomplete from 'js/components/global/taxonomies/TaxonomyTermAutocomplete';
import WnlTagAutocomplete from 'js/admin/components/global/TagAutocomplete';
import WnlNestedSetEditorForm from 'js/admin/components/nestedSet/NestedSetEditorForm';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

export default {
	components: {
		WnlNestedSetEditorForm,
		WnlTaxonomyTermEditorCurrentTerm,
		WnlTaxonomyTermAutocomplete,
		WnlTagAutocomplete
	},
	mixins: [scrollToNodeMixin],
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
		...mapGetters('taxonomyTerms', { termById: 'nodeById' }),
		...mapGetters('taxonomyTerms', ['getAncestorNodesById']),
		...mapState('taxonomyTerms', { selectedTerms: 'selectedNodes', isSaving: 'isSaving' }),
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
			'createTerm': 'create'
		}),
		...mapActions('taxonomyTerms', ['select', 'expand']),
		onSelectTag(tag) {
			this.tag = tag;
		},
		onSelectParent(parent) {
			if (parent) {
				this.select([parent.id]);
				this.expand(parent.id);
				this.scrollToNode(parent);
			} else {
				this.select([]);
			}
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
