<template>
	<div class="terms-editor">
		<div class="terms-editor__panel is-left">
			<div class="terms-editor__panel__header">
				<h4 class="title is-5"><strong>Hierarchia pojęć</strong></h4>
				<span class="control has-icons-right">
					<input class="input" type="search" placeholder="Filtruj po naziwe..." @input="onFilterChange" :value="filter" />
					<span class="icon is-small is-right">
						<i class="fa fa-filter"></i>
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
		border-top: 2px solid $color-lightest-gray
		display: flex
		padding-top: 20px

		&__panel
			flex: 50%

			&.is-left
				border-right: 2px solid $color-lightest-gray
				padding-right: 20px

			&.is-right
				padding-left: 20px

			&__header
				display: flex
				justify-content: space-between
</style>

<script>
import {mapActions, mapState, mapGetters} from 'vuex';

import WnlTaxonomyTermItem from 'js/admin/components/taxonomies/TaxonomyTermItem';
import WnlTaxonomyTermEditorRight from 'js/admin/components/taxonomies/TaxonomyTermEditorRight';

export default {
	props: {
		taxonomyId: {
			type: String|Number,
			required: true,
		},
	},
	computed: {
		rootTerms() {
			return this.filteredTerms.filter(term => term.parent_id === null);
		},
		...mapState('taxonomyTerms', {
			isLoadingTerms: 'isLoading',
			filter: 'filter'
		}),
		...mapGetters('taxonomyTerms', ['filteredTerms']),
	},
	components: {
		WnlTaxonomyTermItem,
		WnlTaxonomyTermEditorRight
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', ['fetchTermsByTaxonomy', 'setFilter']),
		onFilterChange({target: {value}}) {
			this.setFilter(value);
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
