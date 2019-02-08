<template>
	<div>
		<div v-if="selected" class="autocomplete-selected">
			<span>
				<span v-if="ancestors.length">{{ancestors.map(ancestor => ancestor.tag.name).join(' > ')}} ></span>
				{{selected.tag.name}}
			</span>
			<span class="icon is-small clickable" @click="onSelect(null)"><i class="fa fa-close" aria-hidden="true"></i></span>
		</div>
		<div class="control" v-else>
			<input
				class="input"
				ref="input"
				v-model="search"
				:disabled="disabled"
				:placeholder="placeholder"
				@keyup.esc="onEscape"
			/>
			<wnl-autocomplete
				:items="autocompleteTerms"
				:onItemChosen="onSelect"
				:isDown="true"
			>
				<template slot-scope="slotProps">
					<wnl-taxonomy-term-with-ancestors
						:term="slotProps.item"
						:ancestors="getAncestorsById(slotProps.item.id)"
					/>
				</template>
			</wnl-autocomplete>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.autocomplete-selected
		display: flex
		justify-content: space-between
		padding: $margin-small-minus

	.autocomplete-parent-term
		color: $color-inactive-gray

</style>

<script>
import {mapState, mapGetters} from 'vuex';
import {uniqBy} from 'lodash';

import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlTaxonomyTermWithAncestors from 'js/components/global/taxonomies/TaxonomyTermWithAncestors';

export default {
	props: {
		disabled: {
			type: Boolean,
			default: false,
		},
		isFocused: {
			type: Boolean,
			default: false,
		},
		placeholder: {
			type: String,
			default: 'Wpisz nazwę nadrzędnego pojęcia',
		},
		selected: {
			type: Object,
			default: null,
		},
	},
	data() {
		return {
			search: '',
		};
	},
	components: {
		WnlAutocomplete,
		WnlTaxonomyTermWithAncestors
	},
	computed: {
		...mapState('taxonomyTerms', {terms: 'nodes'}),
		...mapGetters('taxonomyTerms', ['getAncestorsById']),
		autocompleteTerms() {
			if (!this.search) {
				return [];
			}
			const lowerSearch = this.search.toLocaleLowerCase();

			const terms = this.terms.filter(term => term.tag.name.toLocaleLowerCase().startsWith(lowerSearch));
			terms.push(...this.terms.filter(term => term.tag.name.toLocaleLowerCase().includes(lowerSearch)));

			return uniqBy(terms, 'id').slice(0, 25);
		},
		ancestors() {
			return this.getAncestorsById(this.selected.id);
		}
	},
	methods: {
		onEscape() {
			this.$refs.input.blur();
		},
		onSelect(item) {
			this.search = '';
			this.$emit('change', item);
		},
	},
	watch: {
		isFocused(isFocused) {
			if (isFocused) {
				this.$refs.input.focus();
				this.$emit('focused');
			}
		},
	},
};
</script>
