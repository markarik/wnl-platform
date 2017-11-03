<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
				:isVisible="isSidenavVisible"
				:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<div class="scrollable-main-container">
				<wnl-moderators-feed/>
			</div>
		</div>
		<wnl-sidenav-slot
				:isVisible="isChatVisible"
				:isDetached="!isChatMounted"
				:hasChat="true"
		>
			<wnl-public-chat :rooms="chatRooms" title="USZANOWANKO"></wnl-public-chat>
		</wnl-sidenav-slot>
		<div v-if="isChatToggleVisible" class="wnl-chat-toggle" @click="toggleChat">
			<span class="icon is-big">
				<i class="fa fa-chevron-left"></i>
				<span>Pokaż czat</span>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-course-layout
		justify-content: space-between

	.wnl-course-content
		max-width: $course-content-max-width
		flex: $course-content-flex auto
		position: relative

	.wnl-course-chat
		max-width: $course-chat-max-width
		min-width: $course-chat-min-width
		width: $course-chat-width

	.help-sidenav
		flex: 1

	.wnl-sidenav
		flex: 1
		padding: 7px 0

		&.mobile
			padding: 0
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import MainNav from 'js/components/MainNav'
	import ModeratorsFeed from 'js/components/moderators/ModeratorsFeed'
	import PublicChat from 'js/components/chat/PublicChat'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import withChat from 'js/mixins/with-chat'

	export default {
		name: 'ModeratorsDashboard',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-moderators-feed': ModeratorsFeed,
			'wnl-public-chat': PublicChat,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
		},
		mixins: [withChat],
		computed: {
			...mapGetters([
				'isSidenavVisible',
				'isSidenavMounted',
				'isChatMounted',
				'isChatVisible',
				'isChatToggleVisible'
			]),
			chatRooms() {
				return [
					{name: '#moderatorzy', channel: 'moderatorzy'},
				]
			},
		},
		methods: {
			...mapActions(['toggleChat']),
		},
		watch: {
			'$route.query.chatChannel' (newVal) {
				newVal && !this.isChatVisible && this.toggleChat();
			}
		},
	}
</script>
