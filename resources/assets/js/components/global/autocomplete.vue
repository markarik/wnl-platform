<template>
	<ul
		class="autocomplete-box"
		v-if="hasItems"
		tabindex="-1"
		@keydown="onKeyDown"
	>
		<li
			class="autocomplete-box__item"
			v-for="item in itemsForDisplay"
			@click="onItemClicked(item)"
			v-bind:class="{ active: item.active }"
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

		&:focus
			outline: none

		&__item
			cursor: pointer
			padding: 10px 15px
			display: flex

			&:hover,
			&.active
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
			itemsForDisplay() {
				return this.items
			}
		},
		methods: {
			onItemClicked(item) {
				this.onItemChosen(item)
			},
			onKeyDown(evt) {
				switch (evt.keyCode){
					case 38:
						this.onArrowUp(evt)
						break
					case 40:
						this.onArrowDown(evt)
						break
					case 13:
						this.onEnter(evt)
						break
				}
			},
			onArrowUp() {
				if (!this.items || !this.items.length) return

				const activeItem = _.find(this.items, { active: true });
				if (!activeItem || activeItem === this.items[0]) {
					this.$set(this.items[this.items.length - 1], 'active', true);
				} else {
					this.$set(this.items[this.items.indexOf(activeItem) - 1], 'active', true)
				}

				if (activeItem) this.$set(activeItem, 'active', false)

				//Something would steal the focus back to the Quill input when if we'd do it synchronously
				this.$nextTick(() => { this.$el.focus(); })
			},
			onArrowDown() {
				if (!this.items || !this.items.length) return

				const activeItem = _.find(this.items, { active: true });

				if (!activeItem || activeItem === this.items[this.items.length - 1]) {
					this.$set(this.items[0], 'active', true)
				} else {
					this.$set(this.items[this.items.indexOf(activeItem) + 1], 'active', true)
				}

				if (activeItem) this.$set(activeItem, 'active', false)

				//Something would steal the focus back to the Quill input when if we'd do it synchronously
				this.$nextTick(() => { this.$el.focus(); })
			},

			onEnter(evt) {
				const activeItem = _.find(this.items, { active: true });

				if (!activeItem) return

				this.$set(activeItem, 'active', false)
				this.onItemClicked(activeItem)

				evt.preventDefault();
				evt.stopPropagation();
				return false
			},

			onEsc(evt) {
				this.$set(this, 'items', null)
			}
		}
	}
</script>
