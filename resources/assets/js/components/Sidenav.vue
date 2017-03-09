<template>
	<aside class="wnl-sidenav wnl-left-content">
		<!-- Navigation Breadcrumbs -->
		<ul class="wnl-sidenav-breadcrumbs" v-if="breadcrumbs">
			<wnl-sidenav-item v-for="breadcrumb in breadcrumbs"
				:ancestors="breadcrumb.ancestors"
				:type="breadcrumb.type"
				:id="breadcrumb.id"
				:meta="breadcrumb.meta">
				{{ breadcrumb.name }}
			</wnl-sidenav-item>
		</ul>
		<!-- Navigation Structure -->
		<ul class="wnl-sidenav-items" v-if="items">
			<wnl-sidenav-item v-for="item in items"
				:ancestors="item.ancestors"
				:type="item.type"
				:id="item.id"
				:meta="item.meta">
				{{ item.name }}
			</wnl-sidenav-item>
		</ul>
	</aside>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		max-width: 250px

	.wnl-sidenav-breadcrumbs
		border-bottom: $border-light-gray
		font-size: $font-size-minus-1
		font-weight: $font-weight-bold
		margin-bottom: 10px

		.wnl-sidenav-item-groups
			font-size: $font-size-minus-2
</style>

<script>
	import * as mutations from 'js/store/mutations-types'
	import { mapGetters, mapMutations } from 'vuex'
	import SidenavItem from './SidenavItem.vue'

	export default {
		name: 'Sidenav',
		props: ['apiUrl'],
		computed: {
			...mapGetters([
				'breadcrumbs',
				'items',
			])
		},
		components: {
			'wnl-sidenav-item': SidenavItem
		},
		methods: {
			// Move it to an action
			...mapMutations([mutations.SET_NAVIGATION])
		},
		created() {
			axios.get(this.apiUrl).then((response) => {
				this[mutations.SET_NAVIGATION](response.data)
			})
		}
	}
</script>
