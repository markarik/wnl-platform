<template>
	<div class="field">
		<select>
			<option v-for="(option, itemKey) in options" key="itemKey"
				:value="itemKey"
				v-text="option.name"
				:selected="option.value === value">
			</option>
		</select>
	</div>
</template>

<script>
	export default {
		name: 'Select',
		props: {
			options: Object,
			value: String,
		}
	}
</script>
