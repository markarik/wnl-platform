<template>
	<div class="field">

		<label :for="name" class="label">
			<slot />
		</label>

		<textarea
			:id="name"
			class="textarea is-medium"
			:name="name"
			:value="form[name]"
			:placeholder="name"
			@input="$emit('input', $event.target.value)"
		/>

		<span
			v-if="form.errors.has(name)"
			class="help is-danger"
			v-text="form.errors.get(name)"
		/>
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
