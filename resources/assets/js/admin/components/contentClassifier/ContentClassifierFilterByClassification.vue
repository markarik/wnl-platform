<template>
	<form @submit.prevent="onSearch">
		<div class="content-classifier-filter__fields margin bottom">
			<div class="field margin right content-classifier-filter__fields__field">
				<label class="label">Wybierz pojęcia</label>
				<wnl-taxonomy-term-selector
					@change="onTermSelect"
				/>
			</div>

			<div class="field content-classifier-filter__fields__field">
				<label class="label">Wybierz tagi</label>
				<wnl-tag-autocomplete
					placeholder="Zacznij pisać aby wyszukać tag"
					@change="onTagSelect"
				/>
			</div>
		</div>

		<div class="content-classifier-filter__active-filters">
			<h5 class="title is-5">Aktywne filtry</h5>

			<div class="content-classifier-filter__active-filters__list margin bottom" v-if="hasActiveFilters">
				<wnl-taxonomy-term-with-ancestors
					v-for="term in byTaxonomyTermsFilter"
					:term="term"
					:ancestors="term.ancestors"
					:key="term.id"
					class="content-classifier-filter__active-filters__term"
					is-bordered
				>
					<span
						slot="left"
						class="icon is-small margin right clickable"
						@click="onTaxonomyTermDelete(term)"
					>
						<i class="fa fa-times"></i>
					</span>
				</wnl-taxonomy-term-with-ancestors>


				<wnl-tag
					v-for="tag in byTagsFilter"
					:key="tag.id"
					:tag="tag"
				>
					<span
						class="icon is-small margin right clickable"
						@click="onTagDelete(tag)"
					>
						<i class="fa fa-times"></i>
					</span>
				</wnl-tag>
			</div>

			<div class="content-classifier-filter__type-filters">
				<div v-for="(meta, contentType) in contentTypes" :key="contentType" class="field is-grouped content-classifier-filter__type-filters__item">
					<input :id="`type-${contentType}`" type="checkbox" class="checkbox" v-model="activeContentTypesMap[contentType]"/>
					<label class="label" :for="`type-${contentType}`">{{meta.name}}</label>
				</div>
			</div>
		</div>

		<button
			class="button submit is-primary margin top"
			type="submit"
			:disabled="submitDisabled"
		>
			Szukaj
		</button>
	</form>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-classifier-filter
		&__fields
			display: flex
			&__field
				flex-grow: 1

		&__active-filters
			background-color: $color-lightest-gray
			border-radius: $border-radius-small
			padding: $margin-base

			&__list
				align-items: center

			&__term
				background-color: $color-white

		&__type-filters
			display: flex
			&__item
				align-items: center
				display: flex
				margin-bottom: 0!important
				margin-right: $margin-huge
				.label
					padding-left: $margin-medium
</style>

<script>
import { mapGetters} from 'vuex';

import WnlTagAutocomplete from 'js/admin/components/global/TagAutocomplete';
import WnlTaxonomyTermSelector from 'js/components/global/taxonomies/TaxonomyTermSelector';
import WnlTaxonomyTermWithAncestors from 'js/components/global/taxonomies/TaxonomyTermWithAncestors';
import WnlTag from 'js/admin/components/global/Tag';

export default {
	components: {
		WnlTagAutocomplete,
		WnlTag,
		WnlTaxonomyTermSelector,
		WnlTaxonomyTermWithAncestors,
	},
	props: {
		contentTypes: {
			type: Object,
			required: true,
		}
	},
	data() {
		const activeContentTypesMap = Object.keys(this.contentTypes).reduce(
			(collector, contentType) => {
				collector[contentType] = true;
				return collector;
			},
			{}
		);

		return {
			byTagsFilter: [],
			byTaxonomyTermsFilter: [],
			activeContentTypesMap,
		};
	},
	computed: {
		...mapGetters('taxonomyTerms', ['getAncestorsById']),
		...mapGetters('taxonomies', ['taxonomyById']),
		hasActiveFilters() {
			return this.byTaxonomyTermsFilter.length || this.byTagsFilter.length;
		},
		submitDisabled() {
			return !this.hasActiveFilters || !Object.values(this.activeContentTypesMap).includes(true);
		},
	},
	methods: {
		onTagSelect(tag) {
			if (!this.byTagsFilter.find(({id}) => id === tag.id)) {
				this.byTagsFilter.push(tag);
			}
		},
		onTermSelect(term, taxonomyId) {
			if (!this.byTaxonomyTermsFilter.find(({id}) => id === term.id)) {
				this.byTaxonomyTermsFilter.push({
					...term,
					taxonomy: this.taxonomyById(taxonomyId),
					ancestors: this.getAncestorsById(term.id),
				});
			}
		},
		onTagDelete(tag) {
			const index = this.byTagsFilter.findIndex(({id}) => id === tag.id);
			if (index > -1) {
				this.byTagsFilter.splice(index, 1);
			}
		},
		onTaxonomyTermDelete(term) {
			const index = this.byTaxonomyTermsFilter.findIndex(({id}) => id === term.id);
			if (index > -1) {
				this.byTaxonomyTermsFilter.splice(index, 1);
			}
		},
		onSearch() {
			this.$emit('search', {
				byTagsFilter: this.byTagsFilter,
				byTaxonomyTermsFilter: this.byTaxonomyTermsFilter,
				activeContentTypesMap: this.activeContentTypesMap
			});
		}
	},
};
</script>
