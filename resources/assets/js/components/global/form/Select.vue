<template>
	<div class="field">
		<select v-model="selected">
			<option v-for="(option, itemKey) in options" key="itemKey"
				:value="itemKey"
				v-text="option.name"
				:selected="itemKey === value">
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
		},
		data() {
			return {
				selected: '',
			}
		},
		mounted() {
			this.selected = this.value
		},
		watch: {
			selected(newValue) {
				this.$emit('input', newValue)
			},
			value(newValue) {
				this.selected = newValue
			},
		}
	}
</script>
