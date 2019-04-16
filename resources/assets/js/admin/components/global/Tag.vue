<template>
	<span
		class="tag is-medium"
		:style="{backgroundColor: getColourForStr(tag.name)}"
		@click="$emit('click', $event)"
	>
		<slot />
		{{tag.name}}
	</span>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.tag
		margin: $margin-small-minus $margin-medium $margin-small-minus 0
</style>

<script>
import { getColourForStr } from 'js/utils/colors.js';

export default {
	data() {
		return {
			getColourForStr,
		};
	},
	props: {
		tag: {
			type: Object,
			required: true
		}
	},
};
</script>
