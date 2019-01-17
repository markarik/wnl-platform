<template>
	<div class="margin bottom">
		<div v-if="selected" class="autocomplete-selected">
			{{selected.name}}
			<span class="icon is-small clickable" @click="onSelect(null)"><i class="fa fa-close" aria-hidden="true"></i></span>
		</div>
		<div class="control" v-else>
			<input class="input" v-model="search" placeholder="Wpisz nazwę tagu, który chcesz dołączyć lub utworzyć" />
			<wnl-autocomplete
				:items="autocompleteTags"
				:onItemChosen="onSelect"
				:isDown="true"
			>
				<template slot-scope="slotProps">
					<div>
						{{slotProps.item.name}}
					</div>
				</template>
				<template slot="footer" v-if="autocompleteTags.length === 0 && search !== ''">
					<div>
						<div class="margin">
							Nie mamy taga <strong>{{search}}</strong>
						</div>
						<div class="autocomplete-footer-button-container">
							<button class="button" @click="onTagAdd">
								<span class="icon is-small">
									<i class="fa fa-plus" aria-hidden="true"></i>
								</span>
								<span>Dodaj nowy tag</span>
							</button>
						</div>
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

	.autocomplete-footer-button-container
		border-top: $border-light-gray
		padding: $margin-base
		text-align: right

</style>

<script>
import {mapState, mapActions} from 'vuex';
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
		...mapState('tags', ['tags']),
		autocompleteTags() {
			if (!this.search) {
				return [];
			}
			const lowerSearch = this.search.toLocaleLowerCase();

			const tags = this.tags.filter(tag => tag.name.toLocaleLowerCase().startsWith(lowerSearch));
			tags.push(...this.tags.filter(tag => tag.name.toLocaleLowerCase().includes(lowerSearch)));

			return uniqBy(tags, 'id').slice(0, 25);
		},
	},
	components: {
		WnlAutocomplete
	},
	methods: {
		...mapActions('tags', {
			fetchAllTags: 'fetchAll',
			createTag: 'create',
		}),
		onSelect(item) {
			this.search = '';
			this.$emit('change', item);
		},
		async onTagAdd() {
			const tag = await this.createTag(this.search);
			this.search = '';
			this.$emit('change', tag);
		},
	},
	mounted() {
		this.fetchAllTags();
	}
};
</script>
