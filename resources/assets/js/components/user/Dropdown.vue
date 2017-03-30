<template>
	<div class="wnl-dropdown">
		<div class="activator" :class="{ 'is-active' : isActive }" @click="isActive = !isActive">
			<wnl-avatar></wnl-avatar>
			<span class="icon is-small">
				<i class="fa fa-chevron-down"></i>
			</span>
			<div class="box drawer" v-if="isActive">
				<ul>
					<!-- <li @click="isActive = !isActive">
						<router-link :to="{name: 'my-orders'}">Twoje zamówienia</router-link>
					</li>
					<li @click="isActive = !isActive">
						<router-link :to="{name: 'my-orders'}">Wyloguj się</router-link>
					</li> -->
					<li v-for="item in items" class="drawer-item">
						<router-link class="drawer-link" :to="{ name: item.route }">{{item.text}}</router-link>
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.activator
		align-items: center
		color: $color-inactive-gray
		display: flex
		justify-content: center
		position: relative

		&.is-active
			color: $color-gray

		.icon
			margin: 0 $margin-small

	.drawer
		position: absolute
		top: 120%

	.drawer-item
		border-bottom: $border-light-gray
		font-size: $font-size-minus-1
		padding: $margin-small 0
		white-space: nowrap

		&:last-child
			border: 0

	.drawer-link,
	.drawer-link.is-active
		font-weight: $font-weight-regular
</style>

<script>
	import { set } from 'vue'

	export default {
		name: 'Dropdown',
		data() {
			return {
				isActive: false,
			}
		},
		computed: {
			items() {
				return [
					{
						'text': 'Kurs',
						'route': 'dashboard',
					},
					{
						'text': 'Twoje zamówienia',
						'route': 'my-orders',
					},
					{
						'text': 'Wyloguj się',
						'route': '',
					},
				]
			}
		},
	}
</script>
