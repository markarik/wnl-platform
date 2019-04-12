<template>
	<div>
		<div v-if="selected" class="autocomplete-selected">
			{{selected.name}}
			<span class="icon is-small clickable" @click="onSelect(null)"><i class="fa fa-close" aria-hidden="true"></i></span>
		</div>
		<wnl-autocomplete
			v-else
			v-model="search"
			:items="autocompleteTags"
			@change="onSelect"
			:placeholder="placeholder"
		>
			<div slot-scope="slotProps">
				{{slotProps.item.name}}
			</div>
			<div slot="footer" v-if="autocompleteTags.length === 0 && search !== ''">
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
		</wnl-autocomplete>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.autocomplete-selected
		display: flex
		justify-content: space-between
		padding: $margin-small-minus

	.autocomplete-footer-button-container
		border-top: $border-light-gray
		padding: $margin-base
		text-align: right

</style>

<script>
import { mapState, mapActions } from 'vuex';
import { uniqBy } from 'lodash';

import WnlAutocomplete from 'js/components/global/Autocomplete';
import { ALERT_TYPES } from 'js/consts/alert';

export default {
	props: {
		selected: {
			type: Object,
			default: null,
		},
		placeholder: {
			type: String,
			default: 'Wpisz nazwę tagu, który chcesz dołączyć lub utworzyć'
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
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('tags', {
			fetchAllTags: 'fetchAll',
			createTag: 'create',
		}),
		onSelect(item) {
			this.search = '';
			this.$emit('change', item);
		},
		async onTagAdd() {
			try {
				const tag = await this.createTag(this.search);
				this.search = '';
				this.$emit('change', tag);
			} catch (error) {
				$wnl.logger.capture(error);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error',
				});
			}
		},
	},
	async mounted() {
		try {
			await this.fetchAllTags();
		} catch (error) {
			$wnl.logger.capture(error);

			this.addAutoDismissableAlert({
				text: 'Ups, coś poszło nie tak przy pobieraniu listy dostępnych tagów, spróbuj ponownie.',
				type: ALERT_TYPES.ERROR,
			});
		}

	}
};
</script>
