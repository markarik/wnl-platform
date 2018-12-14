<template>
	<span class="wnl-emoji" :class="emojiClass" v-html="emoji"></span>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-emoji
		margin: 0 $margin-tiny
</style>

<script>
import emoji from 'node-emoji';
import jEmoji from 'emoji';

export default {
	name: 'Emoji',
	props: ['emojiClass', 'name'],
	computed: {
		emoji() {
			return jEmoji.unifiedToHTML(emoji.get(this.name));
		}
	}
};
</script>
