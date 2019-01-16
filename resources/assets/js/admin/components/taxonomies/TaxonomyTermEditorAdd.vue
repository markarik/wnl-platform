<template>
	<div>
		<h5 class="title is-5 is-uppercase is-marginless"><strong>Nadrzędne pojęcie</strong></h5>
		<span class="info small">Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii.</span>
		<div class="margin bottom">
			<div v-if="parent" class="autocomplete-selected">
					<span>
						{{parent.ancestors.map(ancestor => ancestor.tag.name).join(' > ')}} >
						{{parent.tag.name}}
					</span>
				<span class="icon is-small clickable" @click="parent=null"><i class="fa fa-close" aria-hidden="true"></i></span>
			</div>
			<div class="control" v-else>
				<input class="input" v-model="parentSearch" placeholder="Wpisz nazwę nadrzędnego pojęcia" />
				<wnl-autocomplete
					:items="autocompleteTerms"
					:onItemChosen="onSelectParent"
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

		<h5 class="title is-5 is-uppercase is-marginless"><strong>Tag źródłowy</strong></h5>
		<span class="info">Wybierz tag, na podstawie którego chcesz utworzyć pojęcie, lub utwórz nowy.</span>
		<div class="margin bottom">
			<div v-if="tag" class="autocomplete-selected">
				{{tag.name}}
				<span class="icon is-small clickable" @click="tag=null"><i class="fa fa-close" aria-hidden="true"></i></span>
			</div>
			<div class="control" v-else>
				<input class="input" v-model="tagSearch" placeholder="Wpisz nazwę tagu, który chcesz dołączyć lub utworzyć" />
				<wnl-autocomplete
					:items="autocompleteTags"
					:onItemChosen="onSelectTag"
				>
					<template slot-scope="slotProps">
						<div>
							{{slotProps.item.name}}
						</div>
					</template>
				</wnl-autocomplete>
			</div>
		</div>
		<h5 class="title is-5 is-uppercase is-marginless"><strong>Notatka</strong></h5>
		<span class="info">(Opcjonalnie) Dodaj notatkę niewidoczną dla użytkowników.</span>
		<textarea class="textarea margin bottom" v-model="description" placeholder="Wpisz tekst" />
		<button class="button" @click="onSave">Zapisz</button>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.info
		color: #aaa

	.autocomplete-selected
		display: flex
		justify-content: space-between

	.autocomplete-parent-term
		color: $color-inactive-gray

</style>

<script>
import {mapActions, mapState} from 'vuex';

import WnlAutocomplete from 'js/components/global/Autocomplete';

export default {
	props: {
	},
	data() {
		return {
			description: '',
			tag: null,
			tagSearch: '',
			taxonomy_id: 1,
			parent: null,
			parentSearch: '',
		};
	},
	computed: {
		...mapState('taxonomyTerms', ['selectedTerms', 'terms']),
		...mapState('tags', ['tags']),
		autocompleteTerms() {
			if (!this.parentSearch) {
				return [];
			}
			return this.terms.filter(term => term.tag.name.toLocaleLowerCase().includes(this.parentSearch.toLocaleLowerCase())).slice(0, 10);
		},
		autocompleteTags() {
			if (!this.tagSearch) {
				return [];
			}
			return this.tags.filter(tag => tag.name.toLocaleLowerCase().includes(this.tagSearch.toLocaleLowerCase())).slice(0, 10);
			// TODO add tag
		}
	},
	components: {
		WnlAutocomplete
	},
	methods: {
		...mapActions('taxonomyTerms', {
			'createTerm': 'create',
		}),
		...mapActions('tags', {
			fetchAllTags: 'fetchAll'
		}),
		async onSave() {
			await this.createTerm({
				parent_id: this.parent ? this.parent.id : null,
				tag_id: this.tag.id,
				taxonomy_id: this.taxonomy_id,
				description: this.description,
			});

			this.tag = null;
			this.parent = null;
		},
		onSelectParent(term) {
			this.parent = term;
			this.parentSearch = '';
		},
		onSelectTag(tag) {
			this.tag = tag;
			this.tagSearch = '';
		}
	},
	mounted() {
		this.fetchAllTags();
	}
};
</script>
