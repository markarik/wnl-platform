<template>
	<ul class="autocomplete-box" v-if="hasItems">
		<li
			class="autocomplete-box__item"
			v-for="item in items"
			@click="onItemClicked(item)"
		>
			{{ item.full_name }}
		</li>
	</ul>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.autocomplete-box
		background: #fff
		border: 1px solid #ccc
		color: #666
		position: fixed
		top: 50%
		right: 100px

		&__item
			padding: 10px 15px
</style>

<script>
	export default {
		name: 'Autocomplete',
		props: ['items', 'onItemChosen'],
		computed: {
			hasItems() {
				return this.items && this.items.length
			},
		},
		methods: {
			onItemClicked(item) {
				this.onItemChosen(item)
			}
		}
	}
</script>
