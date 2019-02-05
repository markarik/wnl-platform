<template>
	<div class="field">
		<wnl-toggler
				@toggle="onToggle"
				:value="inputValue"
				:name="name"
		>
		</wnl-toggler>
		<label :for="name" class="label" v-if="$slots.default">
			<slot></slot>
		</label>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.label
		display: inline
</style>

<script>
import Toggler from 'js/components/global/Toggler';
import {formInput} from 'js/mixins/form-input';

export default {
	name: 'WnlFormCheck',
	props: ['name', 'placeholder'],
	mixins: [formInput],
	components: {
		'wnl-toggler': Toggler,
	},
	computed: {
		default() {
			return '';
		}
	},
	methods: {
		onToggle() {
			this.inputValue = !this.inputValue;
			this.$parent.$emit('submitForm');
		}
	},
};
</script>
