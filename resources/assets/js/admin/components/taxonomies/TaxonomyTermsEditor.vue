<template>
	<div class="terms-editor">
		<div class="terms-editor__panel is-left">
			<div class="terms-editor__panel__header">
				<h4 class="title is-5"><strong>Hierarchia pojęć</strong> ({{terms.length}})</h4>
				<span class="control has-icons-right">
					<wnl-term-autocomplete
						@change="onSearchTerm"
						placeholder="Szukaj pojęcia"
					/>
					<span class="icon is-small is-right">
						<i class="fa fa-search"></i>
					</span>
				</span>
			</div>
			<wnl-taxonomy-terms-list v-if="!isLoadingTerms" :terms="rootTerms"/>
			<wnl-text-loader v-else />
		</div>
		<div class="terms-editor__panel is-right">
			<wnl-taxonomy-term-editor-right
				:taxonomyId="taxonomyId"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.terms-editor
		border-top: 1px solid $color-lightest-gray
		display: flex

		&__panel
			flex: 50%

			&.is-left
				border-right: 1px solid $color-lightest-gray
				padding-right: $margin-big

			&.is-right
				padding-left: $margin-big

			&__header
				background-color: $color-white
				display: flex
				justify-content: space-between
				padding-top: $margin-big
				position: sticky
				top: -30px
				z-index: 1
</style>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlTaxonomyTermsList from 'js/admin/components/taxonomies/TaxonomyTermsList';
import WnlTaxonomyTermEditorRight from 'js/admin/components/taxonomies/TaxonomyTermEditorRight';
import WnlTermAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTermAutocomplete';
import scrollToTaxonomyTermMixin from 'js/admin/mixins/scroll-to-taxonomy-term';

export default {
	components: {
		WnlTaxonomyTermsList,
		WnlTaxonomyTermEditorRight,
		WnlTermAutocomplete
	},
	props: {
		taxonomyId: {
			type: [String, Number],
			required: true,
		},
	},
	computed: {
		...mapState('taxonomyTerms', {
			isLoadingTerms: 'isLoading',
			terms: 'terms',
		}),
		...mapGetters('taxonomyTerms', ['getChildrenByParentId']),
		rootTerms() {
			return this.getChildrenByParentId(null);
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', ['setUpNestedSet', 'select', 'expand', 'collapseAll']),
		async onSearchTerm(term) {
			this.collapseAll();
			this.select([term.id]);
			this.expand(term.id);

			this.scrollToTaxonomyTerm(term);
		},
	},
	mixins: [
		scrollToTaxonomyTermMixin,
	],
	async mounted() {
		try {
			this.setUpNestedSet(this.taxonomyId);
		} catch (error) {
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak przy pobieraniu struktury Taksonomii',
				type: 'error'
			});
		}
	},
};
</script>
