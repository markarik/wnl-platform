<template>
	<div @click="submitParent">
		<slot></slot>
	</div>
</template>

<script>
	export default {
		name: 'Submit',
		methods: {
			submitParent() {
				this.$emit('submitForm')
			}
		}
	}
</script>
