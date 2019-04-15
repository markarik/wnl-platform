<template>
	<div class="field">
		<label
			v-if="$slots.default"
			:for="name"
			class="label"
		>
			<slot></slot>
		</label>
		<div class="control" :class="{'is-loading': isLoading}">
			<select
				v-model="inputValue"
				class="select"
				:class="{'is-danger': hasErrors}"
				:name="name"
				:disabled="disabled"
				@input="onInput"
			>
				<option
					v-for="(option, key) in options"
					:key="key"
					:value="option.value"
					v-text="option.text"
				>
				</option>
			</select>
		</div>

		<template v-if="hasErrors">
			<span
				v-for="(error, index) in getErrors"
				:key="index"
				class="help is-danger"
				v-text="error"
			></span>
		</template>
	</div>
</template>

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	props: {
		name: {
			type: String,
			required: true,
		},
		disabled: {
			type: Boolean,
			default: false
		},
		options: {
			type: Array,
			required: true,
		}
	},
	mixins: [formInput],
};
</script>
