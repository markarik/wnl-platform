<template>
	<div class="term-select">
		<wnl-select
			v-model="taxonomyId"
			:options="taxonomiesOptions"
			class="is-marginless"
			@input="onTaxonomyChange"
		/>
		<wnl-taxonomy-term-autocomplete
			class="margin left term-select__autocomplete"
			placeholder="Zacznij pisać, aby wyszukać pojęcie"
			:is-focused="isFocused"
			:disabled="!taxonomyId"
			:is-down="isDown"
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
import { mapActions, mapGetters, mapState } from 'vuex';

import { ALERT_TYPES } from 'js/consts/alert';

import WnlSelect from 'js/admin/components/forms/Select';
import WnlTaxonomyTermAutocomplete from 'js/components/global/taxonomies/TaxonomyTermAutocomplete';
import { USER_SETTING_NAMES } from 'js/consts/settings';

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
		isDown: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			taxonomyId: null,
		};
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById', 'getAncestorNodesById']),
		...mapGetters('taxonomies', ['taxonomyById']),
		...mapGetters(['getSetting']),
		...mapState('taxonomies', ['taxonomies']),
		taxonomiesOptions() {
			return this.taxonomies.map(taxonomy => ({ value: taxonomy.id, text: taxonomy.name }));
		},
	},
	async mounted() {
		try {
			await this.fetchTaxonomies();

			await this.setupCurrentUser();
			const defaultTaxonomyId = this.getSetting(USER_SETTING_NAMES.DEFAULT_TAXONOMY_ID);

			if (this.taxonomyById(defaultTaxonomyId)) {
				this.taxonomyId = defaultTaxonomyId;
			}
		} catch (error) {
			$wnl.logger.capture(error);
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak przy pobieraniu listy Taksonomii',
				type: ALERT_TYPES.ERROR
			});
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', ['setUpNestedSet']),
		...mapActions(['setupCurrentUser']),
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
};
</script>
