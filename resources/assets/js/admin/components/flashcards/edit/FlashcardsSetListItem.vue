<template>
	<div class="flashcard">
		<div class="flashcard-content">{{id}}. {{content}}</div>
		<button
			class="flashcard-remove"
			type="button"
			@click="$emit('remove')"
		>
			<span class="icon is-small">
				<i class="fa fa-trash"></i>
			</span>
		</button>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.flashcard
		border-bottom: $border-light-gray
		cursor: move
		display: flex
		margin-right: $margin-huge
		padding: $margin-base 0
	.flashcard-content
		overflow: hidden
		text-overflow: ellipsis
		width: 500px
		white-space: nowrap
	.flashcard-remove
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
export default {
	name: 'WnlFlashcardsSetListItem',
	props: ['id', 'content', 'onRemove'],
};
</script>
