<template>
	<div>
		<div v-if="selected" class="autocomplete-selected">
			<span>
				<span v-if="ancestors.length">{{ancestors.map(ancestor => ancestor.structurable.name).join(' > ')}} ></span>
				{{selected.structurable.name}}
			</span>
			<span class="icon is-small clickable" @click="onSelect(null)"><i class="fa fa-close" aria-hidden="true"></i></span>
		</div>
		<wnl-autocomplete
			v-else
			v-model="search"
			:placeholder="placeholder"
			:items="autocompletenodes"
			:onItemChosen="onSelect"
		>
			<template slot-scope="slotProps">
				<div>
					<div class="autocomplete-parent-node">{{getAncestorsById(slotProps.item.id).map(ancestor => ancestor.structurable.name).join(' > ')}}</div>

					<div>
						<span class="icon is-small">
							<i :class="['fa', getStructurableIcon(slotProps.item.structurable)]" aria-hidden="true"></i>
						</span>
						{{slotProps.item.structurable.name}}
					</div>
				</div>
			</template>
		</wnl-autocomplete>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.autocomplete-selected
		display: flex
		justify-content: space-between
		padding: $margin-small-minus

	.autocomplete-parent-node
		color: $color-inactive-gray

	.icon
		margin: 0 $margin-tiny
		padding: $margin-small-minus

</style>

<script>
import {mapState, mapGetters} from 'vuex';
import {uniqBy} from 'lodash';

import WnlAutocomplete from 'js/components/global/Autocomplete';

export default {
	props: {
		selected: {
			type: Object,
			default: null,
		},
		placeholder: {
			type: String,
			default: 'Wpisz nazwę nadrzędnej lekcji/grupy'
		}
	},
	data() {
		return {
			search: '',
		};
	},
	components: {
		WnlAutocomplete
	},
	computed: {
		...mapState('courseStructure', ['nodes']),
		...mapGetters('courseStructure', ['getAncestorsById', 'getStructurableIcon']),
		autocompletenodes() {
			if (!this.search) {
				return [];
			}
			const lowerSearch = this.search.toLocaleLowerCase();

			const nodes = this.nodes.filter(node => node.structurable.name.toLocaleLowerCase().startsWith(lowerSearch));
			nodes.push(...this.nodes.filter(node => node.structurable.name.toLocaleLowerCase().includes(lowerSearch)));

			return uniqBy(nodes, 'id').slice(0, 25);
		},
		ancestors() {
			return this.getAncestorsById(this.selected.id);
		}
	},
	methods: {
		onSelect(item) {
			this.search = '';
			this.$emit('change', item);
		},
	},
};
</script>
