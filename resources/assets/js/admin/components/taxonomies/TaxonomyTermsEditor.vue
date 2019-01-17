<template>
	<div class="terms-editor">
		<div class="terms-editor__panel is-left">
			<div class="terms-editor__panel__header">
				<h4 class="title is-5"><strong>Hierarchia pojęć</strong></h4>
				<span class="control has-icons-right">
					<input class="input" type="search" placeholder="Filtruj po nazwie..." @input="onFilterChange" :value="filter" />
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
					@moveTerm="onTermMove"
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
		padding-top: $margin-base

		&__panel
			flex: 50%

			&.is-left
				border-right: 1px solid $color-lightest-gray
				padding-right: $margin-base

			&.is-right
				padding-left: $margin-base

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
			return this.filteredTerms
				.filter(term => term.parent_id === null)
				.sort((termA, termB) => termA.orderNumber - termB.orderNumber);
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
		...mapActions('taxonomyTerms', ['fetchTermsByTaxonomy', 'setFilter', 'moveTerm']),
		onFilterChange({target: {value}}) {
			this.setFilter(value);
		},
		onTermMove(term, direction) {
			this.moveTerm({term, direction});
		}
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
