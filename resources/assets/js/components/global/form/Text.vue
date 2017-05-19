<template>
	<div class="field">
		<label :for="name" class="label" v-if="$slots.default">
			<slot></slot>
		</label>
		<div class="control">
			<input
				type="text"
				class="input"
				:class="{'is-danger': hasErrors}"
				:name="name"
				:placeholder="placeholder || $slots.default[0].text || ''"
				@input="onInput"
				v-model="inputValue">
		</div>

		<span class="help is-danger" v-if="hasErrors" v-for="error in getErrors" v-text="error"></span>
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
			hasErrors() {
				return this.getter('hasErrors', this.name)
			},
			getErrors() {
				return this.getter('getErrors', this.name)
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
			getter(getter, payload = {}) {
				return this.$store.getters[`${this.parentName}/${getter}`](payload)
			},
			mutation(mutation, payload = {}) {
				return this.$store.commit(`${this.parentName}/${mutation}`, payload)
			},
			onInput(value) {
				this.mutation(types.ERRORS_CLEAR_SINGLE, { name: this.name })
			},
		}
	}
</script>
