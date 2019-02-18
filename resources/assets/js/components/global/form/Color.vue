<template>
	<div class="field">
		<label :for="name" class="label" v-if="$slots.default">
			<slot></slot>
		</label>
		<div class="control" :class="{'is-loading': isLoading}">
			<input
				class="input"
				type="color"
				:class="{'is-danger': hasErrors}"
				:name="name"
				:disabled="disabled"
				@input="onInput"
				v-model="inputValue">
		</div>

		<span class="help is-danger"
					v-if="hasErrors"
					v-for="(error, index) in getErrors"
					v-text="error"
					:key="index"></span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

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
