<template>
	<li
			:class="['flashcards-list__item', flashcard.answer !== 'unsolved' && 'flashcards-list__item--solved']"
	>
		<span class="flashcards-list__item__index">{{index + 1}}</span>
		<div class="flashcards-list__item__container">
			<p class="flashcards-list__item__text">{{flashcard.content}}</p>
			<div class="flashcards-list__item__buttons" v-if="flashcard.answer === 'unsolved'">
				<a class="flashcards-list__item__buttons__button text--easy"
				   @click="submitAnswer(flashcard, 'easy')">
					<span class="icon"><i :class="['fa', ANSWERS_MAP.easy.iconClass]"></i></span>
					<span class="flashcards-list__item__buttons__button__text">Łatwe</span>
				</a>
				<a class="flashcards-list__item__buttons__button text--hard"
				   @click="submitAnswer(flashcard, 'hard')">
					<span class="icon"><i :class="['fa', ANSWERS_MAP.hard.iconClass]"></i></span>
					<span class="flashcards-list__item__buttons__button__text">Trudne</span>
				</a>
				<a
						class="flashcards-list__item__buttons__button text--do-not-know"
						@click="submitAnswer(flashcard, 'do_not_know')"
				>
					<span class="icon"><i :class="['fa', ANSWERS_MAP.do_not_know.iconClass]"></i></span>
					<span class="flashcards-list__item__buttons__button__text">Nie Wiem</span>
				</a>
			</div>
			<div
					class="flashcards-list__item__buttons flashcards-list__item__buttons--retake"
					@click="onRetakeFlashcard(flashcard)"
					v-else
			>
				<span class="flashcards-list__item__buttons__button">
					<span class="icon"><i class="fa fa-undo"></i></span>
				</span>
				<span :class="['flashcards-list__item__buttons__button', ANSWERS_MAP[flashcard.answer].buttonClass]">
					<span class="icon"><i
							:class="['fa', ANSWERS_MAP[flashcard.answer].iconClass]"></i></span>
					<span class="flashcards-list__item__buttons__button__text">{{ANSWERS_MAP[flashcard.answer].text}}</span>
				</span>
			</div>
		</div>
		<div v-if="flashcard.answer !== 'unsolved'">
			<div v-if="flashcard.note">{{flashcard.note.note}}</div>
			<wnl-form
					v-else
					method="post"
					suppressEnter="true"
					resetAfterSubmit="true"
					:resourceRoute="`user_flashcard_notes/${flashcard.id}`"
					:name="`flashcardNote-${flashcard.id}`"
					@submitSuccess="onSubmitSuccess">
				<label class="label">Notatka {{flashcard.id}}</label>
				<wnl-quill
						name="note"
						class="margin bottom"
						:options="{ theme: 'snow', placeholder: 'Wpisz swoją notatkę...' }"
						v-model="note"
				/>
			</wnl-form>
		</div>
	</li>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	$buttonWidth: 78px

	.text--bold
		font-weight: 600

	.text--easy
		color: $color-ocean-blue

	.text--hard
		color: $color-yellow

	.text--do-not-know
		color: $color-red

	.flashcards-list__item
		display: flex
		align-items: center

		&--solved
			color: $color-gray-dimmed

			.flashcards-list__item__container
				background: $color-background-lightest-gray

		&__index
			font-weight: $font-weight-bold
			text-align: right

		&__container
			display: flex
			border: $border-light-gray
			padding: $margin-base
			flex-grow: 1
			margin: $margin-small 0 $margin-small $margin-small
			min-height: 54px
			flex-direction: column

			@media #{$media-query-tablet}
				flex-direction: row
				align-items: center

		&__text
			flex-grow: 1
			color: inherit

		&__buttons
			text-align: right
			display: flex
			align-items: center

			@media #{$media-query-tablet}
				flex: 0 0 $buttonWidth * 3

			&--retake
				flex: 0 0 $buttonWidth * 2

				.flashcards-list__item__buttons__button .icon .fa-undo
					font-size: 16px

			&__button
				opacity: 1
				display: flex
				flex-direction: column
				align-items: center
				margin: 0 $margin-small
				cursor: pointer

				@media #{$media-query-tablet}
					flex-basis: 78px
					width: 64px

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

</style>

<script>
	import {mapActions, mapGetters, mapMutations} from 'vuex';
	import {nextTick} from 'vue'
	import {get} from 'lodash';
	import {scrollToElement} from 'js/utils/animations'
	import * as mutationsTypes from "js/store/mutations-types";
	import {Quill as WnlQuill, Form as WnlForm} from 'js/components/global/form/index';
	import {ANSWERS_MAP} from 'js/consts/flashcard';

	export default {
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
		},
		data() {
			return {
				ANSWERS_MAP,
				note: '',
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
				})
			},
			async submitAnswer(flashcard, answer) {
				await this.postAnswer({
					flashcard,
					answer,
					context_type: this.context.type,
					context_id: this.context.id
				})
			},
			onSubmitSuccess() {

			}
		},
	}
</script>
