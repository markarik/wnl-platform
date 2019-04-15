<template>
	<div class="field wnl-form-checkbox">

		<input
			:id="name"
			type="checkbox"
			:name="name"
			:checked="form[name]"
			@change="$emit('input', $event.target.checked)"
		>

		<label :for="name" class="label">
			<slot></slot>
		</label>

		<span
			v-if="form.errors.has(name)"
			class="help is-danger"
			v-text="form.errors.get(name)"
		></span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	.wnl-form-checkbox
		label
			display: inline-block
</style>

<script>
export default {
	name: 'WnlFormCheckbox',
	props: ['type', 'name', 'form'],
};
</script>
