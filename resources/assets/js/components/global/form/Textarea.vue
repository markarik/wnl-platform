<template>
	<div class="field">

		<label :for="name" class="label">
			<slot></slot>
		</label>

		<textarea
			v-model="inputValue"
			class="textarea normal"
			:name="name"
			:placeholder="placeholder"
			@input="onTextInput"
			@keydown.enter.stop
		>
		</textarea>

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

<style lang="sass">
</style>

<script>
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'Textarea',
	props: ['name', 'placeholder'],
	mixins: [formInput],
	computed: {
		default() {
			return '';
		},
	},
	methods: {
		onTextInput(event) {
			this.onInput(event);
			this.$emit('input', event.target.value);
		}
	}
};
</script>
