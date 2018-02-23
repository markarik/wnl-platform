<template>
	<a class="button is-small is-outlined" @click="toggleNotifications">
		<span>{{ toggleText }}</span>
		<span class="toggle-icon icon is-small"><i class="fa" :class="toggleIcon"></i></span>
	</a>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.toggle-icon
		color: $color-gray-dimmed
		margin-left: $margin-small
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	const setting = 'notify_live'

	export default {
		name: 'NotificationsToggle',
        data() {
            return {
                isOn: false,
            }
        },
		computed: {
			toggleText() {
				return !this.isOn ? this.message('turnOff') : this.message('turnOn')
			},
			toggleIcon() {
				return this.isOn ? 'fa-comment' : 'fa-comment-o'
			},
		},
		methods: {
			message(key) {
				return this.$t(`notifications.personal.${key}`)
			},
			toggleNotifications() {
				this.isOn = !this.isOn
			},
		},
	}
</script>
