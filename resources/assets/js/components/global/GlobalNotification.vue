<template>
	<transition name="fade">
		<div class="notification fixed" :class="type" v-show="message">
			<span>{{message}}</span>
		</div>
	</transition>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.notification
		text-align: center

		&.fixed
			border-radius: 0
			bottom: auto
			font-size: $font-size-plus-1
			font-weight: $font-weight-bold
			left: 0
			position: fixed
			right: 0
			top: 0
			z-index: $z-index-alerts
</style>

<script>
    import { mapGetters, mapActions } from 'vuex'

    export default {
        name: 'GlobalNotification',
		computed: {
            ...mapGetters({'message': 'globalNotificationMessage', 'type': 'globalNotificationType'})
		},
    }
</script>