<template>
	<div class="control">
		<label :for="name" class="label" v-if="$slots.default">
			<slot></slot>
		</label>
		<input
			type="text"
			class="input"
			:name="name"
			:placeholder="placeholder || $slots.default[0].text || ''"
			v-model="inputValue">
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import { mapMutations } from 'vuex'
	import * as types from 'js/store/mutations-types'

	export default {
		name: 'NewInput',
		props: ['name', 'placeholder'],
		computed: {
			default() {
				return ''
			},
			fillable() {
				return true
			},
			parentName() {
				return this.$parent.name
			},
			inputValue: {
				get () {
					return this.$store.getters[`${this.parentName}/getField`](this.name)
				},
				set(value) {
					this.mutation(types.FORM_INPUT, { name: this.name, value })
				},
			},
		},
		methods: {
			getter(getter) {
				return this.$store.getters[`${this.parentName}/${getter}`]
			},
			mutation(mutation, payload) {
				return this.$store.commit(`${this.parentName}/${mutation}`, payload)
			},
		}
	}
</script>
