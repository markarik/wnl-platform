<template>
	<div class="field">
		<label
			v-if="$slots.default"
			:for="name"
			class="label"
		>
			<slot />
		</label>
		<div class="control" :class="{'is-loading': isLoading}">
			<input
				v-model="inputValue"
				class="input"
				:class="{'is-danger': hasErrors}"
				:name="name"
				:placeholder="placeholder || $slots.default[0].text || ''"
				:disabled="disabled"
				@input="onInput"
			>
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

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'TextInput',
	mixins: [formInput],
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
	computed: {
		default() {
			return '';
		},
	}
};
</script>
