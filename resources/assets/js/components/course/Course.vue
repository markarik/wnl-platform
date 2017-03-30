<template>
	<div class="wnl-app-layout">
		<div class="wnl-left wnl-app-layout-left">
			<wnl-course-navigation
				:context="context"
				:isLesson="isLesson"
				v-if="courseReady">
			</wnl-course-navigation>
		</div>
		<div class="wnl-middle wnl-app-layout-main">
			<router-view v-if="courseReady"></router-view>
		</div>
		<div class="wnl-right wnl-app-layout-right">
			<wnl-chat :room="chatRoom"></wnl-chat>
		</div>
	</div>
</template>

<script>
	import axios from 'axios'
	import store from 'store'
	import Navigation from 'js/components/course/Navigation.vue'
	import Chat from 'js/components/chat/Chat.vue'
	import { getApiUrl } from 'js/utils/env'
	import { mapGetters, mapActions } from 'vuex'
	import * as mutations from 'js/store/mutations-types'

	export default {
		name: 'Course',
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
		computed: {
			...mapGetters(['courseReady', 'courseName', 'courseStructure']),
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
		},
		components: {
			'wnl-course-navigation': Navigation,
			'wnl-chat': Chat
		},
		methods: {
			...mapActions(['courseSetup'])
		},
		created() {
			this.courseSetup(this.courseId)
		}
	}
</script>
