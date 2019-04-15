<template>
	<div class="field">
		<label
			:for="name"
			class="label"
			v-if="$slots.default"
		>
			<slot></slot>
		</label>
		<div class="control" :class="{'is-loading': isLoading}">
			<input
				class="input"
				:class="{'is-danger': hasErrors}"
				:name="name"
				:placeholder="placeholder || $slots.default[0].text || ''"
				:disabled="disabled"
				@input="onInput"
				v-model="inputValue"
			>
		</div>

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

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'TextInput',
	props: {
		name: {
			type: String,
		},
		placeholder: {
			type: String
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
	mixins: [formInput],
	computed: {
		default() {
			return '';
		},
	}
};
</script>
