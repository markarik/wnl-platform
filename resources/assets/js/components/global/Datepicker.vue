<template>
	<input class="input datepicker" :class="{'withBorder': withBorder}"
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
		border-radius: 0
		box-shadow: none
		font-size: $font-size-plus-1
		font-weight: bold
		outline: 0
		text-align: center

		&.withBorder
			border-bottom: 1px solid $color-ocean-blue

		&.hasColorBackground
			background-color: $color-background-lighter-gray

		&.active
			background: $color-background-light-gray

</style>

<script>
import moment from 'moment';
import Flatpickr from 'flatpickr';
import {pl} from 'flatpickr/dist/l10n/pl.js';

export default {
	name: 'Datepicker',
	props: {
		config: {
			default: () => ({}),
			type: Object,
		},
		value: [String, Date, Number],
		withBorder: Boolean
	},
	data() {
		return {
			datepicker: null,
			selectedDates: null,
		};
	},
	computed: {
		date: {
			get() {
				return this.selectedDates || this.value;
			},
			set(newValue) {
				if (this.selectedDates !== newValue) {
					this.selectedDates = newValue;
					this.$emit('input', moment(newValue).toDate());
				}
			}
		},
		placeholder() {
			return this.$t('ui.placeholders.date');
		},
	},
	methods: {
		dateUpdated(selectedDates, dateStr) {
			this.date = dateStr;
		},
		onChange(payload) {
			this.$emit('onChange', payload);
		},
		redraw(newConfig) {
			this.datepicker.config = Object.assign(this.datepicker.config, newConfig);
			this.datepicker.redraw();
			this.datepicker.jumpToDate();
		},
		setDate(newDate, oldDate) {
			newDate && this.datepicker.setDate(newDate);
		},
	},
	mounted() {
		if (!this.datepicker) {
			this.config.onChange = this.onChange;
			this.config.onValueUpdate = this.dateUpdated;
			this.config.onClose = () => this.$emit('closed', this.selectedDates);
			this.datepicker = new Flatpickr(this.$el, {
				...this.config,
				locale: {
					...pl,
					firstDayOfWeek: 1
				}
			});
			this.setDate(this.value);
		}
		this.$watch('config', this.redraw);
	},
	watch: {
		value() {
			this.setDate(this.value);
		}
	},
	beforeDestroy () {
		if (this.datepicker) {
			this.datepicker.destroy();
			this.datepicker = null;
		}
	},
};
</script>
