<template>
	<div class="wnl-avatar" :class="[sizeClass, colorClass, imageClass]">
		<img
			v-if="isCustom"
			:title="usernameToUse"
			:src="urlToUse"
			class="wnl-avatar-custom"
		>
		<div
			v-else
			:title="usernameToUse"
			class="wnl-avatar-automatic"
		>{{initials}}</div>
		<div v-if="showBadge" class="badge">
			<img :src="badgeUrl">
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	+rounded-square-standard-sizes('avatar')

	// Variables
	$avatars-colors-list: #1abc9c, #2ecc71, #3498db, #9b59b6, #34495e, #16a085, #27ae60, #2980b9, #8e44ad, #2c3e50, #f1c40f, #e67e22, #e74c3c, #f39c12, #d35400, #c0392b

	@for $i from 1 to 16
		.wnl-avatar-color-#{$i}
			background-color: nth($avatars-colors-list, $i)

	.wnl-avatar-automatic
		color: $color-white
		font-weight: $font-weight-black
		cursor: default

	.wnl-avatar
		overflow: hidden
		user-select: none
		position: relative

	.badge
		position: absolute
		bottom: 0
		right: 0
		background: $color-white

</style>
<script>
import _ from 'lodash';
import { mapGetters } from 'vuex';
import { getInitials } from 'js/utils/strings';
import { getImageUrl } from 'js/utils/env';
import { ROLES } from 'js/consts/user';

export default {
	name: 'Avatar',
	props: {
		'fullName': String,
		'size': String,
		'url': String,
		'roles': {
			type: Array,
			default: () => [],
		},
	},
	computed: {
		...mapGetters([
			'currentUserFullName',
			'currentUserAvatar',
			'isModerator'
		]),
		isCurrentUser() {
			return _.isEmpty(this.fullName);
		},
		isCustom() {
			return this.isCurrentUser ? this.currentUserAvatar !== null : !_.isEmpty(this.url);
		},
		usernameToUse() {
			return this.isCurrentUser ? this.currentUserFullName : this.fullName;
		},
		urlToUse() {
			return this.isCurrentUser ? this.currentUserAvatar : this.url;
		},
		sizeClass() {
			// large = 50x50px, medium = 30x30px, small = 20x20px
			let size = this.size || 'medium';
			return `wnl-avatar-${size}`;
		},
		initials() {
			return getInitials(this.usernameToUse);
		},
		colorClass() {
			if (!this.isCustom) {
				let colorPosition = (this.initials.charCodeAt(0) - 65) % 15 + 1;
				return `wnl-avatar-color-${colorPosition}`;
			}
			return '';
		},
		imageClass() {
			return this.isCustom ? 'with-image' : 'without-image';
		},
		badgeUrl() {
			return getImageUrl('wnl-crew-badge.svg');
		},
		showBadge() {
			return this.isCurrentUser ? this.isModerator : this.roles.indexOf(ROLES.MODERATOR) !== -1;
		},
	},
};
</script>
