<template>
	<p
		v-if="$moderatorFeatures.isAllowed('access') && resource.verified_at"
		class="verify icon"
		@click="$emit('unverify')"
	>
		<file-verified-svg />
		<span>Zweryfikowano</span>
	</p>
	<span
		v-else-if="$moderatorFeatures.isAllowed('access')"
		class="verify icon"
		@click="$emit('verify')"
	>
		<file-verified-svg />
		<span>Zweryfikuj</span>
	</span>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.verify
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
import FileVerifiedSvg from 'images/icons/file-verified.svg';

export default {
	components: {
		FileVerifiedSvg,
	},
	props: ['resource'],
	perimeters: [
		moderator,
	]
};
</script>
