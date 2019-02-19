<template>
	<div class="term-select">
		<wnl-select
			:options="taxonomiesOptions"
			class="is-marginless"
			v-model="taxonomyId"
			@input="onTaxonomyChange"
		/>
		<wnl-taxonomy-term-autocomplete
			class="margin left term-select__autocomplete"
			placeholder="Zacznij pisać, aby wyszukać pojęcie"
			:isFocused="isFocused"
			:disabled="!taxonomyId"
			@change="onChange"
			@blur="$emit('blur', $event)"
		/>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.term-select
		display: flex

		&__autocomplete
			flex-grow: 1
</style>

<script>
import {mapActions, mapGetters, mapState} from 'vuex';

import {ALERT_TYPES} from 'js/consts/alert';

import WnlSelect from 'js/admin/components/forms/Select';
import WnlTaxonomyTermAutocomplete from 'js/components/global/taxonomies/TaxonomyTermAutocomplete';
import contentClassifierStore from 'js/services/contentClassifierStore';
import {CONTENT_CLASSIFIER_STORE_KEYS} from 'js/services/contentClassifierStore';

export default {
	components: {
		WnlSelect,
		WnlTaxonomyTermAutocomplete,
	},
	props: {
		isFocused: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			taxonomyId: null,
		};
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById', 'getAncestorsById']),
		...mapGetters('taxonomies', ['taxonomyById']),
		...mapState('taxonomies', ['taxonomies']),
		taxonomiesOptions() {
			return this.taxonomies.map(taxonomy => ({value: taxonomy.id, text: taxonomy.name}));
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', ['setUpNestedSet']),
		...mapActions('taxonomies', {
			fetchTaxonomies: 'fetchAll',
		}),
		async onTaxonomyChange(taxonomyId) {
			try {
				await this.setUpNestedSet(taxonomyId);
				contentClassifierStore.set(CONTENT_CLASSIFIER_STORE_KEYS.LAST_TAXONOMY_ID, taxonomyId);
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Coś poszło nie tak przy pobieraniu struktury Taksonomii',
					type: ALERT_TYPES.ERROR
				});
			}
		},
		onChange(term) {
			this.$emit('change', term, this.taxonomyId);
		}
	},
	async mounted() {
		try {
			await this.fetchTaxonomies();

			const lastTaxonomyId = contentClassifierStore.get(CONTENT_CLASSIFIER_STORE_KEYS.LAST_TAXONOMY_ID);

			if (lastTaxonomyId) {
				this.taxonomyId = lastTaxonomyId;
			}
		} catch (error) {
			$wnl.logger.capture(error);
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak przy pobieraniu listy Taksonomii',
				type: ALERT_TYPES.ERROR
			});
		}
	},
};
</script>
