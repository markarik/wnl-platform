<template>
	<div class="wnl-app-layout wnl-course-layout" v-if="ready">
		<wnl-sidenav-slot
			:isVisible="canRenderSidenav"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<wnl-course-navigation
				v-if="canAccess"
				:context="context"
				:isLesson="isLesson"
			>
			</wnl-course-navigation>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column" v-if="canAccess">
			<router-view :presenceChannel="presenceChannel"/>
		</div>
		<div v-else class="wnl-course-content wnl-column">
			<wnl-splash-screen/>
		</div>
		<wnl-sidenav-slot
			class="course-chat"
			:isVisible="isChatVisible"
			:isDetached="!isChatMounted"
			:hasChat="true"
		>
			<div v-if="isLesson" class="lesson-active-users-container">
				<wnl-active-users message="dashboard.activeUsersLessons" :channel="presenceChannel"/>
			</div>
			<wnl-public-chat :rooms="chatRooms" v-if="canAccess"/>
		</wnl-sidenav-slot>
		<div v-if="canAccess && isChatToggleVisible" class="wnl-chat-toggle">
			<span class="icon is-big" @click="toggleChat">
				<i class="fa fa-chevron-left"></i>
				<span>Pokaż czat</span>
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
		flex: $course-content-flex 0 0
		position: relative
		overflow-y: hidden

	.wnl-course-chat
		max-width: $course-chat-max-width
		min-width: $course-chat-min-width
		width: $course-chat-width

	.course-chat
		.sidenav-content
			display: flex
			flex-direction: column

	.lesson-active-users-container
		.active-users
			margin: $margin-small $margin-base 0
</style>

<script>
	import axios from 'axios'
	import store from 'store'
	import { mapGetters, mapActions } from 'vuex'
	import ActiveUsers from 'js/components/course/dashboard/ActiveUsers'
	import Breadcrumbs from 'js/components/global/Breadcrumbs'
	import PublicChat from 'js/components/chat/PublicChat.vue'
	import Navigation from 'js/components/course/Navigation'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'
	import SplashScreen from 'js/components/global/SplashScreen.vue'
	import { breadcrumb } from 'js/mixins/breadcrumb'
	import { getApiUrl } from 'js/utils/env'
	import withChat from 'js/mixins/with-chat'

	export default {
		name: 'Course',
		components: {
			'wnl-active-users': ActiveUsers,
			'wnl-course-navigation': Navigation,
			'wnl-public-chat': PublicChat,
			'wnl-breadcrumbs': Breadcrumbs,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav,
			'wnl-splash-screen': SplashScreen,
		},
		mixins: [breadcrumb],
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
		computed: {
			...mapGetters('course', ['isLessonAvailable', 'ready']),
			...mapGetters([
				'currentUser',
				'isSidenavVisible',
				'isSidenavMounted',
				'isChatMounted',
				'isChatVisible',
				'isChatToggleVisible',
			]),
			canAccess() {
				return this.currentUser.roles.includes('edition-1-participant') &&
					this.currentUser.roles.includes('edition-2-participant')
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
				return typeof this.lessonId !== 'undefined' && this.isLessonAvailable(this.lessonId)
			},
			chatRooms() {
				let chatRoom = `courses-${this.courseId}`
				if (this.isLesson) {
					chatRoom += `-lessons-${this.lessonId}`
					return [
						{name: '#nauka', channel: chatRoom},
						{name: '#ploteczki', channel: chatRoom + '-ploteczki'}
					]
				}

				return [
					{name: '#aula', channel: chatRoom},
				]
			},
			canRenderSidenav() {
				return this.isSidenavVisible && this.ready
			},
			presenceChannel() {
				return `lesson.${this.lessonId}`
			},
		},
		mixins: [withChat, breadcrumb],
		methods: {
			...mapActions(['toggleChat']),
		},
		watch: {
			'$route.query.chatChannel' (newVal) {
				newVal && !this.isChatVisible && this.toggleChat();
			}
		}
	}
</script>
