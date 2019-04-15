<template>
	<a class="button is-small is-outlined" @click="toggleNotifications">
		<span>{{ toggleText }}</span>
		<span class="toggle-icon icon is-small"><i class="fa" :class="toggleIcon"></i></span>
	</a>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.toggle-icon
		color: $color-gray
		margin-left: $margin-small
</style>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
	name: 'NotificationsToggle',
	props: {
		setting: {
			required: true,
			type: String,
		},
		icons: {
			required: true,
			type: Array,
		}
	},
	computed: {
		...mapGetters(['getSetting']),
		isOn() {
			return this.getSetting(this.setting);
		},
		toggleText() {
			return this.isOn ? this.message('turnOff') : this.message('turnOn');
		},
		toggleIcon() {
			return this.isOn ? this.icons[0] : this.icons[1];
		},
	},
	methods: {
		...mapActions(['changeUserSettingAndSync']),
		message(key) {
			return this.$t(`notifications.personal.${key}`);
		},
		toggleNotifications() {
			this.changeUserSettingAndSync({ setting: this.setting, value: !this.isOn });
		},
	},
};
</script>
