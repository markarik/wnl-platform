<template>
	<aside class="menu">
		<p class="menu-label">
			Lekcje
		</p>
		<ul class="menu-list">
			<li><router-link :to="{ name: 'lessons' }">Edycja lekcji</router-link></li>
		</ul>
		<!-- <p class="menu-label">
		Administration
		</p>
		<ul class="menu-list">
			<li><a>Team Settings</a></li>
			<li>
				<a class="is-active">Manage Your Team</a>
				<ul>
					<li><a>Members</a></li>
					<li><a>Plugins</a></li>
					<li><a>Add a member</a></li>
				</ul>
			</li>
			<li><a>Invitations</a></li>
			<li><a>Cloud Storage Environment Settings</a></li>
			<li><a>Authentication</a></li>
		</ul>
		<p class="menu-label">
			Transactions
		</p>
		<ul class="menu-list">
			<li><a>Payments</a></li>
			<li><a>Transfers</a></li>
			<li><a>Balance</a></li>
		</ul> -->
	</aside>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
	export default {
		name: 'Sidenav',
	}
</script>
