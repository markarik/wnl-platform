<template>
	<aside class="wnl-sidenav wnl-left-content">
		<!-- Navigation Breadcrumbs -->
		<ul class="wnl-sidenav-breadcrumbs">
			<wnl-sidenav-item v-for="breadcrumb in breadcrumbs" :url="breadcrumb.url" :type="breadcrumb.type">
				{{ breadcrumb.text }}
			</wnl-sidenav-item>
		</ul>
		<!-- Navigation Structure -->
		<ul class="wnl-sidenav-items">
			<wnl-sidenav-item v-for="item in items" :url="item.url" :type="item.type" :status="item.status">
				{{ item.text }}
			</wnl-sidenav-item>
		</ul>
	</aside>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		max-width: 250px
		padding: 20px

	.wnl-sidenav-breadcrumbs
		border-bottom: $border-light-gray
		font-size: $font-size-minus-2
		font-weight: $font-weight-bold
		margin-bottom: 10px

	.wnl-sidenav-items
		.wnl-sidenav-item-group

		.wnl-sidenav-item-lesson
			font-size: $font-size-minus-1

		.wnl-sidenav-item-section
			font-size: $font-size-minus-2
			padding-left: 10px
</style>

<script>
	import SidenavItem from './SidenavItem.vue'

	export default {
		name: 'Sidenav',
		props: {
			apiUrl: {
				type: String,
				default: '/papi/v1/courses/1/nav'
			}
		},
		computed: {
			breadcrumbs() {
				return this.$store.getters.breadcrumbs
			},
			items() {
				return this.$store.getters.items
			}
		},
		components: {
		'wnl-sidenav-item': SidenavItem
		},
		methods: {
			setNavigation: function (url) {
				this.$store.dispatch('setNavigation', url)
			}
		},
		created: function () {
			this.setNavigation(this.apiUrl)
		}
	}
</script>
