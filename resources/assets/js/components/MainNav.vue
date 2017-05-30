<template>
	<div class="wnl-main-nav">
		<router-link class="wnl-main-nav-item" :to="{ name: 'courses', params: { courseId: 1 } }">
			<span class="icon is-medium">
				<i class="fa fa-home"></i>
			</span>
			<span class="text">Kurs</span>
		</router-link>
		<a class="wnl-main-nav-item">
			<span class="icon is-medium">
				<i class="fa fa-star"></i>
			</span>
			<span class="text">Kolekcje</span>
		</a>
		<a class="wnl-main-nav-item">
			<span class="icon is-medium">
				<i class="fa fa-pencil-square-o"></i>
			</span>
			<span class="text">Baza pytań</span>
		</a>
		<router-link class="wnl-main-nav-item" :to="{ name: 'myself' }">
			<span class="icon is-medium">
				<i class="fa fa-user"></i>
			</span>
			<span class="text">Konto</span>
		</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-main-nav
		border-right: $border-light-gray
		display: flex
		flex-direction: column
		height: 100%
		min-height: 100%
		min-width: 100px
		width: 100px

		.wnl-main-nav-item
			align-items: center
			color: $color-gray-dimmed
			display: flex
			flex-direction: column
			justify-content: center
			height: 100px
			transition: all $transition-length-base
			width: 100px

			&:hover,
			&.is-active
				background: $color-background-light-gray
				color: $color-ocean-blue
				transition: all $transition-length-base

			.text
				font-size: $font-size-minus-2
				text-transform: uppercase
</style>

<script>
	export default {
		name: 'MainNav'
	}
</script>
