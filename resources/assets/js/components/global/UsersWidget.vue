<template>
	<div class="wnl-users-widget">
		<wnl-avatar v-for="user in users" :username="user.full_name"></wnl-avatar>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-users-widget
		height: 50px
		border-bottom: $border-light-gray
		padding: 10px 0

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