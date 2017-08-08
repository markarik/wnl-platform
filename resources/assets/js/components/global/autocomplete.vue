<template>
	<ul class="autocomplete-box" v-if="hasItems">
		<li
			class="autocomplete-box__item"
			v-for="item in items"
			@click="onItemClicked(item)"
		>
			<div class="autocomplete-box__avatar">
				<wnl-avatar :fullName="item.full_name" :url="item.avatar"></wnl-avatar>
			</div>
			<div class="autocomplete-box__text">
				{{ item.full_name }}
			</div>
		</li>
	</ul>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.autocomplete-box
		background: #fff
		border: $border-light-gray
		bottom: 44px
		color: #666
		left: 0
		position: absolute
		width: 100%

		&__item
			cursor: pointer
			padding: 10px 15px
			display: flex

			&:hover
				background: #f9f9f9

		&__text
			padding: 5px 10px

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
