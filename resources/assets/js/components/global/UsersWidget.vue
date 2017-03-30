<template>
	<div class="wnl-users-widget">
		<p class="metadata">
			<span class="icon is-small">
				<i class="fa fa-users"></i>
			</span> Uczą się razem z Tobą
		</p>
		<wnl-avatar v-for="user in users" :username="user.full_name"></wnl-avatar>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-users-widget
		border-bottom: $border-light-gray
		padding: 20px 0 10px

		.metadata
			color: $color-background-gray
			margin-bottom: $margin-small

			& .icon
				color: $color-inactive-gray

		.wnl-avatar
			display: inline-flex
			margin-right: 10px
</style>

<script>
	import Avatar from './Avatar.vue'

	export default {
		name: 'UsersWidget',
		props: ['users'],
		components: {
			'wnl-avatar': Avatar,
		},
	}
</script>
