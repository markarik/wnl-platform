<template>
	<div class="margin left">
		<h5 class="title is-5">Zarządzaj zestawami pytań</h5>
		<div v-if="areFlashcardsSetsReady" class="meta-flashcards">
			<div class="margin right">
				<strong>Wybrane zestawy</strong>
				<ul>
					<draggable
						v-model="flashcardsSetIds"
						@start="drag=true"
						@end="drag=false"
					>
						<li
							v-for="flashcardsSetId in flashcardsSetIds"
							:key="flashcardsSetId"
							class="flashcards-set"
						>
							<div class="flashcards-set-content">
								{{flashcardsSetId}}. {{allFlashcardsSets.find(flashcardset => flashcardset.id === flashcardsSetId).name}}
							</div>
							<button
								class="flashcards-set-remove"
								type="button"
								@click="removeFlashcardsSet(flashcardsSetId)"
							>
								<span class="icon is-small">
									<i class="fa fa-trash" />
								</span>
							</button>
						</li>
					</draggable>
				</ul>
			</div>
			<wnl-autocomplete
				v-model="flashcardsSetInput"
				class="flashcards-set-add"
				:items="flashcardsSetsAutocompleteItems"
				label="Wybierz zestaw pytań"
				placeholder="Id lub treść aby wyszukać"
				@change="addFlashcardsSet"
			>
				<wnl-flashcards-set-autocomplete-item slot-scope="slotProps" :item="slotProps.item" />
			</wnl-autocomplete>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.meta-flashcards
		display: flex
		align-items: flex-start

	.flashcards-set
		border-bottom: $border-light-gray
		cursor: move
		display: flex
		padding: $margin-base 0

	.flashcards-set-content
		overflow: hidden
		text-overflow: ellipsis
		width: 400px
		white-space: nowrap

	.flashcards-set-remove
		background: none
		border: none
		cursor: pointer
		margin-left: $margin-big
		outline: none
		transition: color ease-in-out .2s
		&:hover
			color: red

	.flashcards-set-add
		width: 300px
</style>

<script>
import { mapState, mapActions } from 'vuex';
import draggable from 'vuedraggable';

import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlFlashcardsSetAutocompleteItem from 'js/admin/components/lessons/edit/FlashcardsSetAutocompleteItem';

export default {
	name: 'ScreensMetaEditorFlashcards',
	components: {
		draggable,
		WnlAutocomplete,
		WnlFlashcardsSetAutocompleteItem,
	},
	props: ['value'],
	data: function() {
		return {
			flashcardsSetInput: '',
		};
	},
	computed: {
		flashcardsSetIds: {
			get: function () {
				if (!this.value.resources) {
					return [];
				}

				return this.value.resources.map(flashcardsSet => flashcardsSet.id);
			},
			set: function (flashcardsSetIds) {
				this.$emit('input', { resources: flashcardsSetIds.map(id => ({ id, name: 'flashcards_sets' })) });
			}
		},
		flashcardsSetsAutocompleteItems: function() {
			if (this.flashcardsSetInput === '') {
				return [];
			}

			return this.allFlashcardsSets
				.filter(flashcardsSet => !this.flashcardsSetIds.includes(flashcardsSet.id) &&
						(
							flashcardsSet.id === parseInt(this.flashcardsSetInput, 10) ||
							flashcardsSet.name.toLowerCase().includes(this.flashcardsSetInput.toLowerCase())
						)
				);
		},
		...mapState('flashcardsSets', {
			areFlashcardsSetsReady: 'ready',
			allFlashcardsSets: 'flashcardsSets'
		}),
	},
	mounted() {
		this.flashcardsSetsSetup();
	},
	methods: {
		...mapActions('flashcardsSets', {
			flashcardsSetsSetup: 'setup'
		}),
		addFlashcardsSet: function(flashcardsSet) {
			this.flashcardsSetIds = [...this.flashcardsSetIds, flashcardsSet.id];
			this.flashcardsSetInput = '';
		},
		removeFlashcardsSet: function(flashcardsSetId) {
			this.flashcardsSetIds = this.flashcardsSetIds.filter(id => id !== flashcardsSetId);
		}
	},
};
</script>
