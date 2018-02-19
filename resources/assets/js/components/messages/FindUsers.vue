<template lang="html">
	<div class="wnl-find-users">

		<div class="wnl-find-users-input">
			<input
				:placeholder="$t('messages.search.placeholder')"
				v-model="textInputValue"
				@input="onInput"
				@keydown="onKeyDown"
				ref="input"
			/>
		</div>

		<wnl-users-autocomplete
			class="scrollable-list"
			:items="results"
			ref="autocomplete"
			@close="onClose"
		/>

	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'


	.wnl-find-users
		overflow-x: hidden
		overflow-y: auto
		&-input
			display: flex
			align-items: center
			width: 100%
			overflow: hidden

			input
				+simple-input
				width: 90%
				margin: auto
				text-align: left
				margin-top: $margin-small
				margin-bottom: $margin-small
				min-height: $margin-big

				&:focus
					outline: none

</style>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import {getApiUrl} from 'js/utils/env'
	import UsersAutocomplete from 'js/components/messages/UsersAutocomplete'

	const KEYS = {
		enter: 13,
		esc: 27,
		arrowUp: 38,
		arrowDown: 40,
	}

	export default {
		name: 'FindUsers',
		components: {
			'wnl-users-autocomplete': UsersAutocomplete
		},
		data() {
			return {
				results: () => [],
				textInputValue: ''
			}
		},
		methods: {
			onInput: _.debounce(function ({target: {value}}) {
				if (value.length === 0) return

				const query = encodeURIComponent(value)
				axios.get(getApiUrl(`user_profiles/.search?q=${query}`))
					.then(res => {
						if (res.data.length === 0) return
						this.results = res.data
					})
			}, 300),
			onKeyDown(evt) {
				const { enter, arrowUp, arrowDown, esc } = KEYS

				if (this.results.length === 0) {
					return
				}

				if (evt.keyCode === esc) {
					this.onClose()
					return
				}

				if ([enter, arrowUp, arrowDown].indexOf(evt.keyCode) === -1) {
					return
				}

				this.$refs.autocomplete.onKeyDown(evt)
				this.killEvent(evt)

				//for some of the old browsers, returning false
				// is the true way to kill propagation
				return false
			},
			killEvent(evt) {
				evt.preventDefault()
				evt.stopPropagation()
			},
			onClose() {
				this.textInputValue = ''
				this.$emit('close')
			},
		},
		mounted(){
			this.$refs.input.focus()
		}
	}
</script>
