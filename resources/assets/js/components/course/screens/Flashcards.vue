<template>
	<div class="flashcards content">
		<div class="flashcards__title">
			<h2 class="flashcards__title__header" id="flashacardsSetHeader">Zestawy powtórkowe na dziś</h2>
			<ul class="flashcards__title__list">
				<li class="flashcards__title__list__item" v-for="set in sets" :key="set.id" @click="scrollToSet(set.id)">{{set.name}}</li>
			</ul>
		</div>
		<div class="flashcards__description" v-html="screenData.content"/>
		<div class="flashcards-set" v-for="set in sets" :key="set.id">
			<div class="flashcards-set__title" :name="set.name" :id="`set-${set.id}`">
				<h3 class="flashcards-set__title__header">
					{{set.name}}
				</h3>
				<p>Numery map myśli: <span class="text--bold">{{set.mind_maps_text}}</span></p>
			</div>
			<div class="flashcards-set__results">
				<div class="flashcards-set__results__single text--easy">
					<span>{{getEasyForSet(set)}}</span>
					<span>{{ANSWERS_MAP.easy.text}}</span>
				</div>
				<div class="flashcards-set__results__single text--hard">
					<span>{{getHardForSet(set)}}</span>
					<span>{{ANSWERS_MAP.hard.text}}</span>
				</div>
				<div class="flashcards-set__results__single text--do-not-know">
					<span>{{getDontKnowForSet(set)}}</span>
					<span>{{ANSWERS_MAP.do_not_know.text}}</span>
				</div>
				<div class="flashcards-set__results__single">
					<span>{{getUnsolvedForSet(set)}}</span>
					<span>Bez odpowiedzi</span>
				</div>
			</div>
			<ol class="flashcards-set__list">
				<li v-for="(flashcard, index) in set.flashcards" :key="flashcard.id" class="flashcards-list__item">
					<span class="flashcards-list__item__index">{{index + 1}}</span>
					<div class="flashcards-list__item__container">
						<p class="flashcards-list__item__text">{{flashcard.content}}</p>
						<div class="flashcards-list__item__buttons" v-if="flashcard.answer === 'unsolved'">
							<a class="flashcards-list__item__buttons__button text--easy" @click="submitAnswer(flashcard, 'easy')">
								<span class="icon"><i :class="['fa', ANSWERS_MAP.easy.iconClass]"></i></span>
								<span class="flashcards-list__item__buttons__button__text">Łatwe</span>
							</a>
							<a class="flashcards-list__item__buttons__button text--hard" @click="submitAnswer(flashcard, 'hard')">
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
						<div v-else>
							<span :class="['flashcards-list__item__buttons__button', ANSWERS_MAP[flashcard.answer].buttonClass]">
								<span class="icon"><i :class="['fa', ANSWERS_MAP[flashcard.answer].iconClass]"></i></span>
								<span class="flashcards-list__item__buttons__button__text">{{ANSWERS_MAP[flashcard.answer].text}}</span>
							</span>
						</div>
					</div>
				</li>
			</ol>
		</div>
		<div class="flashcards-scroll" @click="scrollTop">
			<span class="icon is-small"><i class="fa fa-arrow-up"></i></span>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.text--bold
		font-weight: 600
	.text--easy
		color: $color-ocean-blue
	.text--hard
		color: $color-yellow
	.text--do-not-know
		color: $color-red

	.flashcards.content
		margin: $margin-big 0
		line-height: $line-height-base

	.flashcards
		&__title
			text-align: center

			&__header
				font-weight: 600
				text-transform: uppercase
				font-size: 18px

			&__list
				font-size: 16px
				list-style: none
				line-height: $line-height-plus

				&__item
					cursor: pointer
					color: $color-ocean-blue

		&__description
			margin-top: $margin-big

		.flashcards-set
			&__title
				text-align: center
				margin-top: $margin-big

				&__header
					font-size: $font-size-plus-3
					margin-bottom: 0

				&__sub
					font-size: $font-size-base

			&__results
				display: flex
				align-items: center
				justify-content: flex-start
				margin: $margin-base 0
				font-weight: $font-weight-bold
				text-transform: uppercase
				flex-wrap: wrap

				@media #{$media-query-tablet}
					justify-content: center

				&__single
					display: flex
					flex-direction: column
					align-items: center
					margin: $margin-small $margin-base

			&__list
				margin: $margin-small

		.flashcards-list
			padding-left: 15px

			&__item
				display: flex
				align-items: center

				&__index
					color: $color-ocean-blue
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

				&__buttons
					text-align: right
					display: flex
					align-items: center

					@media #{$media-query-tablet}
						flex: 0 0 78px * 3

					&__button
						opacity: 1
						display: flex
						flex-direction: column
						align-items: center
						margin: 0 $margin-small

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

		.flashcards-scroll
			width: 32px
			height: 32px
			background: $color-ocean-blue
			display: flex
			justify-content: center
			align-items: center
			position: absolute
			bottom: 60px
			right: 32px
			z-index: 20
			cursor: pointer

			.icon
				color: $color-white

</style>

<script>
	import {mapActions, mapGetters} from 'vuex';
	import {nextTick} from 	'vue'
	import {get} from 'lodash';
	import { scrollToElement } from 'js/utils/animations'

	const ANSWERS_MAP = {
		easy: {
			text: 'Łatwe',
			iconClass: 'fa-smile-o',
			buttonClass: 'text--easy'
		},
		hard: {
			text: 'Trudne',
			iconClass: 'fa-meh-o',
			buttonClass: 'text--hard'
		},
		do_not_know: {
			text: 'Nie wiem',
			iconClass: 'fa-frown-o',
			buttonClass: 'text--do-not-know'
		}
	}

	export default {
		props: {
			screenData: {
				type: Object,
				required: true
			}
		},
		data() {
			return {
				content: '',
				ANSWERS_MAP,
				applicableSetsIds: []
			}
		},
		computed: {
			...mapGetters('flashcards', ['getSetById']),
			sets() {
				return this.applicableSetsIds.map(id => this.getSetById(id))
			},
			getUnsolvedForSet() {
				return (set) => set.flashcards.filter(flashcard => flashcard.answer === 'unsolved').length
			},
			getEasyForSet() {
				return (set) => set.flashcards.filter(flashcard => flashcard.answer === 'easy').length
			},
			getHardForSet() {
				return (set) => set.flashcards.filter(flashcard => flashcard.answer === 'hard').length
			},
			getDontKnowForSet() {
				return (set) => set.flashcards.filter(flashcard => flashcard.answer === 'do_not_know').length
			}
		},
		methods: {
			...mapActions('flashcards', ['setFlashcardsSet', 'postAnswer']),
			scrollToSet(setId) {
				scrollToElement(document.getElementById(`set-${setId}`));
			},
			scrollTop() {
				scrollToElement(document.getElementById('flashacardsSetHeader'));
			},
			async submitAnswer(flashcard, answer) {
				await this.postAnswer({
					flashcard, answer
				})
			}
		},
		async mounted() {
			const resources = get(this.screenData, 'meta.resources', []);

			await Promise.all(resources.map(({id}) => {
				return !this.getSetById(id) && this.setFlashcardsSet({
					setId: id, include: 'flashcards'
				})
			}))

			this.applicableSetsIds = resources.map(({id}) => id);
		}
	}
</script>
