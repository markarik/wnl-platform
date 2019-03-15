<template>
	<div class="quiz-question">
		<div class="quiz-question-content">{{id}}. {{content}}</div>
		<button class="quiz-question-remove" type="button" @click="$emit('remove')">
			<span class="icon is-small">
				<i class="fa fa-trash"></i>
			</span>
		</button>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.quiz-question
		border-bottom: $border-light-gray
		cursor: move
		display: flex
		margin-right: $margin-huge
		padding: $margin-base 0
	.quiz-question-content
		overflow: hidden
		text-overflow: ellipsis
		width: 500px
		white-space: nowrap
	.quiz-question-remove
		background: none
		border: none
		cursor: pointer
		margin-left: $margin-big
		outline: none
		transition: color ease-in-out .2s
		&:hover
			color: $color-red
</style>

<script>
export default {
	props: {
		id: {
			type: Number,
			require: true
		},
		content: {
			type: String,
			required: true
		}
	}
};
</script>
