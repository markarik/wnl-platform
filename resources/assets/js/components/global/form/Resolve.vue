<template>
	<span class="resolvable icon" v-if="$moderatorFeatures.isAllowed('access')">
		&nbsp;·
		<i class="fa fa-check icon" @click="$emit('resolveResource')"/>
	</span>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.icon
		cursor: pointer

		&:hover
			color: $color-red
</style>

<script>
	import moderator from 'js/perimeters/moderator'

	export default {
		perimeters: [
			moderator,
		]
	}
</script>
