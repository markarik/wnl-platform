<template>
	<div class="field">
		<label :for="name" class="label" v-if="$slots.default">
			<slot></slot>
		</label>
		<div class="control" :class="{'is-loading': isLoading}">
			<input
				class="input"
				type="color"
				:class="{'is-danger': hasErrors, 'is-empty': !inputValue}"
				:name="name"
				:disabled="disabled"
				@input="onInput"
				v-model="inputValue">
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

<style lang="sass" rel="stylesheet/sass">
	input.is-empty[type=color]::-webkit-color-swatch
		background-color: transparent !important
</style>

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'ColorInput',
	props: {
		name: {
			type: String,
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
	mixins: [formInput],
};
</script>
