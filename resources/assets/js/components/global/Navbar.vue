<template>
	<nav class="wnl-navbar has-shadow" :class="{'is-desktop': !isTouchScreen}">
		<div class="wnl-navbar-item wnl-navbar-sidenav-toggle" v-if="canShowSidenavTrigger">
			<a class="wnl-navbar-sidenav-trigger" @click="toggleSidenav">
				<span class="icon">
					<i class="fa" :class="sidenavIconClass"></i>
				</span>
			</a>
		</div>
		<div class="wnl-navbar-branding">
			<router-link :to="{ name: 'dashboard' }" class="wnl-logo-link">
				<img class="logo-image" :src="logoSrc" :alt="$t('nav.navbar.logoAlt')">
				<img v-if="!isMobile" class="logo-text" :src="logoTextSrc" :alt="$t('nav.navbar.logoAlt')">
			</router-link>
		</div>
		<!-- <div class="wnl-navbar-signup" v-if="!isMobile">
			<a target="_blank" :href="signUpLink" class="button is-success is-small is-outlined">
				<span>Zapisz się</span>&nbsp;
				<span class="icon is-small">
					<i class="fa fa-thumbs-o-up"></i>
				</span>
			</a>
		</div> -->
		<div
			v-if="$currentEditionParticipant.isAllowed('access')"
			class="wnl-navbar-item wnl-navbar-search"
		>
			<wnl-search/>
		</div>
		<div
			v-if="$currentEditionParticipant.isAllowed('access')"
			class="wnl-navbar-item wnl-navbar-feed">
			<wnl-personal-feed/>
		</div>
		<div
			class="wnl-navbar-item wnl-navbar-messages"
			v-if="$currentEditionParticipant.isAllowed('access')"
		>
			<wnl-chat-feed/>
		</div>
		<div class="wnl-navbar-item wnl-navbar-profile">
			<wnl-user-dropdown/>
		</div>
		<div class="wnl-navbar-item wnl-navbar-chat-toggle" v-if="canShowChatToggleInNavbar">
			<span class="icon is-big"><i class="fa" :class="chatIconClass" @click="toggleChat"></i></span>
		</div>
	</nav>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$logo-width: 50px
	$logo-text-width: 90px

	.modal-active .wnl-navbar
		z-index: 0

	.wnl-navbar
		+small-shadow()
		display: flex
		flex: 0 $navbar-height
		z-index: $z-index-navbar

		&.is-desktop
			padding: 0 $margin-medium

			.wnl-navbar-branding
				padding: 0

			.wnl-navbar-item:hover
				background-color: $color-background-light-gray
				transition: background $transition-length-base

			.wnl-logo-link
				max-width: $logo-width + $logo-text-width + $margin-small

			.wnl-navbar-item
				width: $navbar-height + 2 * $margin-tiny

			.wnl-navbar-profile
				width: $navbar-height + 2 * $margin-small

			.wnl-navbar-feed,
			.wnl-navbar-search
				width: $navbar-height

	.wnl-navbar-profile
		margin-left: $margin-small

	.wnl-navbar-branding
		justify-content: flex-start
		flex-grow: 1
		padding: 0 $margin-small

	.wnl-navbar-signup
		+flex-center()
		cursor: pointer
		height: $navbar-height
		min-height: $navbar-height

	.wnl-navbar-item
		align-items: center
		cursor: pointer
		display: flex
		height: $navbar-height
		justify-content: center
		min-height: $navbar-height
		transition: background $transition-length-base
		width: $navbar-height - 2 * $margin-small

		.icon
			color: $color-gray

	.wnl-navbar-sidenav-toggle
		padding-left: 0

	.wnl-navbar-chat-toggle
		padding-right: 0

	.wnl-logo-link
		align-items: center
		display: flex
		height: $navbar-height
		max-width: $logo-width

		.logo-image,
		.logo-text
			display: block

		.logo-image
			width: $logo-width

		.logo-text
			margin-left: $margin-small
			width: $logo-text-width

	.wnl-right
		height: 100%
</style>

<script>
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';

import Search from 'js/components/global/search/Search';
import UserDropdown from 'js/components/user/UserDropdown.vue';
import PersonalFeed from 'js/components/notifications/feeds/personal/PersonalFeed';
import ChatFeed from 'js/components/notifications/feeds/chat/ChatFeed';
import { mapGetters, mapActions } from 'vuex';
import { getImageUrl, getUrl } from 'js/utils/env';

export default {
	name: 'Navbar',
	perimeters: [currentEditionParticipant],
	components: {
		'wnl-user-dropdown': UserDropdown,
		'wnl-personal-feed': PersonalFeed,
		'wnl-search': Search,
		'wnl-chat-feed': ChatFeed,
	},
	computed: {
		...mapGetters([
			'canShowChatToggleInNavbar',
			'canShowControlsInNavbar',
			'canShowSidenavTrigger',
			'currentUserFullName',
			'isChatVisible',
			'isMobile',
			'isTouchScreen',
			'isSidenavOpen',
		]),
		chatIconClass() {
			return this.isChatVisible ? 'fa-close' : 'fa-comments-o';
		},
		logoSrc() {
			return getImageUrl('wnl-logo-image.svg');
		},
		logoTextSrc() {
			return getImageUrl('wnl-logo-text.svg');
		},
		sidenavIconClass() {
			return this.isSidenavOpen ? 'fa-close' : 'fa-bars';
		},
		signUpLink() {
			return getUrl('payment/select-product');
		},
	},
	methods: {
		...mapActions(['toggleSidenav', 'toggleChat'])
	}
};
</script>
