<template>
	<div class="field">

		<label :for="name" class="label">
			<slot></slot>
		</label>

		<textarea
			class="textarea normal"
			:name="name"
			@input="onTextInput"
			:placeholder="placeholder"
			v-model="inputValue"
			@keydown.enter.stop
		>
		</textarea>

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
