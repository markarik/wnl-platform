<template>
	<wnl-nested-set-editor :is-loading="isLoadingTerms">
		<template slot="header">
			<h4 class="title is-5 is-marginless"><strong>Hierarchia pojęć</strong> ({{terms.length}})</h4>
			<span class="terms-editor__search control has-icons-right">
				<wnl-taxonomy-term-autocomplete
					@change="onSearch"
					placeholder="Szukaj pojęcia"
				/>
				<span class="icon is-small is-right">
					<i class="fa fa-search"></i>
				</span>
			</span>
		</template>
		<wnl-taxonomy-terms-list slot="nodes-list" :terms="getRootNodes" />
		<wnl-taxonomy-term-editor-right
			slot="panel-right"
			:taxonomy-id="taxonomyId"
		/>
	</wnl-nested-set-editor>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.terms-editor__search
		flex: 1 0 auto
		margin-left: $margin-big
</style>

<script>
import { mapActions, mapState, mapGetters } from 'vuex';

import WnlNestedSetEditor from 'js/admin/components/nestedSet/NestedSetEditor';
import WnlTaxonomyTermsList from 'js/admin/components/taxonomies/TaxonomyTermsList';
import WnlTaxonomyTermEditorRight from 'js/admin/components/taxonomies/TaxonomyTermEditorRight';
import WnlTaxonomyTermAutocomplete from 'js/components/global/taxonomies/TaxonomyTermAutocomplete';
import scrollToNodeMixin from 'js/admin/mixins/scroll-to-node';

export default {
	components: {
		WnlTaxonomyTermsList,
		WnlTaxonomyTermEditorRight,
		WnlTaxonomyTermAutocomplete,
		WnlNestedSetEditor
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
		...mapGetters('taxonomyTerms', ['getChildrenNodesByParentId', 'getRootNodes']),
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
