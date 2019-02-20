<template>
	<div class="field">

		<label :for="name" class="label">
			<slot></slot>
		</label>

		<textarea
				  class="textarea normal"
				  :name="name"
				  @input="$emit('input', $event.target.value)"
				  :placeholder="placeholder"
				  v-model="inputValue"
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
	}
};
</script>
