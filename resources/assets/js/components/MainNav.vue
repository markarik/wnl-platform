<template>
	<div class="wnl-main-nav wnl-column" v-bind:class="{ 'horizontal': isHorizontal }">
		<router-link class="wnl-main-nav-item" :to="{ name: 'courses', params: { courseId: 1, keepsNavOpen: true }}">
			<span class="icon is-medium">
				<i class="fa fa-home"></i>
			</span>
			<span class="text">Kurs</span>
		</router-link>
		<router-link
			class="wnl-main-nav-item"
			:to="{ name: 'collections', params: { keepsNavOpen: true } }"
			v-if="$currentEditionParticipant.isAllowed('access')"
		>
			<span class="icon is-medium">
				<i class="fa fa-star-o"></i>
			</span>
			<span class="text">Kolekcje</span>
		</router-link>
		<router-link
			class="wnl-main-nav-item"
			:to="{name: 'questions-dashboard', params: { keepsNavOpen: true } }"
			v-if="$currentEditionParticipant.isAllowed('access')"
		>
			<span class="icon is-medium">
				<i class="fa fa-check-square-o"></i>
			</span>
		<span class="text">{{$t('nav.sideNav.questions')}}</span>
		</router-link>
		<router-link
			class="wnl-main-nav-item"
			:to="{ name: 'myself', params: { keepsNavOpen: true } }"
		>
			<span class="icon is-medium">
				<i class="fa fa-user-o"></i>
			</span>
			<span class="text">Konto</span>
		</router-link>
		<router-link
			class="wnl-main-nav-item"
			:to="{ name: 'help', params: { keepsNavOpen: true } }"
		>
			<span class="icon is-medium">
				<i class="fa fa-heartbeat"></i>
			</span>
			<span class="text">Pomoc</span>
		</router-link>
		<!--<a-->
			<!--v-if="!$upcomingEditionParticipant.isAllowed('access') && !currentUser.accountSuspended"-->
			<!--class="wnl-main-nav-item"-->
			<!--:href="signUpLink"-->
		<!--&gt;-->
			<!--<span class="icon is-medium">-->
				<!--<i class="fa fa-thumbs-o-up"></i>-->
			<!--</span>-->
			<!--<span class="text">Zapisz się!</span>-->
		<!--</a>-->
		<router-link
			class="wnl-main-nav-item"
			:to="{name: 'moderatorFeed'}"
			v-if="$moderatorFeatures.isAllowed('access')"
		>
			<span class="icon is-medium">
				<i class="fa fa-list"></i>
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
import {mapGetters} from 'vuex';
import moderatorFeatures from 'js/perimeters/moderator';
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';
import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import {getUrl} from 'js/utils/env';

export default {
	name: 'MainNav',
	props: ['isHorizontal'],
	perimeters: [moderatorFeatures, currentEditionParticipant, upcomingEditionParticipant],
	computed: {
		...mapGetters(['currentUser']),
		signUpLink() {
			return getUrl('payment/select-product');
		},
	},
};
</script>
