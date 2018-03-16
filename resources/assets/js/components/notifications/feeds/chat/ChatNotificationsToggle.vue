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

	export default {
		name: 'NotificationsToggle',
		computed: {
			...mapGetters(['getSetting']),
			isOn() {
				return this.getSetting(this.SETTING)
			},
			toggleText() {
				return this.isOn ? this.message('turnOff') : this.message('turnOn')
			},
			toggleIcon() {
				return this.isOn ? 'fa-comment' : 'fa-comment-o'
			},
		},
		methods: {
			...mapActions(['changeUserSettingAndSync']),
			message(key) {
				return this.$t(`notifications.personal.${key}`)
			},
			toggleNotifications() {
				this.changeUserSettingAndSync({setting: this.SETTING, value: !this.isOn})
			},
		},
		SETTING: 'private_chat_nofitications'
	}
</script>
