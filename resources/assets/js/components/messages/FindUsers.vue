<template lang="html">
	<div class="wnl-find-users">
		<div class="wnl-find-users-input control" :class="{'is-loading': loading}">
			<input
				:placeholder="$t('messages.search.placeholder')"
				v-model="textInputValue"
				@input="onInput"
				@keydown="onKeyDown"
				ref="input"
			/>
		</div>
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
			width: 90%
			margin: auto
			overflow: hidden

			&.control::after
				top: 38%
				height: 2vh
				right: 0
				width: 2vh

			input
				+simple-input
				text-align: left
				width: 100%
				margin-top: $margin-small
				margin-bottom: $margin-small
				padding-right: $margin-base
				min-height: $margin-big

				&:focus
					outline: none

</style>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import {getApiUrl} from 'js/utils/env'

	const KEYS = {
		enter: 13,
		esc: 27,
		arrowUp: 38,
		arrowDown: 40,
	}

	export default {
		name: 'FindUsers',
		data() {
			return {
				textInputValue: '',
				loading: false,
			}
		},
		methods: {
			onInput: _.debounce(function ({target: {value}}) {
				if (value.length === 0) return

				this.loading = true
				const query = encodeURIComponent(value)
				axios.get(getApiUrl(`user_profiles/.search?q=${query}`))
					.then(res => {
						if (res.data.length === 0) return
						this.$set(res.data[0], 'active', true);
						this.$emit('updateItems', res.data)
						this.loading = false
					})
			}, 300),
			onKeyDown(evt) {
				const { enter, arrowUp, arrowDown, esc } = KEYS

				if (evt.keyCode === esc) {
					this.onClose()
					return
				}

				if ([enter, arrowUp, arrowDown].indexOf(evt.keyCode) === -1) {
					return
				}

				this.$emit('onKeyDown', evt)
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
