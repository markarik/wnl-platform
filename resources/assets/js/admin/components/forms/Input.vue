<template>
	<div class="control">
		<label
			v-if="this.$slots.default"
			:for="name"
			class="label"
		>
			<slot />
		</label>

		<input
			:id="name"
			:type="type"
			class="input"
			:name="name"
			:value="form[name]"
			:placeholder="name"
			@input="$emit('input', $event.target.value)"
		>
		<span
			v-if="form.errors.has(name)"
			class="help is-danger pre-line"
			v-text="form.errors.get(name)"
		/>
	</div>
</template>

<script>
export default {
	name: 'WnlFormInput',
	props: ['type', 'name', 'form'],
};
</script>
