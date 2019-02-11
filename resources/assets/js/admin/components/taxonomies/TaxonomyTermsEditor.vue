<template>
	<div class="terms-editor">
		<div class="terms-editor__panel is-left">
			<div class="terms-editor__panel__header">
				<h4 class="title is-5"><strong>Hierarchia pojęć</strong> ({{terms.length}})</h4>
				<span class="control has-icons-right">
					<wnl-taxonomy-term-autocomplete
						@change="onSearch"
						placeholder="Szukaj pojęcia"
					/>
					<span class="icon is-small is-right">
						<i class="fa fa-search"></i>
					</span>
				</span>
			</div>
			<wnl-taxonomy-terms-list v-if="!isLoadingTerms" :terms="getRootNodes"/>
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
import WnlTaxonomyTermAutocomplete from 'js/components/global/taxonomies/TaxonomyTermAutocomplete';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

export default {
	components: {
		WnlTaxonomyTermsList,
		WnlTaxonomyTermEditorRight,
		WnlTaxonomyTermAutocomplete
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
			terms: 'nodes',
		}),
		...mapGetters('taxonomyTerms', ['getChildrenByParentId', 'getRootNodes']),
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', ['setUpNestedSet', 'focus']),
		async onSearch(term) {
			this.focus(term);
			this.scrollToNode(term);
		},
	},
	mixins: [
		scrollToNodeMixin,
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
