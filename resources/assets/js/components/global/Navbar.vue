<template>
	<nav class="nav has-shadow">
		<div class="columns wnl-nav-container">
			<div class="column wnl-nav-column">
				<div class="wnl-left-content wnl-nav-item">
					<!-- TODO: Load data dynamically -->
					<a class="wnl-logo-link" href="wnl-platform.app/course/1">
						<img src="http://wnl-platform.app/images/wnl-logo.svg" alt="Logo Więcej niż LEK">
					</a>
				</div>
			</div>
			<div class="column is-half wnl-nav-column">

			</div>
			<div class="column wnl-nav-column">
				<div class="nav-menu wnl-nav-item">
					{{ currentUserName }}
				</div>
			</div>
		</div>
	</nav>
</template>

<style lang="sass">
	.wnl-nav-column
		height: 100%

	.wnl-nav-item
		align-items: center
		display: flex
		height: 100%

	.wnl-nav-container
		width: 100%

	.wnl-logo-link
		display: block
		max-width: 175px

		img
			display: block
</style>

<script>
	export default {
		name: 'Navbar',
		computed: {
			currentUserName () {
				const currentUser = this.$store.state.users.current
				return currentUser.first_name + ' ' + currentUser.last_name
			}
		}
	}
</script>
