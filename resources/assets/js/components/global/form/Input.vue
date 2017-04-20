<template lang="html">
	<div class="field">

		<label :for="name" class="label">
			<slot></slot>
		</label>

		<input :type="type"
			   class="input is-medium"
			   :name="name"
			   :value="form[name]"
			   @input="$emit('input', $event.target.value)"
			   :placeholder="name"
			   :id="name">

		<span class="help is-danger" v-if="form.errors.has(name)"
			  v-text="form.errors.get(name)"></span>
	</div>
</template>

<style lang="sass">
</style>

<script>
	export default {
		name: 'wnl-form-input',
		props: ['type', 'name', 'form'],
	}
</script>
