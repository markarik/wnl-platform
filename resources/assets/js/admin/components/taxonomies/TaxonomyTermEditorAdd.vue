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
			<button class="button" @click="onSave" :disabled="submitDisabled">Dodaj pojęcie</button>
		</div>
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
import {mapActions, mapState, mapGetters} from 'vuex';
import {uniqBy} from 'lodash';

import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlTermAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTermAutocomplete';
import WnlTagAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermEditorTagAutocomplete';

export default {
	props: {
		taxonomyId: {
			type: String|Number,
			required: true,
		}
	},
	data() {
		return {
			description: '',
			tag: null,
			parent: null,
		};
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById']),
		...mapState('taxonomyTerms', ['selectedTerms']),
		submitDisabled() {
			return this.tag === null;
		}
	},
	components: {
		WnlAutocomplete,
		WnlTermAutocomplete,
		WnlTagAutocomplete
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', {
			'createTerm': 'create',
		}),
		async onSave() {
			try {
				await this.createTerm({
					parent_id: this.parent ? this.parent.id : null,
					tag_id: this.tag.id,
					taxonomy_id: this.taxonomyId,
					description: this.description,
				});

				this.tag = null;
				this.parent = null;

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
		initializeParent(selectedTerms) {
			if (selectedTerms.length) {
				this.parent = this.termById(selectedTerms[0]);
			}
		}
	},
	mounted() {
		this.initializeParent(this.selectedTerms);
	},
	watch: {
		selectedTerms(selectedTerms) {
			this.initializeParent(selectedTerms);
		}
	}
};
</script>
