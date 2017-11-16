<template>
	<div>
		<input
			:value="valueComputed"
			:class="{'is-empty': valueComputed.length === 0, 'full-height': true}"
			@focus="onOpen"
			@input="onInput"
			@keydown="onKeyDown"
		/>
		<wnl-autocomplete
			v-if="show"
			:items="usersListFiltered"
			:onItemChosen="onItemChosen"
			:itemComponent="'wnl-user-autocomplete-item'"
			@close="onClose"
			class="wnl-autocomplete-dropdown"
			ref="autocomplete"
		/>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.full-height
		height: 100%

	.wnl-autocomplete-dropdown
		top: 100%
		height: 250px
		overflow-y: auto

	.is-empty
		border: 2px solid $color-yellow
		border-radius: 5px
</style>

<script>
const keys = {
	enter: 13,
	esc: 27,
	arrowUp: 38,
	arrowDown: 40,
}

import Autocomplete from 'js/components/global/Autocomplete'

export default {
	data() {
		return {
			focused: false,
			textInputValue: ''
		}
	},
	props: {
		usersList: {
			type: Array,
			required: true
		},
		onItemChosen: {
			type: Function,
			required: true
		},
		show: {
			type: Boolean,
			default: false
		},
		initialValue: {
			type: String,
			default: ''
		}
	},
	components: {
		'wnl-autocomplete': Autocomplete
	},
	computed: {
		valueComputed() {
			return this.focused ? this.textInputValue : this.initialValue
		},
		usersListFiltered() {
			return this.usersList.filter(moderator => (
				moderator.full_name.toLowerCase().indexOf(this.textInputValue.toLowerCase()) > -1)
			)
		},
	},
	methods: {
		onKeyDown(evt) {
			const { enter, arrowUp, arrowDown, esc } = keys

			if (this.usersList.length === 0) {
				this.$emit('close')
				return
			}

			if (evt.keyCode === esc) {
				this.onClose()
				return
			}
			if ([enter, arrowUp, arrowDown].indexOf(evt.keyCode) === -1) {
				this.onOpen()
				return
			}

			this.$refs.autocomplete.onKeyDown(evt)
			this.killEvent(evt)

			//for some of the old browsers, returning false is the true way to kill propagation
			return false
		},
		killEvent(evt) {
			evt.preventDefault()
			evt.stopPropagation()
		},
		onClose() {
			this.focused = false
			this.textInputValue = ''
			this.$emit('close')
		},
		onOpen() {
			this.focused = true
			this.$emit('show')
		},
		onInput(event) {
			this.textInputValue = event.target.value
		},
	},
	watch: {
		show(newValue) {
			this.focused = newValue
		}
	}
}
</script>
