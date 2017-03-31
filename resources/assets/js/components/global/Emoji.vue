<template>
	<span class="wnl-emoji" :class="emojiClass">{{emoji}}</span>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-emoji
		margin: 0 $margin-tiny
</style>

<script>
	import emoji from 'node-emoji'

	export default {
		name: 'Emoji',
		props: ['emojiClass', 'name'],
		computed: {
			emoji() {
				return emoji.get(this.name)
			}
		}
	}
</script>
