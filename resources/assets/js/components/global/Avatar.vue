<template>
	<div class="wnl-avatar" :class="[sizeClass, colorClass]">
		<img :title="usernameToUse" class="wnl-avatar-custom" v-if="isCustom">
		<div :title="usernameToUse" class="wnl-avatar-automatic" v-else>{{ initials }}</div>
	</div>
</template>
<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	+rounded-square-standard-sizes('avatar')

	// Variables
	$avatars-colors-list: #1abc9c, #2ecc71, #3498db, #9b59b6, #34495e, #16a085, #27ae60, #2980b9, #8e44ad, #2c3e50, #f1c40f, #e67e22, #e74c3c, #95a5a6, #f39c12, #d35400, #c0392b, #bdc3c7, #7f8c8d

	@for $i from 1 to 19
		.wnl-avatar-color-#{$i}
			background-color: nth($avatars-colors-list, $i)

	.wnl-avatar-automatic
		color: $color-white
		font-weight: $font-weight-black
</style>
<script>
	import { mapGetters } from 'vuex'
	import { getInitials } from 'js/utils/strings'

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
				// large = 50x50px, medium = 30x30px, small = 20x20px
				let size = this.size || 'medium'
				return `wnl-avatar-${size}`
			},
			initials() {
				return getInitials(this.usernameToUse)
			},
			colorClass() {
				if (!this.isCustom) {
					let colorPosition = (this.initials.charCodeAt(0) - 65) % 19 + 1
					return `wnl-avatar-color-${colorPosition}`
				}
				return ''
			}
		}
	}
</script>
