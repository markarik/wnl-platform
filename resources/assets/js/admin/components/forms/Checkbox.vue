<template lang="html">
	<div class="field wnl-form-checkbox">

		<input type="checkbox"
			   :name="name"
			   :checked="form[name]"
			   @change="$emit('input', $event.target.checked)"
			   :id="name">

		<label :for="name" class="label">
			<slot></slot>
		</label>

		<span class="help is-danger" v-if="form.errors.has(name)"
			  v-text="form.errors.get(name)"></span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	.wnl-form-checkbox
		label
			display: inline-block
</style>

<script>
export default {
	name: 'wnl-form-checkbox',
	props: ['type', 'name', 'form'],
};
</script>
