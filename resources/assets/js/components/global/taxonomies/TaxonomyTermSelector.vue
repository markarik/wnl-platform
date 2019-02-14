<template>
	<div class="term-select">
		<wnl-select
			:options="taxonomiesOptions"
			class="is-marginless"
			v-model="taxonomyId"
			@input="onTaxonomyChange"
		/>
		<wnl-taxonomy-term-autocomplete
			placeholder="Zacznij pisać, aby wyszukać pojęcie"
			:disabled="!taxonomyId"
			@change="onChange"
			class="margin left term-select__autocomplete"
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

export default {
	components: {
		WnlSelect,
		WnlTaxonomyTermAutocomplete,
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
