<template>
	<div class="has-text-centered">
		<h2 class="title">Obsługa platformy 👨🏻‍🏫</h2>
		<p class="title is-5">Zanim wylądujesz na Dashboardzie, zapoznaj się z nim w 3 minuty!</p>
		<div class="onboarding-video-wrapper margin-top-huge">
			<iframe
				class="onboarding-video"
				src="https://www.youtube.com/embed/Gttl8H08C3E"
				frameborder="0" allow="encrypted-media" allowfullscreen
			></iframe>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.onboarding-video-wrapper
		max-width: 700px
		padding-bottom: 56.25%
		position: relative

	.onboarding-video
		height: 100%
		left: 0
		position: absolute
		width: 100%
</style>
