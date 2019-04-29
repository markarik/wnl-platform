<template>
	<p
		v-if="showBadge"
		:class="['verify', 'icon', verified && 'verify--verified', canVerify && 'verify--can_verify']"
		@click="onClick"
	>
		<file-verified-svg class="verify__svg" />
		<span>{{message}}</span>
	</p>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.verify
		font-size: $font-size-minus-1
		font-weight: bold
		color: $color-gray
		text-transform: uppercase
		width: auto

		&--verified
			color: $color-green

		&--can_verify
			cursor: pointer

			&:hover
				color: $color-red

		&__svg
			width: 16px
			height: 16px

		span
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
		},
		canVerify: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		showBadge() {
			return this.verified || this.canVerify;
		},
		verified () {
			return this.resource.verified_at;
		},
		message() {
			return this.verified ? 'Zweryfikowano' : 'Zweryfikuj';
		}
	},
	methods: {
		onClick () {
			if (!this.canVerify) return;

			if (this.verified) this.$emit('unverify');
			else this.$emit('verify');
		}
	}
};
</script>
