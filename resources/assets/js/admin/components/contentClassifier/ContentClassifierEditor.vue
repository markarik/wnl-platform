<template>
	<div class="content-classifier__panel-editor">
		<h4 class="title is-4 margin bottom">Zarządzaj</h4>
		<label class="label is-uppercase"><strong>Pojęcia</strong></label>
		<ul class="margin bottom">
			<li v-for="term in activeTaxonomyTerms" :key="term.id">{{term.tag.name}}</li>
		</ul>
		<div class="field">
			<label class="label is-uppercase"><strong>Dodaj pojęcie</strong></label>
			<wnl-select
				:options="taxonomiesOptions"
				v-model="taxonomyId"
				@input="onTaxonomyChange"
			/>
			<wnl-taxonomy-term-autocomplete
				placeholder="Wyszukaj pojęcie"
				@change="onTermAdded"
			/>
		</div>

	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content-classifier
		&__panel-editor
			flex: 50%
</style>

<script>
import axios from 'axios';
import {mapActions, mapGetters, mapState} from 'vuex';
import {uniqBy} from 'lodash';

import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

import WnlSelect from 'js/admin/components/forms/Select';
import WnlTaxonomyTermAutocomplete from 'js/admin/components/taxonomies/TaxonomyTermAutocomplete';

export default {
	components: {
		WnlTaxonomyTermAutocomplete,
		WnlSelect,
	},
	data() {
		return {
			taxonomyId: null,
		};
	},
	props: {
		filteredContent: {
			type: Object,
			required: true,
		}
	},
	computed: {
		...mapGetters('taxonomyTerms', ['termById']),
		...mapState('taxonomies', ['taxonomies']),
		activeTaxonomyTerms() {
			const taxonomyTerms = [];

			// TODO simplify the spaghetti
			Object.keys(this.filteredContent)
				.forEach(contentType => {
					this.filteredContent[contentType]
						.forEach(item => {
							if (item.taxonomyTerms) {
								taxonomyTerms.push(...item.taxonomyTerms);
							}
						});
				});

			return uniqBy(taxonomyTerms, 'id');
		},
		taxonomiesOptions() {
			if (!this.taxonomies) {
				return [];
			}

			return this.taxonomies.map(taxonomy => ({value: taxonomy.id, text: taxonomy.name}));
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('taxonomyTerms', ['fetchTermsByTaxonomy']),
		...mapActions('taxonomies', {
			fetchTaxonomies: 'fetchAll',
		}),

		async onTermAdded(term) {
			try {
				await axios.put(getApiUrl(`taxonomy_termables/${term.id}`), {
					annotations: this.filteredContent.annotations.map(item => item.id),
					flashcards: this.filteredContent.flashcards.map(item => item.id),
					quiz_questions: this.filteredContent.quizQuestions.map(item => item.id),
					slides: this.filteredContent.slides.map(item => item.id),
				});
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Nie udało się zapisać nowej klasyfikacji. Spróbuj ponownie.',
					type: ALERT_TYPES.ERROR
				});
			}
		},
		async onTaxonomyChange(taxonomyId) {
			try {
				await this.fetchTermsByTaxonomy(taxonomyId);
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Coś poszło nie tak przy pobieraniu struktury Taksonomii',
					type: ALERT_TYPES.ERROR
				});
			}
		}
	},
	async mounted() {
		try {
			await this.fetchTaxonomies();
		} catch (error) {
			$wnl.logger.capture(error);
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak przy pobieraniu listy Taksonomii',
				type: ALERT_TYPES.ERROR
			});
		}
	},
};
</script>
