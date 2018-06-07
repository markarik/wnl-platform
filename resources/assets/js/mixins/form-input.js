import * as types from 'js/store/mutations-types'

export var formInput = {
	computed: {
		default() {
			return null
		},
		fillable() {
			return true
		},
		parentName() {
			return this.$parent.name
		},
		hasErrors() {
			return this.getterFunction('hasErrors', this.name)
		},
		getErrors() {
			return this.getterFunction('getErrors', this.name)
		},
		isLoading() {
			return this.getter('isLoading')
		},
		inputValue: {
			get() {
				return this.getterFunction('getField', this.name)
			},
			set(value) {
				return this.setValue(value)
			},
		},
	},
	methods: {
		getter(getter) {
			return this.$store.getters[`${this.parentName}/${getter}`]
		},
		getterFunction(getter, payload = {}) {
			return this.$store.getters[`${this.parentName}/${getter}`](payload)
		},
		mutation(mutation, payload = {}) {
			return this.$store.commit(`${this.parentName}/${mutation}`, payload)
		},
		onInput(value) {
			if (this.hasErrors) {
				this.mutation(types.ERRORS_CLEAR_SINGLE, { name: this.name })
			}
		},
		setValue(value) {
			this.mutation(types.FORM_INPUT, { name: this.name, value })
		},
	}
}
