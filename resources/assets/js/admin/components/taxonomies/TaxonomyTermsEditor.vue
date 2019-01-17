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
			<ul v-if="!isLoadingTerms">
				<wnl-taxonomy-term-item
					v-for="term in rootTerms"
					:term="term"
					:key="term.id"
				/>
			</ul>
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
import {mapActions, mapState} from 'vuex';

import WnlTaxonomyTermItem from 'js/admin/components/taxonomies/TaxonomyTermItem';
import WnlTaxonomyTermEditorRight from 'js/admin/components/taxonomies/TaxonomyTermEditorRight';
import WnlTermAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTermAutocomplete';

export default {
	props: {
		taxonomyId: {
			type: String|Number,
			required: true,
		},
	},
	computed: {
		rootTerms() {
			return this.terms.filter(term => term.parent_id === null);
		},
		...mapState('taxonomyTerms', {
			isLoadingTerms: 'isLoading',
			terms: 'terms',
		}),
	},
	components: {
		WnlTaxonomyTermItem,
		WnlTaxonomyTermEditorRight,
		WnlTermAutocomplete
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', ['fetchTermsByTaxonomy', 'select', 'expand', 'collapseAll']),
		onSearchTerm(term) {
			this.collapseAll();
			this.select([term.id]);
			this.expand(term);
		},
	},
	async mounted() {
		try {
			this.fetchTermsByTaxonomy(this.taxonomyId);
		} catch (error) {
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak przy pobieraniu struktury Taksonomii',
				type: 'error'
			});
		}
	},
};
</script>
