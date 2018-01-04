<template lang="html">
	<div class="conversation-snippet">
		<wnl-avatar fullName="Roman" size="large"></wnl-avatar>
		siema
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.conversation-snippet
		display: flex
		padding: $margin-medium

		&:hover
			cursor: pointer
			background-color: $color-background-lightest-gray

</style>

<script>
	export default {
		name: 'ConversationSnippet',
	}
</script>
