<template>
	<ul
		class="autocomplete-box"
		v-bind:class="{'is-down': isDown}"
		v-show="hasItems"
		tabindex="-1"
		@keydown="onKeyDown"
	>
		<li
			class="autocomplete-box__item"
			v-for="item in items"
			@click="onItemChosen(item)"
			v-bind:class="{ active: item.active }"
			v-bind:key="item.id"
		>
			<component :is='itemComponent' :item="item"></component>
		</li>
	</ul>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.autocomplete-box
		background: $autocomplete-box-background
		border: $autocomplete-box-border
		box-shadow: $autocomplete-box-shadow
		bottom: 44px
		color: $autocomplete-text-color
		left: 0
		max-width: 300px
		position: absolute
		width: 100%
		z-index: $z-index-autocomplete

		&__text
			padding: 5px 10px

		&.is-down
			bottom: auto
			top: 44px

		&:focus
			outline: none

		&__item
			align-items: center
			cursor: pointer
			display: flex
			font-size: 12px
			font-weight: 900
			padding: 8px 10px
			text-align: left

			&:hover,
			&.active
				background: $autocomplete-active-item-background
				color: $autocomplete-active-item-text-color

		&__text
			padding: 5px 10px
</style>

<script>
	import UserAutocomplete from 'js/components/global/UserAutocompleteItem'
	import TagAutocomplete from 'js/components/global/TagAutocompleteItem'

	export default {
		name: 'Autocomplete',
		props: ['items', 'onItemChosen', 'itemComponent', 'isDown'],
		components: {
			'wnl-tag-autocomplete-item': TagAutocomplete,
			'wnl-user-autocomplete-item': UserAutocomplete
		},
		computed: {
			hasItems() {
				return this.items && this.items.length
			},
		},
		methods: {
			onKeyDown(evt) {
				switch (evt.keyCode){
					case 38:
						evt.stopPropagation()
						this.onArrowUp(evt)
						break
					case 40:
						evt.stopPropagation()
						this.onArrowDown(evt)
						break
					case 13:
						this.onEnter(evt)
						break
					case 27:
						this.$emit('close')
						break
				}
			},
			onArrowUp() {
				if (!this.hasItems) return

				const activeIndex = this.getActiveItem();
				if (activeIndex <= 0) {
					this.$set(this.items[this.items.length - 1], 'active', true);
				} else {
					this.$set(this.items[activeIndex - 1], 'active', true)
				}

				if (activeIndex >= 0) this.$set(this.items[activeIndex], 'active', false)

				//Something would steal the focus back to the Quill input when if we'd do it synchronously
				this.$nextTick(() => { this.$el.focus(); })
			},
			onArrowDown() {
				if (!this.hasItems) return

				const activeIndex = this.getActiveItem();

				if (activeIndex < 0 || activeIndex === this.items.length - 1) {
					this.$set(this.items[0], 'active', true)
				} else {
					this.$set(this.items[activeIndex + 1], 'active', true)
				}

				if (activeIndex > -1) this.$set(this.items[activeIndex], 'active', false)

				//Something would steal the focus back to the Quill input when if we'd do it synchronously
				this.$nextTick(() => { this.$el.focus(); })
			},

			onEnter(evt) {
				const activeIndex = this.getActiveItem();

				if (activeIndex < 0) return

				this.$set(this.items[activeIndex], 'active', false)
				this.onItemChosen(this.items[activeIndex])

				evt.preventDefault();
				evt.stopPropagation();
				return false
			},

			getActiveItem() {
				return this.items.findIndex((item) => item.active)
			}
		}
	}
</script>
