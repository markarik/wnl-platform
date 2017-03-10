<template>
	<div class="wnl-app-layout">
		<div class="wnl-app-layout-left">
			<wnl-course-navigation
				:context="context"
				:isLesson="isLesson"
				v-if="structureLoaded">
			</wnl-course-navigation>
		</div>
		<div class="wnl-app-layout-main">
			<router-view></router-view>
		</div>
		<div class="wnl-app-layout-right">
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
	import { mapGetters, mapMutations } from 'vuex'
	import * as mutations from 'js/store/mutations-types'

	export default {
		name: 'Course',
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
		data() {
			return {
				structureLoaded: false,
			}
		},
		computed: {
			...mapGetters(['courseName', 'courseStructure']),
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
			structureApiUrl() {
				return getApiUrl(`courses/${this.courseId}/nav`)
			}
		},
		components: {
			'wnl-course-navigation': Navigation,
			'wnl-chat': Chat
		},
		methods: {
			// ...mapActions(['progressSetupEdition'])
			...mapMutations([
				mutations.SET_STRUCTURE,
			])
		},
		created() {
			// if (!this.wasProgressChecked) {
			// 	this.progressSetupEdition(this.courseId)
			// }
			let storedData = store.get(this.localStorageKey)

			if (typeof storedData !== 'object') {
				axios.get(this.structureApiUrl).then((response) => {
					// store.
					this[mutations.SET_STRUCTURE](response.data)
					this.structureLoaded = true
				}).catch(console.log.bind(console))
			} else {
				this[mutations.SET_STRUCTURE](storedData)
				this.structureLoaded = true
			}
		}
	}
</script>
