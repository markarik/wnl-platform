<template>
	<a :class="[{'is-loading': isLoading}, cssClass ? cssClass : 'button is-primary is-wide']"
		:disabled="!hasChanges || anyErrors"
		@click="submitParent">
		<slot>Zapisz</slot>
	</a>
</template>

<script>
	export default {
		name: 'Submit',
		props: ['cssClass'],
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
			modifiedAnnotationId() {
				return this.getter('modifiedAnnotationId')
			},
			anyErrors() {
				return this.getter('anyErrors')
			},
			cssClasses() {
				return this.css
			}
		},
		methods: {
			getter(getter) {
				return this.$store.getters[`${this.parentName}/${getter}`]
			},
			submitParent() {
				this.$parent.$emit('submitForm')
			}
		}
	}
</script>
