<template>
	<div :class="sizeClass">
		<img class="wnl-avatar wnl-avatar-custom" v-if="isCustom">
		<div class="wnl-avatar wnl-avatar-automatic" :class="colorClass" v-else>{{ initials }}</div>
	</div>
</template>
<style lang="sass">
	@import 'resources/assets/sass/variables'

	// Variables
	$avatars-colors-list: #1abc9c, #2ecc71, #3498db, #9b59b6, #34495e, #16a085, #27ae60, #2980b9, #8e44ad, #2c3e50, #f1c40f, #e67e22, #e74c3c, #95a5a6, #f39c12, #d35400, #c0392b, #bdc3c7, #7f8c8d

	@for $i from 1 to 19
		.wnl-avatar-color-#{$i}
			background-color: nth($avatars-colors-list, $i)

	$avatar-base-size-small: 20px
	$avatar-base-size-medium: 30px
	$avatar-base-size-large: 50px

	=avatar-dimensions($baseSize)
		font-size: $baseSize / 2
		line-height: $baseSize / 2
		height: $baseSize
		width: $baseSize

		.wnl-avatar-automatic
			border-radius: $baseSize / 10

	.wnl-avatar
		align-items: center
		display: flex
		height: 100%
		justify-content: center
		width: 100%

	.wnl-avatar-automatic
		color: $color-white
		font-weight: $font-weight-black

	.wnl-avatar-small
		+avatar-dimensions($avatar-base-size-small)

	.wnl-avatar-medium
		+avatar-dimensions($avatar-base-size-medium)

	.wnl-avatar-large
		+avatar-dimensions($avatar-base-size-large)
</style>
<script>
	import { mapGetters } from 'vuex'

	export default {
		name: 'Avatar',
		props: ['username', 'size'],
		computed: {
			...mapGetters([
				'currentUserFullName',
			]),
			usernameToUse() {
				return this.username || this.currentUserFullName
			},
			isCustom() {
				return false // TODO: Fix when images are available
			},
			sizeClass() {
				// large = 64x64px, medium = 32x32px, small = 16x16px
				let size = this.size || 'medium'
				return 'wnl-avatar-' + size
			},
			initials() {
				return global.$fn.getInitials(this.username)
			},
			colorClass() {
				let colorPosition = (this.initials.charCodeAt(0) - 65) % 19 + 1
				return 'wnl-avatar-color-' + colorPosition
			}
		}
	}
</script>
