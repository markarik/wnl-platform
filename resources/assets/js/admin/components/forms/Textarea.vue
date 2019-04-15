<template>
	<div class="field">

		<label :for="name" class="label">
			<slot></slot>
		</label>

		<textarea
			class="textarea is-medium"
			:name="name"
			:value="form[name]"
			@input="$emit('input', $event.target.value)"
			:placeholder="name"
			:id="name"
		>
		</textarea>

		<span
			class="help is-danger"
			v-if="form.errors.has(name)"
			v-text="form.errors.get(name)"
		></span>
	</div>
</template>

<style lang="sass">
</style>

<script>
export default {
	name: 'WnlFormTextarea',
	props: ['name', 'form'],
};
</script>
