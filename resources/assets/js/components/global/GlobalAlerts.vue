<template>
	<div>
		<wnl-alert v-for="(alert, index) in alerts" :key="index" :type="alert.type">
			{{alert.text}}
		</wnl-alert>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

</style>
<script>
import Alert from 'js/components/global/GlobalAlert'

export default {
	components: {
		'wnl-alert': Alert
	},
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
