<template>
	<div class="flashcards content">
		<div class="flashcards__title">
			<h2 class="flashcards__title__header" id="flashacardsSetHeader">Zestawy powtórkowe na dziś</h2>
			<ul class="flashcards__title__list">
				<li class="flashcards__title__list__item" v-for="set in flashcardsSets" :key="set.id" @click="scrollToSet(set.id)">{{set.name}}</li>
			</ul>
		</div>
		<div class="flashcards__description" v-html="screenData.content"/>
		<div class="flashcards-set" v-for="set in flashcardsSets" :key="set.id">
			<div class="flashcards-set__title" :name="set.name" :id="`set-${set.id}`">
				<h3 class="flashcards-set__title__header">
					{{set.name}}
				</h3>
				<p>Numery map myśli: <span class="text--bold">{{set.mind_maps_text}}</span></p>
			</div>
			<ol class="flashcards-set__list">
				<li v-for="(flashcard, index) in set.flashcards" :key="flashcard.id" class="flashcards-list__item">
					<span class="flashcards-list__item__index">{{index + 1}}</span>
					<p class="flashcards-list__item__text">{{flashcard.content}}</p>
					<div class="flashcards-list__item__buttons">
						<button @click="submitAnswer(flashcard.id, 'easy')">łatwe</button>
						<button @click="submitAnswer(flashcard.id, 'hard')">trudne</button>
						<button @click="submitAnswer(flashcard.id, 'do not know')">nie wiem</button>
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

		.flashcards-list
			padding-left: 15px

			&__item
				display: flex
				align-items: center

				&__index
					color: $color-ocean-blue
					font-weight: $font-weight-bold
					flex-basis: 20px
					text-align: right

				&:nth-child(5n)
					.flashcards-list__item__index
						margin-bottom: $margin-big

				&:nth-child(10n-4),
				&:nth-child(10n-3),
				&:nth-child(10n-2),
				&:nth-child(10n-1),
				&:nth-child(10n)
					.flashcards-list__item__index
						color: $color-purple

				&__text
					border: $border-light-gray
					padding: $margin-base
					flex-grow: 1
					margin: $margin-small 0 $margin-small $margin-small
					min-height: 54px

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
	import {mapActions} from 'vuex';
	import {nextTick} from 	'vue'
	import {get} from 'lodash';
	import { scrollToElement } from 'js/utils/animations'

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
				flashcards: [],
				flashcardsSets: []
			}
		},
		methods: {
			...mapActions('flashcards', ['fetchFlashcardsSet', 'postAnswer']),
			scrollToSet(setId) {
				scrollToElement(document.getElementById(`set-${setId}`));
			},
			scrollTop() {
				scrollToElement(document.getElementById('flashacardsSetHeader'));
			},
			async submitAnswer(flashcardId, answer) {
				await this.postAnswer({
					flashcardId, answer
				})
			}
		},
		async mounted() {
			const resources = get(this.screenData, 'meta.resources', []);

			const setsResponse = await Promise.all(resources.map(({id}) => {
				return this.fetchFlashcardsSet({
					setId: id, include: 'flashcards'
				})
			}))

			const sets = [];

			setsResponse.forEach(setResponse => {
				const {included, ...data} = setResponse;

				// it's based on the assumption that there is always only one lesson for a flashcards set
				data.flashcards = data.flashcards.map(flashcardId => {
					return included.flashcards[flashcardId]
				});

				sets.push(data)
			});

			console.log(sets);
			this.flashcardsSets = sets;
		}
	}
</script>
