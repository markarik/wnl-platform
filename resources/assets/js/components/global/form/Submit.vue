<template>
	<div>
		<a class="button is-primary is-wide"
			:class="{'is-loading': isLoading}"
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
