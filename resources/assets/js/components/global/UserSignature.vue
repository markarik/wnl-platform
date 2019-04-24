<template>
	<div class="wnl-user-badge">
		<img :src="badgeUrl">
		{{$t('user.userProfile.signatures.wnlCrew')}}
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-user-badge
		height: 10px
		text-transform: uppercase
		font-weight: $font-weight-black
		color: #6F7285
		font-size: $font-size-minus-3
		line-height: initial
		img
			height: 8px
</style>

<script>
import { getImageUrl } from 'js/utils/env';

export default {
	computed: {
		badgeUrl() {
			return getImageUrl('wnl-crew-badge.svg');
		},
	}
};
</script>
