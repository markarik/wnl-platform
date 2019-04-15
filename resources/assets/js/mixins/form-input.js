import * as types from 'js/store/mutations-types';
import { nextTick } from 'vue';

export var formInput = {
	computed: {
		default() {
			return null;
		},
		fillable() {
			return true;
		},
		parentName() {
			return this.$parent.name;
		},
		hasErrors() {
			return this.getterFunction('hasErrors', this.name);
		},
		getErrors() {
			return this.getterFunction('getErrors', this.name);
		},
		formData() {
			return this.getter('getData');
		},
		isLoading() {
			return this.getter('isLoading');
		},
		inputValue: {
			get() {
				return this.getterFunction('getField', this.name);
			},
			set(value) {
				return this.setValue(value);
			},
		},
	},
	methods: {
		getter(getter) {
			return this.$store.getters[`form/${getter}`](this.parentName);
		},
		getterFunction(getter, payload = {}) {
			return this.$store.getters[`form/${getter}`](this.parentName)(payload);
		},
		mutation(mutation, payload = {}) {
			return this.$store.commit(`form/${mutation}`, { payload, formName: this.parentName });
		},
		async onInput($event) {
			if (this.hasErrors) {
				this.mutation(types.ERRORS_CLEAR_SINGLE, { name: this.name });
			}
			await nextTick();
			this.$emit('input', $event);
		},
		setValue(value) {
			this.mutation(types.FORM_INPUT, { name: this.name, value });
		},
	}
};
