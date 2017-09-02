<template>
	<input class="input datepicker"
		:placeholder="placeholder"
		:value="date"
	/>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.datepicker-container
		position: relative

	.datepicker
		border: 0
		border-bottom: 1px solid $color-ocean-blue
		border-radius: 0
		box-shadow: none
		font-size: $font-size-plus-1
		font-weight: bold
		outline: 0
		text-align: center
</style>

<script>
	import Flatpickr from 'flatpickr'

	export default {
		name: 'Datepicker',
		props: {
			config: {
				default: () => ({}),
				type: Object,
			},
			value: String|Date,
		},
		data() {
			return {
				datepicker: null,
				selectedDates: null,
			}
		},
		computed: {
			date: {
				get() {
					return this.selectedDates || this.value
				},
				set(newValue) {
					if (this.selectedDates !== newValue) {
						this.selectedDates = newValue
						this.$emit('input', new Date(newValue))
					}
				}
			},
			placeholder() {
				return this.$t('ui.placeholders.date')
			},
		},
		methods: {
			redraw(newConfig) {
				this.datepicker.config = Object.assign(this.datepicker.config, newConfig)
				this.datepicker.redraw()
				this.datepicker.jumpToDate()
			},
			setDate(newDate, oldDate) {
				newDate && this.datepicker.setDate(newDate)
			},
			dateUpdated(selectedDates, dateStr) {
				this.date = dateStr
			},
		},
		mounted() {
			if (!this.datepicker) {
				this.config.onValueUpdate = this.dateUpdated
				this.datepicker = new Flatpickr(this.$el, this.config)
				this.setDate(this.value)
			}
			this.$watch('config', this.redraw)
			this.$watch('value', this.setDate)
		},
		beforeDestroy () {
			if (this.datepicker) {
				this.datepicker.destroy()
				this.datepicker = null
			}
		},
	}
</script>
