<template lang="html">
	<div class="field">
		<wnl-toggler
				@input="onInput"
				@click="onClick"
				v-model="value"
				:name="name"
		>
		</wnl-toggler>
		<label :for="name" class="label" v-if="$slots.default">
			<slot></slot>
		</label>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	.label
		display: inline

</style>

<script>
	import Toggler from 'js/components/global/Toggler'
	import {formInput} from 'js/mixins/form-input'

	export default {
		name: 'wnl-form-check',
		props: ['name', 'placeholder'],
		mixins: [formInput],
		components: {
			'wnl-toggler': Toggler,
		},
		data () {
			return {
				value: null
			}
		},
		computed: {
			default() {
				return ''
			}
		},
		methods: {
			onClick() {
				// console.log('submitForm');
				this.$parent.$emit('submitForm')
			}
		},
		watch: {
			inputValue (newValue) {
				if (newValue !== this.value) {
					this.value = newValue
				}
			},
			value (value){
				this.setValue(value)
			}
		}
	}
</script>
