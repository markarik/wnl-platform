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
				type="password"
				class="input"
				:class="{'is-danger': hasErrors}"
				:name="name"
				:placeholder="placeholder || $slots.default[0].text || ''"
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

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'Password',
	props: {
		name: {
			type: String,
		},
		placeholder: {
			type: String
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
