<template>
	<div class="wnl-main-nav wnl-column" :class="{ 'horizontal': isHorizontal }">
		<router-link
			v-if="isOnboardingFinished"
			class="wnl-main-nav-item"
			:to="{ name: 'courses', params: { courseId: 1, keepsNavOpen: true }}"
		>
			<span class="icon is-medium">
				<i class="fa fa-home" />
			</span>
			<span class="text">Kurs</span>
		</router-link>
		<router-link
			v-if="!isOnboardingFinished"
			class="wnl-main-nav-item"
			:to="{ name: 'onboarding' }"
		>
			<span class="icon is-medium">
				<i class="fa fa-play" />
			</span>
			<span class="text">Start</span>
		</router-link>
		<router-link
			v-if="$currentEditionParticipant.isAllowed('access') && isOnboardingFinished"
			class="wnl-main-nav-item"
			:to="{ name: 'collections', params: { keepsNavOpen: true } }"
		>
			<span class="icon is-medium">
				<i class="fa fa-star-o" />
			</span>
			<span class="text">Kolekcje</span>
		</router-link>
		<router-link
			v-if="$currentEditionParticipant.isAllowed('access') && isOnboardingFinished"
			class="wnl-main-nav-item"
			:to="{name: 'questions-dashboard', params: { keepsNavOpen: true } }"
		>
			<span class="icon is-medium">
				<i class="fa fa-check-square-o" />
			</span>
			<span class="text">{{$t('nav.sideNav.questions')}}</span>
		</router-link>
		<router-link
			class="wnl-main-nav-item"
			:to="{ name: 'myself', params: { keepsNavOpen: true } }"
		>
			<span class="icon is-medium">
				<i class="fa fa-user-o" />
			</span>
			<span class="text">Konto</span>
		</router-link>
		<router-link
			class="wnl-main-nav-item"
			:to="{ name: 'help', params: { keepsNavOpen: true } }"
		>
			<span class="icon is-medium">
				<i class="fa fa-heartbeat" />
			</span>
			<span class="text">Pomoc</span>
		</router-link>
		<a
			v-if="!currentUserHasLatestProduct && !isMobile && getCurrentCourseProductSignupsOpen"
			class="wnl-main-nav-item"
			:href="signUpLink"
		>
			<span class="icon is-medium">
				<i class="fa fa-thumbs-o-up" />
			</span>
			<span class="text">Zapisz się!</span>
		</a>
		<router-link
			v-if="$moderatorFeatures.isAllowed('access') && isOnboardingFinished"
			class="wnl-main-nav-item"
			:to="{name: 'moderatorFeed'}"
		>
			<span class="icon is-medium">
				<i class="fa fa-list" />
			</span>
			<span class="text">Feed</span>
		</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-main-nav
		border-right: $border-light-gray
		display: flex
		flex-direction: column
		max-width: $main-nav-max-size
		min-width: $main-nav-min-size
		width: $main-nav-size
		overflow-x: hidden

		&.horizontal
			border-bottom: $border-light-gray
			flex-direction: row
			max-width: 100%
			min-width: initial
			width: 100%
			max-height: $main-nav-max-size
			min-height: $main-nav-min-size
			height: $main-nav-size

			.icon
				height: 1.5rem
				width: 1.5rem

				.fa
					font-size: 23px

		.wnl-main-nav-item
			align-items: center
			color: $color-gray
			display: flex
			flex-direction: column
			justify-content: center
			height: $main-nav-size
			min-height: $main-nav-min-size
			min-width: $main-nav-min-size
			max-height: $main-nav-max-size
			max-width: $main-nav-max-size
			transition: all $transition-length-base
			width: $main-nav-size

			&:hover,
			&.is-active
				background: $color-background-lighter-gray
				color: $color-ocean-blue
				font-weight: $font-weight-regular
				transition: all $transition-length-base

			.text
				font-size: $font-size-minus-3
				text-transform: uppercase
</style>

<script>
import { mapGetters } from 'vuex';
import moderatorFeatures from 'js/perimeters/moderator';
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';
import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import { getUrl } from 'js/utils/env';

export default {
	name: 'MainNav',
	props: ['isHorizontal'],
	perimeters: [moderatorFeatures, currentEditionParticipant, upcomingEditionParticipant],
	computed: {
		...mapGetters(['isOnboardingFinished', 'currentUserHasLatestProduct', 'isMobile']),
		...mapGetters('products', ['getCurrentCourseProductSignupsOpen']),
		signUpLink() {
			return getUrl('payment/account');
		},
	},
};
</script>
