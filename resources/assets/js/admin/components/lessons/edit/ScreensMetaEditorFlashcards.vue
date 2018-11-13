<template>
	<div class="margin left">
		<h5 class="title is-5">Zarządzaj zestawami pytań</h5>
		<div class="meta-flashcards" v-if="areFlashcardsSetsReady">
			<div class="margin right">
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
			<div class="flashcards-set-add">
				<label class="label">Wybierz zestaw pytań</label>
				<div class="select">
					<wnl-select
							:options="flashcardsSetOptions"
							name="flashcardsSetId"
							v-model="addSetForm.flashcardsSetId"
							@input="addSet"
					></wnl-select>
				</div>
			</div>
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
	import { mapState, mapActions } from 'vuex'
	import draggable from 'vuedraggable';

	import Form from 'js/classes/forms/Form';
	import WnlSelect from "js/admin/components/forms/Select";

	export default {
		name: 'ScreensMetaEditorFlashcards',
		props: ['value'],
		components: {
			draggable,
			WnlSelect,
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
					if (!this.value.resources) {
						return [];
					}

					return this.value.resources.map(flashcardsSet => flashcardsSet.id);
				},
				set: function (flashcardsSetIds) {
					this.$emit('input', { resources: flashcardsSetIds.map(id => ({id, name: 'flashcards_sets'})) });
				}
			},
			flashcardsSetOptions: function() {
				return this.allFlashcardsSets
					.filter(flashcardsSet => !this.flashcardsSetIds.includes(flashcardsSet.id))
					.map(flashcardsSet => ({
						value: flashcardsSet.id,
						text: `${flashcardsSet.id}. ${flashcardsSet.name}`
					}));
			},
			...mapState('flashcardsSets', {
				areFlashcardsSetsReady: 'ready',
				allFlashcardsSets: 'flashcardsSets'
			}),
		},
		methods: {
			...mapActions('flashcardsSets', {
				flashcardsSetsSetup: 'setup'
			}),
			addSet: function() {
				if (this.addSetForm.flashcardsSetId) {
					this.flashcardsSetIds = [...this.flashcardsSetIds, this.addSetForm.flashcardsSetId];
					this.addSetForm.reset();
				}
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
