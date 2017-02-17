<template>
	<li :class="itemClass">
		<a :href="url" v-if="isLink" :class="linkClass">
			<slot></slot>
		</a>
		<span v-else>
			<slot></slot>
		</span>
	</li>
</template>

<style lang="sass">
	.wnl-sidenav-item
		padding: 5px 0
</style>

<script>
	export default {
		name: 'SidenavItem',
		props: ['type', 'url', 'status'],
		computed: {
			itemClass() {
				return 'wnl-sidenav-item wnl-sidenav-item-' + this.type
			},
			isLink() {
				return this.url != '#'
			},
			linkClass() {
				return 'wnl-lesson-' + this.status
			}
		}
	}
</script>
