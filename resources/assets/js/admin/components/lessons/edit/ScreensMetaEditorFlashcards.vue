<template>
	<div>
		<h5 class="title is-5">Zarządzaj zestawami pytań</h5>
		<div class="meta-flashcards" v-if="areFlashcardsSetsReady">
			<div>
				<strong>Wybrane zestawy</strong>
				<ul>
					<draggable v-model="flashcardsSetIds" @start="drag=true" @end="drag=false">
						<li v-for="flashcardsSetId in flashcardsSetIds" class="flashcards-set">
							<div class="flashcards-set-content">
								{{flashcardsSetId}}. {{allFlashcardsSets.find(flashcardset => flashcardset.id === flashcardsSetId).name}}
							</div>
							<button class="flashcards-set-remove" type="button" @click="removeFlashcardsSet(flashcardsSetId)">
								<span class="icon is-small">
									<i class="fa fa-trash"></i>
								</span>
							</button>
						</li>
					</draggable>
				</ul>
			</div>
			<form @submit.prevent="addSetFormSubmit">
				<wnl-form-input
						name="flashcardsSetId"
						:form="addSetForm"
						v-model="addSetForm.flashcardsSetId"
				>
					Dodaj zestaw (id zestawu)
				</wnl-form-input>
				<button class="button is-small is-success"
						type="submit"
						:disabled="!addSetForm.flashcardsSetId"
				>
					<span class="margin right">+ Dodaj zestaw</span>
				</button>
			</form>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.meta-flashcards
		display: flex


	.flashcards-set
		border-bottom: $border-light-gray
		cursor: move
		display: flex
		margin-right: $margin-huge
		padding: $margin-base 0

	.flashcards-set-content
		overflow: hidden
		text-overflow: ellipsis
		width: 500px
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
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'
	import draggable from 'vuedraggable';

	import Form from 'js/classes/forms/Form';
	import WnlFormInput from "js/admin/components/forms/Input";

	export default {
		name: 'ScreensMetaEditorFlashcards',
		props: ['value'],
		components: {
			draggable,
			WnlFormInput
		},
		data: function() {
			return {
				addSetForm: new Form({
					flashcardsSetId: null
				}),
			};
		},
		computed: {
			flashcardsSetIds: {
				get: function () {
					if (!JSON.parse(this.value).resources) {
						return [];
					}

					return JSON.parse(this.value).resources.map(flashcardsSet => flashcardsSet.id);
				},
				set: function (flashcardsSetIds) {
					this.$emit('input', JSON.stringify({ resources: flashcardsSetIds.map(id => ({id, name: 'flashcards_sets'}))}))
				}
			},
			...mapGetters('flashcardsSets', {
				areFlashcardsSetsReady: 'isReady',
				allFlashcardsSets: 'allFlashcardsSets'
			}),
		},
		methods: {
			...mapActions('flashcardsSets', {
				flashcardsSetsSetup: 'setup'
			}),
			addSetFormSubmit: function() {
				const flashcardsSetId = parseInt(this.addSetForm.flashcardsSetId, 10);
				this.flashcardsSetIds = [...this.flashcardsSetIds, flashcardsSetId];
				this.addSetForm.reset();
			},
			removeFlashcardsSet: function(flashcardsSetId) {
				this.flashcardsSetIds = this.flashcardsSetIds.filter(id => id !== flashcardsSetId);
			}
		},
		mounted() {
			this.flashcardsSetsSetup();
		}
	}
</script>
