<template>
	<p
		:class="['verify', 'icon', resource.verified_at && 'verify--verified']"
		@click="onClick"
	>
		<file-verified-svg class="verify__svg"/>
		<span>{{message}}</span>
	</p>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.verify
		font-size: $font-size-minus-1
		cursor: pointer
		width: auto
		color: $color-gray

		&--verified
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
	computed: {
		message() {
			return this.resource.verified_at ? 'Zweryfikowano' : 'Zweryfikuj';
		}
	},
	methods: {
		onClick () {
			if (this.resource.verified_at) this.$emit('unverify');
			else this.$emit('verify');
		}
	}
};
</script>
