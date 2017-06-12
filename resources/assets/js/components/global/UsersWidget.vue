<template>
	<div class="wnl-users-widget">
		<p class="metadata">
			{{chatTitle}}
		</p>
		<!-- <p class="metadata">
			<span class="icon is-small">
				<i class="fa fa-users"></i>
			</span> Uczą się razem z Tobą
		</p>
		<div class="avatars-container">
			<wnl-avatar v-for="user in otherUsers" :username="user.full_name"></wnl-avatar>
		</div> -->
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-users-widget
		+white-shadow()

		border-bottom: $border-light-gray
		padding: 20px 0 10px
		z-index: 10

		.metadata
			color: $color-background-gray
			display: flex
			justify-content: space-between
			margin-bottom: $margin-small

			& .icon
				color: $color-inactive-gray

		.wnl-avatar
			display: inline-flex
			margin-right: 10px
			white-space: nowrap

	.avatars-container
		height: map-get($rounded-square-sizes, 'medium')
		overflow-x: hidden
</style>

<script>
	import Avatar from './Avatar.vue'
	import { mapActions, mapGetters } from 'vuex'

	export default {
		name: 'UsersWidget',
		props: ['users'],
		computed: {
			...mapGetters(['currentUserId']),
			otherUsers() {
				return this.users.filter((user) => user.id !== this.currentUserId)
			},
			chatTitle() {
				if (this.$route.name === 'courses') {
					return 'Czat kursu'
				} else {
					return 'Czat modułu'
				}
			}
		},
		components: {
			'wnl-avatar': Avatar,
		}
	}
</script>
