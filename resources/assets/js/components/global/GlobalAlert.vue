<template>
	<div>
		<div class="notification" :class="typeToClass(type)">
			<slot/>
			<button v-if="dismissable" @click="$emit('onDismiss', {id: id})" class="delete"></button>
		</div>
	</div>
</template>
<script>
export default {
	props: {
		type: {
			type: String
		},
		dismissable: {
			type: Boolean,
			default: true
		},
		id: {
			type: String,
			default: ''
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
					case 'warning':
					return 'is-warning'
				default:
					return 'is-primary'
			}
		},
	}
}
</script>
