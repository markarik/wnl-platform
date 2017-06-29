<template>
	<div class="wnl-main-nav wnl-column" v-bind:class="{ 'horizontal': isHorizontal }">
		<router-link class="wnl-main-nav-item" :to="{ name: 'courses', params: { courseId: 1 } }">
			<span class="icon is-medium">
				<i class="fa fa-home"></i>
			</span>
			<span class="text">Kurs</span>
		</router-link>
		<!-- <router-link class="wnl-main-nav-item" :to="{ name: 'collections' }">
			<span class="icon is-medium">
				<i class="fa fa-star-o"></i>
			</span>
			<span class="text">Kolekcje</span>
		</router-link> -->
		<router-link class="wnl-main-nav-item" :to="{ name: 'myself' }">
			<span class="icon is-medium">
				<i class="fa fa-user-o"></i>
			</span>
			<span class="text">Konto</span>
		</router-link>
		<router-link class="wnl-main-nav-item" :to="{ name: 'help' }">
			<span class="icon is-medium">
				<i class="fa fa-heartbeat"></i>
			</span>
			<span class="text">Pomoc</span>
		</router-link>
		<router-link
			class="wnl-main-nav-item"
			:to="{ name: 'moderatorsFeed' }"
			v-show="$moderatorFeed.isAllowed('route')"
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
		overflow-x: hidden;

		&.horizontal
			border-bottom: 1px solid $color-light-gray
			flex-direction: row
			max-width: 100%
			min-width: initial
			width: 100%
			max-height: $main-nav-max-size
			min-height: $main-nav-min-size
			height: $main-nav-size

		.wnl-main-nav-item
			align-items: center
			color: $color-gray-dimmed
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
				background: $color-background-light-gray
				color: $color-ocean-blue
				transition: all $transition-length-base

			.text
				font-size: $font-size-minus-3
				text-transform: uppercase
</style>

<script>
	import moderatorFeed from 'js/perimeters/moderatorFeed';

	export default {
		name: 'MainNav',
		props: ['isHorizontal'],
		perimeters: [
			moderatorFeed
		]
	}
</script>
