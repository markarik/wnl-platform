<template>
	<div class="card margin vertical">
		<header class="card-header">
			<p class="card-header-title">
				{{text}}
			</p>
		</header>
		<div class="card-content">
			<p class="quiz-answer" v-for="(answer, index) in answers"
				:class="{'is-selected': isSelected(index), 'is-correct': isCorrect(index)}"
				@click="selectAnswer(index)"
			>
				{{answer.text}}
			</p>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.quiz-answer
		border-bottom: $border-light-gray
		cursor: pointer
		padding: 0.5em
		margin: 0

		&:last-child
			margin: 0

		&:hover
			background: $color-light-gray

		&:active
			background: $color-inactive-gray

		&, &:hover, &:active
			transition: all $transition-length-base

	.is-correct
		background: $color-green

		&:active, &:hover
			background: $color-green

	.is-selected
		background: $color-inactive-gray

		&:active, &:hover
			background: $color-inactive-gray
</style>

<script>
	export default {
		props: ['answers', 'index', 'text', 'total', 'isResolved'],
		name: 'QuizQuestion',
		data() {
			return {
				selected: null,
			}
		},
		computed: {
			number() {
				return this.index + 1
			},
			isResolved() {
				return true
			}
		},
		methods: {
			isSelected(index) {
				return this.selected === index
			},
			isCorrect(index) {
				return this.isResolved && this.answers[index].is_correct
			},
			selectAnswer(index) {
				this.selected = index
			}
		}
	}
</script>
