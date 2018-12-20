<template>
	<div class="wnl-alerts">
		<wnl-alert class="wnl-alert"
			v-for="alert in alerts"
			:key="alert.id"
			:type="alert.type"
			:id="alert.id"
			@onDismiss="onDismiss(alert)"
		>
			<div v-html="alert.text"></div>
		</wnl-alert>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.wnl-alerts
		position: absolute
		width: 100vw
		z-index: $z-index-alerts
		text-align: center

	.wnl-alert
		/deep/ .notification
			strong
				color: inherit

</style>
<script>
import Alert from 'js/components/global/GlobalAlert';
import {mapActions} from 'vuex';

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
		...mapActions(['closeAlert']),
		onDismiss(alert) {
			if (typeof alert.dismissCallback === 'function') {
				alert.dismissCallback();
			}
			this.closeAlert(alert);
		}
	},
};
</script>
