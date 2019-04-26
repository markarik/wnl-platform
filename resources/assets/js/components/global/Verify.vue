<template>
	<p
		v-if="resource.verified_at"
		class="verify icon"
		@click="$emit('unverify')"
	>
		<file-verified-svg class="verify__svg verify__svg--verified"/>
		<span>Zweryfikowano</span>
	</p>
	<span
		v-else
		class="verify icon"
		@click="$emit('verify')"
	>
		<file-verified-svg class="verify__svg"/>
		<span>Zweryfikuj</span>
	</span>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.verify
		cursor: pointer
		width: auto
		color: $color-green

		&__svg
			width: 16px
			height: 16px

		&:hover
			color: $color-red


		span
			margin-left: $margin-small

		i
			font-size: $font-size-minus-1
			margin-left: $margin-small

</style>

<script>
import FileVerifiedSvg from 'images/icons/file-verified.svg';

export default {
	components: {
		FileVerifiedSvg,
	},
	props: {
		resource: {
			type: Object,
			required: true
		}
	},
};
</script>
