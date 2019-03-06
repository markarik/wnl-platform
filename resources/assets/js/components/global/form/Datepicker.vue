<template>
	<div class="field">
		<label :for="name" class="label">
			<slot></slot>
		</label>

		<input class="input datepicker"
			   :name="name"
			   ref="input"
			   :placeholder="placeholder"
			   v-model="inputValue"
			   @input="$emit('input', $event.target.value)"
		/>

		<template v-if="hasErrors">
			<span
				class="help is-danger"
				v-for="(error, index) in getErrors"
				v-text="error"
				:key="index"
			></span>
		</template>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.datepicker-container
		position: relative

	.datepicker

		&.hasColorBackground
			background-color: $color-background-lighter-gray

		&.active
			background: $color-background-light-gray

</style>

<script>
import Flatpickr from 'flatpickr';
import { formInput } from 'js/mixins/form-input';
import {pl} from 'flatpickr/dist/l10n/pl.js';

export default {
	name: 'Datepicker',
	mixins: [formInput],
	props: {
		config: {
			default: () => ({}),
			type: Object,
		},
		name: {
			type: String,
		},
	},
	data() {
		return {
			datepicker: null,
		};
	},
	computed: {
		placeholder() {
			return this.$t('ui.placeholders.date');
		},
	},
	methods: {
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
			this.datepicker = new Flatpickr(this.$refs.input, {
				...this.config,
				locale: {
					...pl,
					firstDayOfWeek: 1
				}
			});
			this.setDate(this.inputValue);
		}
	},
	watch: {
		config(newConfig) {
			this.redraw(newConfig);
		},
		inputValue(val) {
			this.setDate(val * 1000);
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
