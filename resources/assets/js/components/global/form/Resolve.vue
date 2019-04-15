<template>
	<p
		v-if="$moderatorFeatures.isAllowed('access') && resource.resolved"
		class="resolvable icon"
		@click="$emit('unresolveResource')"
	>
		&nbsp;·
		<i class="fa fa-undo" />
		<span v-t="'ui.action.undo'" />
	</p>
	<span v-else-if="$moderatorFeatures.isAllowed('access')" class="resolvable icon">
		&nbsp;·
		<i class="fa fa-check" @click="$emit('resolveResource')" />
	</span>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.resolvable
		cursor: pointer
		width: auto

		&:hover
			color: $color-red

		span
			margin-left: $margin-small

		i
			font-size: $font-size-minus-1
			margin-left: $margin-small

</style>

<script>
import moderator from 'js/perimeters/moderator';

export default {
	props: ['resource'],
	perimeters: [
		moderator,
	]
};
</script>
