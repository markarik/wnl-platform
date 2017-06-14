<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:isVisible="canRenderSidenav"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<wnl-course-navigation
				:context="context"
				:isLesson="isLesson"
			>
			</wnl-course-navigation>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<router-view v-if="ready"></router-view>
		</div>
		<wnl-sidenav-slot
			:isVisible="isChatVisible"
			:isDetached="!isChatMounted"
			:hasChat="true"
		>
			<wnl-public-chat :rooms="chatRooms"></wnl-public-chat>
		</wnl-sidenav-slot>
		<div v-if="isChatToggleVisible" class="wnl-chat-toggle">
			<span class="icon is-big" @click="toggleChat">
				<i class="fa fa-comments-o"></i>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-course-layout
		justify-content: space-between

	.wnl-course-content
		max-width: $course-content-max-width
		flex: $course-content-flex auto
		position: relative

	.wnl-course-chat
		flex: $course-chat-flex auto
		max-width: $course-chat-max-width
		min-width: $course-chat-min-width

	.wnl-chat-toggle
		align-items: flex-start
		border-left: $border-light-gray
		display: flex
		flex-grow: 0
		justify-content: center
		padding: 20px

		.icon
			color: $color-gray-dimmed
			cursor: pointer
</style>

<script>
	import axios from 'axios'
	import store from 'store'
	import { mapGetters, mapActions } from 'vuex'
	import Breadcrumbs from 'js/components/global/Breadcrumbs'
	import PublicChat from 'js/components/chat/PublicChat.vue'
	import Navigation from 'js/components/course/Navigation'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'
	import { breadcrumb } from 'js/mixins/breadcrumb'
	import { getApiUrl } from 'js/utils/env'
	import withChat from 'js/mixins/with-chat'

	export default {
		name: 'Course',
		mixins: [breadcrumb],
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
		computed: {
			...mapGetters('course', ['ready']),
			...mapGetters([
				'isSidenavVisible',
				'isSidenavMounted',
				'isChatMounted',
				'isChatVisible',
				'isChatToggleVisible'
			]),
			breadcrumb() {
				return {
					text: 'Kurs',
					to: {
						name: 'courses',
						courseId: this.courseId,
					},
				}
			},
			context() {
				return {
					courseId: this.courseId,
					lessonId: this.lessonId,
					screenId: this.screenId,
					slide: this.slide,
				}
			},
			isLesson() {
				return typeof this.lessonId !== 'undefined'
			},
			chatRooms() {
				let chatRoom = `courses-${this.courseId}`
				if (this.isLesson) {
					chatRoom += `-lessons-${this.lessonId}`
				}
				return [
					{name: '#powaga', channel: chatRoom},
					{name: '#ploteczki', channel: chatRoom + '-ploteczki'}
				]
			},
			localStorageKey() {
				return `course-structure-${this.courseId}`
			},

			canRenderSidenav() {
				return this.isSidenavVisible && this.ready
			}
		},
		components: {
			'wnl-course-navigation': Navigation,
			'wnl-public-chat': PublicChat,
			'wnl-breadcrumbs': Breadcrumbs,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav
		},
		mixins: [withChat],
		methods: {
			...mapActions('course', [
				'setup'
			]),
			...mapActions(['toggleChat', 'initChat'])
			// ...mapActions(['addBreadcrumb', 'removeBreadcrumb']),
		},
		created() {
			this.setup(this.courseId)
		}
		// mounted() {
		// 	this.addBreadcrumb({
		// 		text: 'Kurs',
		// 		to: {
		// 			name: 'courses',
		// 			courseId: this.courseId,
		// 		},
		// 	})
		// },
		// beforeDestroy() {
		// 	this.removeBreadcrumb('Kurs')
		// },
	}
</script>
