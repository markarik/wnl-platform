<template>
	<li
		:class="['flashcards-list__item', flashcard.answer !== 'unsolved' && 'flashcards-list__item--solved']"
	>
		<span class="flashcards-list__item__index">{{index}}</span>
		<div class="flashcards-list__item__container">
			<div class="flashcards-list__item__text__container">
				<p class="flashcards-list__item__text">{{flashcard.content}}</p>
				<div
					class="flashcards-list__item__buttons"
					:class="{'wnl-is-loading': isLoading}"
					v-if="flashcard.answer === 'unsolved'"
				>
					<a
						class="flashcards-list__item__buttons__button text--easy"
						@click="submitAnswer(flashcard, 'easy')"
						:title="ANSWERS_MAP.easy.text"
					>
						<span class="icon"><i :class="['fa', ANSWERS_MAP.easy.iconClass]"></i></span>
					</a>
					<a
						class="flashcards-list__item__buttons__button text--hard"
						@click="submitAnswer(flashcard, 'hard')"
						:title="ANSWERS_MAP.hard.text"
					>
						<span class="icon"><i :class="['fa', ANSWERS_MAP.hard.iconClass]"></i></span>
					</a>
					<a
						class="flashcards-list__item__buttons__button text--do-not-know"
						@click="submitAnswer(flashcard, 'do_not_know')"
						:title="ANSWERS_MAP.do_not_know.text"
					>
						<span class="icon"><i :class="['fa', ANSWERS_MAP.do_not_know.iconClass]"></i></span>
					</a>
				</div>
				<div
					class="flashcards-list__item__buttons flashcards-list__item__buttons--retake"
					v-else
				>
					<span
						class="flashcards-list__item__buttons__button"
						@click="onRetakeFlashcard(flashcard)"
						title="Ponów"
					>
						<span class="icon"><i class="fa fa-undo"></i></span>
					</span>
					<span
						:class="['flashcards-list__item__buttons__button is-disabled', ANSWERS_MAP[flashcard.answer].buttonClass]"
						:title="ANSWERS_MAP[flashcard.answer].text"
					>
						<span class="icon">
							<i :class="['fa', ANSWERS_MAP[flashcard.answer].iconClass]"></i>
						</span>
					</span>
				</div>
			</div>
			<div v-if="flashcard.answer !== 'unsolved'">
				<wnl-text-button
					v-if="!flashcard.note && !isNoteEditorOpen"
					@click="toggleNoteEditor"
					type="button"
				>+ DODAJ NOTATKĘ</wnl-text-button>
				<div v-if="flashcard.note && !isNoteEditorOpen">
					<label class="label">TWOJA NOTATKA
					<wnl-text-button
						type="button"
						@click="toggleNoteEditor"
						icon="edit"
					>EDYTUJ</wnl-text-button>
					</label>
					<span class="flashcards-list__item__note-content content" v-html="flashcard.note.note" />
				</div>
				<wnl-form
					v-if="isNoteEditorOpen"
					:method="noteFormMethod"
					:reset-after-submit="true"
					:resource-route="noteFormResourceRoute"
					:name="`flashcardNote-${flashcard.id}`"
					:suppress-enter="true"
					:hide-default-submit="true"
					@submitSuccess="onSubmitSuccess"
				>
					<label class="label">TWOJA NOTATKA
					<wnl-text-button
						type="button"
						@click="toggleNoteEditor"
						icon="close"
					>ANULUJ</wnl-text-button>
					</label>
					<wnl-quill
						name="note"
						class="margin bottom flashcards-list__item__note-editor"
						:options="{ theme: 'snow', placeholder: 'Wpisz swoją notatkę...' }"
						:toolbar="[['bold', 'italic', 'underline', 'link'], [{ color: fontColors }], ['clean']]"
						v-model="note"
					/>
					<div class="level">
						<div class="level-item">
							<wnl-submit css-class="button is-small is-primary">
								Zapisz
							</wnl-submit>
						</div>
					</div>
				</wnl-form>
			</div>
		</div>
	</li>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	$buttonWidth: 42px

	.text--bold
		font-weight: 600

	.text--easy
		color: $color-ocean-blue

	.text--hard
		color: $color-yellow

	.text--do-not-know
		color: $color-red

	.flashcards-list__item
		align-items: center
		display: flex
		flex-direction: column
		margin-top: $margin-base

		&--solved
			color: $color-gray

			.flashcards-list__item__container
				background: $color-background-lightest-gray

		&__index
			font-weight: $font-weight-bold

		&__container
			border: $border-light-gray
			border-radius: $border-radius-small
			margin: 0
			padding: $margin-base
			width: 100%

			@media #{$media-query-tablet}
				margin: $margin-small 0

		&__text__container
			display: flex
			min-height: 54px
			flex-direction: column

			@media #{$media-query-tablet}
				flex-direction: row
				align-items: center

		&__text
			flex-grow: 1
			color: inherit

		&__buttons
			align-items: center
			display: flex
			justify-content: center
			margin: $margin-small 0

			@media #{$media-query-tablet}
				flex: 0 0 $buttonWidth * 2
				justify-content: flex-end
				margin: 0 0 0 $margin-big

			&--retake
				color: $color-blue

				.flashcards-list__item__buttons__button .icon .fa-undo
					font-size: 16px

			&__button
				opacity: 1
				display: flex
				flex-direction: column
				align-items: center
				padding: $margin-small
				cursor: pointer
				transition: opacity ease-in-out .1s

				@media #{$media-query-tablet}
					flex-basis: 42px
					width: 42px

				&:hover
					opacity: 1

				.icon
					width: 24px
					height: 24px

					.fa
						font-size: 24px

				&__text
					font-weight: $font-weight-bold
					text-transform: uppercase
					font-size: 12px

				&.is-disabled
					cursor: auto

		&__note-editor
			background: white
</style>

<script>
import { mapActions, mapMutations } from 'vuex';
import * as mutationsTypes from 'js/store/mutations-types';
import { Quill as WnlQuill, Form as WnlForm, Submit as WnlSubmit } from 'js/components/global/form/index';
import WnlTextButton from 'js/components/global/TextButton';
import { ANSWERS_MAP } from 'js/consts/flashcard';
import { fontColors } from 'js/utils/colors';
import features from 'js/consts/events_map/features.json';
import emits_events from 'js/mixins/emits-events';

export default {
	mixins: [emits_events],
	props: {
		context: {
			type: Object,
			required: true
		},
		flashcard: {
			type: Object,
			required: true,
		},
		index: {
			type: Number,
			required: true,
		},
	},
	components: {
		WnlQuill,
		WnlForm,
		WnlTextButton,
		WnlSubmit,
	},
	data() {
		return {
			ANSWERS_MAP,
			note: this.flashcard.note && this.flashcard.note.note || '',
			fontColors,
			isNoteEditorOpen: false,
			isLoading: false,
		};
	},
	computed: {
		noteFormMethod() {
			return this.flashcard.note ? 'put' : 'post';
		},
		noteFormResourceRoute() {
			let resourceUrl = `user_flashcard_notes/${this.flashcard.id}`;

			if (this.flashcard.note) {
				resourceUrl += `/${this.flashcard.note.id}`;
			}

			return resourceUrl;
		}
	},
	methods: {
		...mapActions('flashcards', ['postAnswer']),
		...mapMutations('flashcards', {
			'updateFlashcard': mutationsTypes.FLASHCARDS_UPDATE_FLASHCARD
		}),
		onRetakeFlashcard(flashcard) {
			this.updateFlashcard({
				...flashcard,
				answer: 'unsolved'
			});

			this.note = this.flashcard.note && this.flashcard.note.note || '';
		},
		async submitAnswer(flashcard, answer) {
			if (this.isLoading) {
				return;
			}

			this.isLoading = true;
			await this.postAnswer({
				flashcard,
				answer,
				context_type: this.context.type,
				context_id: this.context.id
			});
			this.isLoading = false;
			this.isNoteEditorOpen = ['hard', 'do_not_know'].includes(answer);
			this.emitUserEvent({
				feature_component: features.flashcards.feature_components.single.value,
				action: features.flashcards.feature_components.single.actions.select_answer.value,
				target: this.flashcard.id,
				value: Object.keys(ANSWERS_MAP).indexOf(answer)
			});
		},
		onSubmitSuccess(updatedNote) {
			this.updateFlashcard({
				...this.flashcard,
				note: updatedNote
			});
			this.isNoteEditorOpen = false;
			this.emitUserEvent({
				feature_component: features.flashcards.feature_components.single.value,
				action: features.flashcards.feature_components.single.actions.add_note.value,
				target: this.flashcard.id,
			});
		},
		toggleNoteEditor() {
			this.isNoteEditorOpen = !this.isNoteEditorOpen;
		},
	},
};
</script>
