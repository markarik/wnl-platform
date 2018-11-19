<template>
	<div class="flashcards">
		<div class="flashcards__title content">
			<h2 class="flashcards__title__header" id="flashacardsSetHeader">Zestawy powtórkowe na dziś</h2>
			<ul class="flashcards__title__list">
				<li class="flashcards__title__list__item" v-for="set in sets" :key="set.id"
					@click="scrollToSet(set.id)">{{set.name}}
				</li>
			</ul>
		</div>
		<div class="flashcards__description content" v-html="screenData.content"/>
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
			<div @click="onRetakeSet(set)" class="flashcards-set__retake">
				<span class="icon"><i class="fa fa-undo"></i></span>
				ponów cały zestaw
			</div>
			<ol class="flashcards-set__list">
				<wnl-flashcard-item
						v-for="(flashcard, index) in set.flashcards"
						:key="flashcard.id"
						:flashcard="flashcard"
						:index="index + 1"
						:context="{type: context, id: screenData.id}"
				/>
			</ol>
		</div>
		<div class="flashcards-scroll" @click="scrollTop">
			<span class="icon is-small"><i class="fa fa-arrow-up"></i></span>
		</div>
	</div>
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
			&__retake
				display: flex
				align-items: center
				justify-content: flex-end
				text-transform: uppercase
				font-weight: 600
				margin: $margin-huge $margin-small $margin-small 0
				font-size: 12px
				cursor: pointer

				.fa-undo
					margin-right: $margin-base
					font-size: 16px

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
	import {mapActions, mapGetters, mapMutations} from 'vuex';
	import {nextTick} from 'vue'
	import {get} from 'lodash';
	import {scrollToElement} from 'js/utils/animations'
	import * as mutationsTypes from "js/store/mutations-types";
	import WnlFlashcardItem from 'js/components/course/screens/flashcards/FlashcardItem';
	import {ANSWERS_MAP} from 'js/consts/flashcard';

	export default {
		props: {
			screenData: {
				type: Object,
				required: true
			},
			context: {
				type: String,
				required: true
			}
		},
		components: {
			WnlFlashcardItem,
		},
		data() {
			return {
				ANSWERS_MAP,
				applicableSetsIds: [],
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
			...mapActions('flashcards', ['setFlashcardsSet']),
			...mapMutations('flashcards', {
				'updateFlashcard': mutationsTypes.FLASHCARDS_UPDATE_FLASHCARD
			}),
			scrollToSet(setId) {
				scrollToElement(document.getElementById(`set-${setId}`));
			},
			scrollTop() {
				scrollToElement(document.getElementById('flashacardsSetHeader'));
			},
			onRetakeSet(set) {
				set.flashcards.forEach(this.onRetakeFlashcard)
			},
		},
		async mounted() {
			const resources = get(this.screenData, 'meta.resources', []);

			await Promise.all(resources.map(({id}) => {
				return !this.getSetById(id) && this.setFlashcardsSet({
					setId: id,
					include: 'flashcards.user_flashcard_notes',
					context_type: this.context,
					context_id: this.screenData.id
				})
			}))

			this.applicableSetsIds = resources.map(({id}) => id);
		}
	}
</script>
