<template>
	<div class="field">
		<select v-model="selected">
			<option v-for="(option, key) in options" :key="key"
				:value="option.value"
				v-text="option.text">
			</option>
		</select>
	</div>
</template>

<script>
export default {
	name: 'Select',
	props: ['options', 'value'],
	data() {
		return {
			selected: this.value,
		};
	},
	watch: {
		selected(newValue) {
			this.$emit('input', newValue);
		},
		value(newValue) {
			this.selected = newValue;
		},
	}
};
</script>
