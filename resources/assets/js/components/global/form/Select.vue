<template>
	<div class="field">
		<label :for="name" class="label" v-if="$slots.default">
			<slot></slot>
		</label>
		<div class="control" :class="{'is-loading': isLoading}">
			<select
				class="select"
				:class="{'is-danger': hasErrors}"
				:name="name"
				:disabled="disabled"
				@input="onInput"
				v-model="inputValue"
			>
				<option
					v-for="(option, key) in options"
					:key="key"
					:value="option.value"
					v-text="option.text">
				</option>
			</select>
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

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'TextInput',
	props: {
		name: {
			type: String,
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
