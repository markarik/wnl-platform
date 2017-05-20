<template>
	<div>
		<a class="button is-primary is-wide"
			:class="{'is-loading': isLoading}"
			:disabled="!hasChanges || anyErrors"
			@click="submitParent">
			<slot></slot>
		</a>
	</div>
</template>

<script>
	export default {
		name: 'Submit',
		computed: {
			parentName() {
				return this.$parent.name
			},
			isLoading() {
				return this.getter('isLoading')
			},
			/**
			 * If you ever wonder don't I just use an isDisabled computed property
			 * Vue doesn't have access to methods, when evaluating computed
			 * properties. However, when it renders them - everything is in
			 * place. That's why I used them in the template.
			 */
			hasChanges() {
				return this.getter('hasChanges')
			},
			anyErrors() {
				return this.getter('anyErrors')
			},
		},
		methods: {
			getter(getter) {
				return this.$store.getters[`${this.parentName}/${getter}`]
			},
			submitParent() {
				this.$emit('submitForm')
			}
		}
	}
</script>
