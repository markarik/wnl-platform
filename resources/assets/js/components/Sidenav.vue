<template>
	<aside class="wnl-sidenav wnl-left-content">
		<!-- Navigation Breadcrumbs -->
		<ul class="wnl-sidenav-breadcrumbs" v-if="breadcrumbs">
			<wnl-sidenav-item v-for="breadcrumb in breadcrumbs"
				:ancestors="breadcrumb.ancestors"
				:type="breadcrumb.type"
				:id="breadcrumb.id">
				{{ breadcrumb.name }}
			</wnl-sidenav-item>
		</ul>
		<!-- Navigation Structure -->
		<ul class="wnl-sidenav-items" v-if="items">
			<wnl-sidenav-item v-for="item in items"
				:ancestors="item.ancestors"
				:type="item.type"
				:id="item.id">
				{{ item.name }}
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
</style>

<script>
	import SidenavItem from './SidenavItem.vue'

	export default {
		name: 'Sidenav',
		props: ['apiUrl'],
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
			setNavigation: function (data) {
				this.$store.dispatch('setNavigation', data)
			}
		},
		created: function () {
			axios.get(this.apiUrl).then((response) => {
				this.setNavigation(response.data)
			})
		}
	}
</script>
