<template>
	<div class="field">
		<input
			:id="checkboxId"
			v-model="value"
			type="checkbox"
			class="is-checkradio"
			:value="checkboxValue"
		>
		<label class="checkbox" :for="checkboxId"><slot /></label>
	</div>
</template>

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'CheckboxInput',
	mixins: [formInput],
	props: {
		name: {
			type: String,
		},
		checkboxValue: {
			required: true
		},
		checkboxId: {
			required: true
		}
	},
	computed: {
		default() {
			return '';
		},
		value: {
			get() {
				return this.inputValue || [];
			},
			set(value) {
				this.inputValue = value;
			}
		}
	}
};
</script>
