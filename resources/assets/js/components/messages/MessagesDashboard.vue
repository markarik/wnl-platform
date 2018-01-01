<template lang="html">
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
				:isVisible="isSidenavVisible"
				:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside help-sidenav">
				siema
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<div class="scrollable-main-container">
				<wnl-private-chat :users="chatUsers"></wnl-private-chat>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import MainNav from 'js/components/MainNav'
	import PublicChat from 'js/components/chat/PublicChat'
	import PrivateChat from 'js/components/chat/PrivateChat'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import withChat from 'js/mixins/with-chat'

	export default {
		name: 'MessagesDashboard',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-public-chat': PublicChat,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-private-chat': PrivateChat,
		},
		computed: {
			...mapGetters([
				'isSidenavVisible',
				'isSidenavMounted',
				'isChatMounted',
				'isChatVisible',
				'isChatToggleVisible',
				'currentUserName'
			]),
			...mapGetters('course', ['ready']),
			chatUsers() {
				return [1,2]
			},
		}
	}
</script>
