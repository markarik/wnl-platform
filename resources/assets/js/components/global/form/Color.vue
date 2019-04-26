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
				type="color"
				:class="{'is-danger': hasErrors, 'is-empty': !inputValue}"
				:name="name"
				:disabled="disabled"
				@input="onInput"
			>
		</div>

		<template v-if="hasErrors">
			<span
				v-for="(error, index) in getErrors"
				:key="index"
				class="help is-danger pre-line"
				v-text="error"
			/>
		</template>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.is-empty::-webkit-color-swatch
		background-color: transparent !important
</style>

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'ColorInput',
	mixins: [formInput],
	props: {
		name: {
			type: String,
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
};
</script>
