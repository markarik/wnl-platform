<template>
	<div
		v-if="roles.length"
		class="wnl-user-signature"
		:class="size"
	>
		<img :src="badgeUrl">
		{{$t('user.userProfile.signatures.wnlCrew')}}
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-user-signature
		text-transform: uppercase
		font-weight: $font-weight-bold
		line-height: initial
		color: #6F7285

		&.regular
			height: 10px
			font-size: $font-size-minus-3

			img
				height: 8px

		&.big
			height: 24px
			font-size: $font-size-plus-2

			img
				height: 20px

</style>

<script>
import { getImageUrl  } from 'js/utils/env';
import { ROLES } from 'js/consts/user';

const VALID_SIZES = ['regular', 'big'];

export default {
	props: {
		'roles': {
			type: Array,
			default: () => ['moderator'],
		},
		'size': {
			type: String,
			default: 'regular',
			validator: (size) => VALID_SIZES.indexOf(size) !== -1
		}
	},
	computed: {
		badgeUrl() {
			return getImageUrl('wnl-crew-badge.svg');
		},
		isModerator() {
			return this.roles.indexOf(ROLES.MODERATOR) !== -1;
		},
	}
};
</script>
