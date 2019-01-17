<template>
	<div class="margin bottom">
		<div v-if="selected" class="autocomplete-selected">
				<span>
					<span v-if="selected.ancestors.length">{{selected.ancestors.map(ancestor => ancestor.tag.name).join(' > ')}} ></span>
					{{selected.tag.name}}
				</span>
			<span class="icon is-small clickable" @click="onSelect(null)"><i class="fa fa-close" aria-hidden="true"></i></span>
		</div>
		<div class="control" v-else>
			<input class="input" v-model="search" placeholder="Wpisz nazwę nadrzędnego pojęcia" />
			<wnl-autocomplete
				:items="autocompleteTerms"
				:onItemChosen="onSelect"
				:isDown="true"
			>
				<template slot-scope="slotProps">
					<div>
						<div class="autocomplete-parent-term">{{slotProps.item.ancestors.map(ancestor => ancestor.tag.name).join(' > ')}}</div>
						<div>{{slotProps.item.tag.name}}</div>
					</div>
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

	.autocomplete-parent-term
		color: $color-inactive-gray

</style>

<script>
import {mapState} from 'vuex';
import {uniqBy} from 'lodash';

import WnlAutocomplete from 'js/components/global/Autocomplete';

export default {
	props: {
		selected: {
			type: Object,
			default: null,
		}
	},
	data() {
		return {
			search: '',
		};
	},
	computed: {
		...mapState('taxonomyTerms', ['terms']),
		autocompleteTerms() {
			if (!this.search) {
				return [];
			}
			const lowerSearch = this.search.toLocaleLowerCase();

			const terms = this.terms.filter(term => term.tag.name.toLocaleLowerCase().startsWith(lowerSearch));
			terms.push(...this.terms.filter(term => term.tag.name.toLocaleLowerCase().includes(lowerSearch)));

			return uniqBy(terms, 'id').slice(0, 25);
		},
	},
	components: {
		WnlAutocomplete
	},
	methods: {
		onSelect(item) {
			this.search = '';
			this.$emit('change', item);
		},
	},
};
</script>
