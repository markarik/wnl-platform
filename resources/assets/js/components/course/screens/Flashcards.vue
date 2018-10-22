<template>
	<div class="flashcards content">
		<h2 class="flashcards__title">Zestawy powtórkowe na dziś
			<ul class="flashcards__title__list">
				<li v-for="set in flashcardsSets" :key="set.id">{{set.lesson.name}}</li>
			</ul>
		</h2>
		<div v-html="screenData.content"/>
        <div v-for="set in flashcardsSets" :key="set.id">
            <h3>{{set.lesson.name}}</h3>
            <span>Numery map myśli: {{set.mind_maps_text}}</span>
			<ol class="flashcards-list">
				<li v-for="flashcard in set.flashcards" :key="flashcard.id" class="flashcards-list__item">
					<span class="flashcards-list__item__text">{{flashcard.content}}</span>
				</li>
			</ol>
        </div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.flashcards.content
		margin: $margin-big 0
		font-size: $font-size-plus-1
		line-height: $line-height-plus

		.flashcards-list
			counter-reset: li
			padding-left: 15px
			margin-left: 0
			list-style: decimal

			&__item
				list-style-type: none

				&:nth-child(5n)
					margin-bottom: $margin-big

				&:nth-child(10n-4),
				&:nth-child(10n-3),
				&:nth-child(10n-2),
				&:nth-child(10n-1),
				&:nth-child(10n)
					&:before
						color: $color-purple

				&:before
					color: $color-ocean-blue
					content: counter(li) ". "
					counter-increment: li
					font-weight: 700
</style>
<script>
	import {mapActions} from 'vuex';
	import {get} from 'lodash';

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
			...mapActions('flashcards', ['fetchFlashcardsSet']),
		},
		async mounted() {
			const resources = get(this.screenData, 'meta.resources', []);

			const setsResponse = await Promise.all(resources.map(({id}) => {
				return this.fetchFlashcardsSet({
					setId: id, include: 'flashcards,lesson'
				})
			}))

			const sets = [];

			setsResponse.forEach(setResponse => {
				const {included, ...data} = setResponse;

				// it's based on the assumption that there is always only one lesson for a flashcards set
				data.lesson = data.lesson.map(lessonId => {
					return included.lesson[lessonId]
				})[0];

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
