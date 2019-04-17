<template>
	<div class="field">
		<wnl-toggler
			:value="inputValue"
			:name="name"
			@toggle="onToggle"
		/>
		<label
			v-if="$slots.default"
			:for="name"
			class="label"
		>
			<slot />
		</label>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.label
		display: inline
</style>

<script>
import Toggler from 'js/components/global/Toggler';
import { formInput } from 'js/mixins/form-input';

export default {
	name: 'WnlFormCheck',
	components: {
		'wnl-toggler': Toggler,
	},
	mixins: [formInput],
	props: ['name', 'placeholder'],
	computed: {
		default() {
			return '';
		}
	},
	methods: {
		onToggle() {
			this.inputValue = !this.inputValue;
			this.$emit('input');
		}
	},
};
</script>
