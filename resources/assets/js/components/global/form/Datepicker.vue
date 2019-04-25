<template>
	<div class="field ">
		<label :for="name" class="label">
			<slot />
		</label>

		<div class="field has-addons">
			<div class="control is-expanded">
				<input
					ref="input"
					v-model="inputValue"
					class="input datepicker"
					:name="name"
					:placeholder="placeholder"
					@input="onDateInput"
				>
			</div>
			<div
				v-if="isOptional"
				class="control"
				@click="clearDate()"
			>
				<span class="icon is-small clickable"><i class="fa fa-close" /></span>
			</div>
		</div>

		<template v-if="hasErrors">
			<span
				v-for="(error, index) in getErrors"
				:key="index"
				class="help is-danger"
				v-text="error"
			/>
		</template>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.datepicker-container
		position: relative

	.datepicker
		&.hasColorBackground
			background-color: $color-background-lighter-gray

		&.active
			background: $color-background-light-gray

	.icon
		padding: $margin-base
</style>

<script>
import Flatpickr from 'flatpickr';
import { formInput } from 'js/mixins/form-input';
import { pl } from 'flatpickr/dist/l10n/pl.js';

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
		isOptional: {
			type: Boolean,
			default: false,
		}
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
	watch: {
		config(newConfig) {
			this.redraw(newConfig);
		},
		inputValue(val) {
			this.setDate(val * 1000);
		}
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
	beforeDestroy () {
		if (this.datepicker) {
			this.datepicker.destroy();
			this.datepicker = null;
		}
	},
	methods: {
		redraw(newConfig) {
			this.datepicker.config = Object.assign(this.datepicker.config, newConfig);
			this.datepicker.redraw();
			this.datepicker.jumpToDate();
		},
		setDate(newDate) {
			newDate && this.datepicker.setDate(newDate);
		},
		clearDate() {
			this.datepicker.setDate(null);
		},
		onDateInput(event) {
			this.onInput(event);
			this.$emit('input', event.target.value);
		}
	},
};
</script>
