<template>
	<div class="wnl-app-layout wnl-course-layout" v-if="ready">
		<wnl-sidenav-slot
			:is-visible="canRenderSidenav"
			:is-detached="!isSidenavMounted"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
			<wnl-course-navigation :is-lesson="isLesson">
			</wnl-course-navigation>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<router-view :presence-channel="presenceChannel"/>
		</div>
		<wnl-sidenav-slot
			class="course-chat"
			:is-visible="isChatVisible"
			:is-detached="!isChatMounted"
			:has-chat="true"
		>
			<div v-if="isLesson" class="lesson-active-users-container">
				<wnl-active-users message="dashboard.activeUsersLessons" :channel="presenceChannel"/>
			</div>
			<wnl-public-chat :rooms="chatRooms"/>
		</wnl-sidenav-slot>
		<div v-if="isChatToggleVisible" class="wnl-chat-toggle">
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
import { mapGetters, mapActions, mapState } from 'vuex';
import ActiveUsers from 'js/components/course/dashboard/ActiveUsers';
import PublicChat from 'js/components/chat/PublicChat.vue';
import Navigation from 'js/components/course/Navigation';
import SidenavSlot from 'js/components/global/SidenavSlot';
import MainNav from 'js/components/MainNav';
import { breadcrumb } from 'js/mixins/breadcrumb';
import withChat from 'js/mixins/with-chat';

export default {
	name: 'Course',
	components: {
		'wnl-active-users': ActiveUsers,
		'wnl-course-navigation': Navigation,
		'wnl-public-chat': PublicChat,
		'wnl-sidenav-slot': SidenavSlot,
		'wnl-main-nav': MainNav,
	},
	props: ['courseId', 'lessonId', 'screenId', 'slide'],
	computed: {
		...mapState('course', ['isPlanBuilderEnabled']),
		...mapGetters('course', ['isLessonAvailable', 'ready']),
		...mapGetters([
			'isSidenavVisible',
			'isSidenavMounted',
			'isChatMounted',
			'isChatVisible',
			'isChatToggleVisible',
		]),
		context() {
			return {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
				slide: this.slide,
			};
		},
		isLesson() {
			// Allow users to look at unavailable lessons when PlanBuilder is enabled
			return typeof this.lessonId !== 'undefined' && (this.isLessonAvailable(this.lessonId) || this.isPlanBuilderEnabled);
		},
		chatRooms() {
			let courseChatRoom = `courses-${this.courseId}`,
				lessonChatRoom = courseChatRoom + `-lessons-${this.lessonId}`;
			if (this.isLesson) {
				return [
					{ name: '#lekcja', channel: lessonChatRoom },
					{ name: '#aula', channel: courseChatRoom }
				];
			}

			return [
				{ name: '#aula', channel: courseChatRoom },
			];
		},
		canRenderSidenav() {
			return this.isSidenavVisible && this.ready;
		},
		presenceChannel() {
			return `lesson.${this.lessonId}`;
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
};
</script>
