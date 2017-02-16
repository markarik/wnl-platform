<template>
	<div>
		<aside class="wnl-sidenav">
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
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		padding: 20px

	.wnl-sidenav-breadcrumbs
		font-size: $font-size-minus-2
		font-weight: $font-weight-bold
		margin-bottom: 10px

	.wnl-sidenav-items
		.wnl-sidenav-item-group

		.wnl-sidenav-item-lesson
			font-size: $font-size-minus-1
			padding-left: 5px

		.wnl-sidenav-item-section
			padding-left: 10px
</style>

<script>
	import SidenavItem from './SidenavItem.vue'

	export default {
		name: 'Sidenav',
		components: {
		'wnl-sidenav-item': SidenavItem
		},
		data: () => {
			return {
				breadcrumbs: [
					{
						type: 'course',
						icon: 'course',
						url: 'http://wnl-platform.app/course/1',
						text: 'Więcej niż LEK'
					}
				],
				items: [
					{
						type: 'group',
						icon: 'group',
						url: '#',
						text: 'Interna'
					},
					{
						type: 'lesson',
						icon: 'lesson',
						url: 'http://wnl-platform.app/course/1/lesson/1',
						text: 'Pulmonologia I',
						status: 'done'
					},
					{
						type: 'lesson',
						icon: 'lesson',
						url: 'http://wnl-platform.app/course/1/lesson/2',
						text: 'Pulmonologia II',
						status: 'started'
					},
					{
						type: 'lesson',
						icon: 'lesson',
						url: 'http://wnl-platform.app/course/1/lesson/2',
						text: 'Kardiologia I',
						status: 'enabled'
					},
					{
						type: 'lesson',
						icon: 'lesson',
						url: 'http://wnl-platform.app/course/1/lesson/1',
						text: 'Kardiologia II',
						status: 'disabled'
					}
				]
			}
		}
	}
</script>
