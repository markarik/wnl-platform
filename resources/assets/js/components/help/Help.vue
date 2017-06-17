<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="help-sidenav">
				<wnl-sidenav :items="sidenavItems" itemsHeading="Pomoc"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<div class="scrollable-main-container" v-if="ready">
				<router-view v-if="!isMainRoute"></router-view>
				<wnl-learning-help v-else></wnl-learning-help>
			</div>
		</div>
		<wnl-sidenav-slot
			:isVisible="isChatVisible"
			:isDetached="!isChatMounted"
			:hasChat="true"
		>
		<wnl-public-chat :rooms="chatRooms" title="W czym możemy Ci pomóc?"></wnl-public-chat>
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
		min-width: $sidenav-min-width
		overflow: auto
		padding: 7px 0
		width: $sidenav-width
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import LearningHelp from 'js/components/help/LearningHelp'
	import MainNav from 'js/components/MainNav'
	import PublicChat from 'js/components/chat/PublicChat'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import withChat from 'js/mixins/with-chat'

	export default {
		name: 'Help',
		components: {
			'wnl-learning-help': LearningHelp,
			'wnl-main-nav': MainNav,
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
			...mapGetters('course', ['ready']),
			sidenavItems() {
				return [
					{
						text: 'Pomoc w nauce',
						itemClass: 'has-icon',
						to: {
							name: 'help-learning',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-mortar-board',
						iconTitle: 'Pomoc w nauce',
					},
					{
						text: 'Pomoc techniczna',
						itemClass: 'has-icon',
						to: {
							name: 'help-tech',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-magic',
						iconTitle: 'Pomoc techniczna',
					},
					{
						text: 'Nad czym pracujemy?',
						itemClass: 'has-icon',
						to: {
							name: 'help-new',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-gift',
						iconTitle: 'Nad czym pracujemy?',
					},
				]
			},
			chatRooms() {
				return [
					{name: '#nauka', channel: 'help-learning'},
					{name: '#tech', channel: 'help-tech'},
				]
			},
			isMainRoute() {
				return this.$route.name === 'help'
			},
		},
		methods: {
			...mapActions(['toggleChat']),
			...mapActions('course', ['setup']),
		},
		mounted() {
			if (!this.ready) {
				this.setup(1)
			}
		},
	}
</script>
