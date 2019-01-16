<template>
	<div>
		<h5 class="title is-5 is-uppercase is-marginless"><strong>Nadrzędne pojęcie</strong></h5>
		<span class="info small">Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii.</span>
		<wnl-term-autocomplete
			@change="onSelectParent"
			:selected="parent"
		/>

		<h5 class="title is-5 is-uppercase is-marginless"><strong>Tag źródłowy</strong></h5>
		<span class="info">Wybierz tag, na podstawie którego chcesz utworzyć pojęcie, lub utwórz nowy.</span>
		<wnl-tag-autocomplete
			@change="onSelectTag"
			:selected="tag"
		/>

		<h5 class="title is-5 is-uppercase is-marginless"><strong>Notatka</strong></h5>
		<span class="info">(Opcjonalnie) Dodaj notatkę niewidoczną dla użytkowników.</span>
		<textarea class="textarea margin bottom" v-model="description" placeholder="Wpisz tekst" />
		<div class="has-text-centered">
			<button class="button" @click="onSubmitClick" :disabled="submitDisabled">{{submitLabel}}</button>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.info
		color: #aaa
</style>

<script>
import {mapActions} from 'vuex';

import WnlTermAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTermAutocomplete';
import WnlTagAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTagAutocomplete';

export default {
	props: {
		onSave: {
			type: Function,
			required: true,
		},
		submitLabel: {
			type: String,
			required: true,
		},
		taxonomyId: {
			type: String|Number,
			required: true,
		},
		tags: {
			type: Array,
			required: true,
		},
		term: {
			type: Object,
		},
		terms: {
			type: Array,
			required: true,
		}
	},
	data() {
		return {
			description: '',
			id: null,
			tag: null,
			parent: null,
		};
	},
	computed: {
		submitDisabled() {
			return this.tag === null;
		},
	},
	components: {
		WnlTermAutocomplete,
		WnlTagAutocomplete
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		async onSubmitClick() {
			try {
				await this.onSave({
					id: this.id,
					parent_id: this.parent ? this.parent.id : null,
					tag_id: this.tag.id,
					description: this.description,
					taxonomy_id: this.taxonomyId
				});

				this.addAutoDismissableAlert({
					text: 'Zapisano!',
					type: 'success'
				});
			} catch (error) {
				$wnl.logger.capture(error);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error',
				});
			}
		},
		onSelectParent(term) {
			this.parent = term;
		},
		onSelectTag(tag) {
			this.tag = tag;
		},
	},
	watch: {
		term({description, id, tag, parent}) {
			this.description = description;
			this.id = id;
			this.tag = tag;
			this.parent = parent;
		}
	},
};
</script>
