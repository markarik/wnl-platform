<template>
	<div class="wnl-app-layout">
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
		<div class="wnl-course-chat wnl-column">
			<wnl-chat :room="chatRoom"></wnl-chat>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-course-content
		border-left: $border-light-gray
		border-right: $border-light-gray
		margin: 0 $margin-base
		max-width: $course-content-max-width
		min-width: $course-content-min-width
		flex: $course-content-flex auto
		padding: $column-padding
		position: relative

	.wnl-course-chat
		flex: $course-chat-flex auto
		max-width: $course-chat-max-width
</style>

<script>
	import axios from 'axios'
	import { mapGetters, mapActions } from 'vuex'

	import Breadcrumbs from 'js/components/global/Breadcrumbs'
	import Chat from 'js/components/chat/Chat'
	import Navigation from 'js/components/course/Navigation'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'
	import { breadcrumb } from 'js/mixins/breadcrumb'
	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'Course',
		components: {
			'wnl-breadcrumbs': Breadcrumbs,
			'wnl-course-navigation': Navigation,
			'wnl-chat': Chat,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav
		},
		mixins: [breadcrumb],
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
		computed: {
			...mapGetters('course', ['ready']),
			...mapGetters(['isSidenavVisible', 'isSidenavMounted']),
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
			chatRoom() {
				let chatRoom = `courses-${this.courseId}`
				if (this.isLesson) {
					chatRoom += `-lessons-${this.lessonId}`
				}
				return chatRoom
			},
			localStorageKey() {
				return `course-structure-${this.courseId}`
			},
			canRenderSidenav() {
				return this.isSidenavVisible && this.ready
			}
		},
		methods: {
			...mapActions('course', [
				'setup'
			]),
			// ...mapActions(['addBreadcrumb', 'removeBreadcrumb']),
		},
		created() {
			this.setup(this.courseId)
		},
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
