<template>
	<div>
		<div v-for="(alert, index) in alerts" :key="index" :class="getClass(alert)">
			ALERT
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

</style>
<script>
export default {
	props: {
		alerts: {
			type: Array,
			required: true
		}
	},
	methods: {
		getClass({type}) {
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
