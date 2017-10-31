<template>
	<div>
		<div class="notification" :class="typeToClass(type)">
			<slot/>
			<button v-if="dismissable" @click="$emit('onDismiss')" class="delete"></button>
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

</style>
<script>
export default {
	props: {
		type: {
			type: String
		},
		dismissable: {
			type: Boolean,
			default: true
		}
	},
	methods: {
		typeToClass(type) {
			switch(type) {
				case 'error':
					return 'is-danger'
				case 'success':
					return 'is-success'
				case 'info':
					return 'is-info'
				default:
					return 'is-primary'
			}
		}
	}
}
</script>
