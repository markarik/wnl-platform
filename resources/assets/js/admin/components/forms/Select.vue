<template>
	<div class="field select">
		<select
			ref="select"
			v-model="selected"
			@keyup.esc="onEscape"
		>
			<option
				v-for="(option, key) in options"
				:key="key"
				:value="option.value"
				v-text="option.text"
			>
			</option>
		</select>
	</div>
</template>

<script>
export default {
	name: 'Select',
	props: {
		options: {
			type: Array,
			required: true,
		},
		value: {
			value: [Number, String],
			default: null,
		},
	},
	data() {
		return {
			selected: this.value,
		};
	},
	methods: {
		onEscape() {
			this.$refs.select.blur();
		},
	},
	watch: {
		selected(newValue) {
			this.$emit('input', newValue);
		},
		value(newValue) {
			this.selected = newValue;
		},
	},
};
</script>
