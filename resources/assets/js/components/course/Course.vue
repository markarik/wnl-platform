<template>
	<div class="wnl-app-layout wnl-course-layout" v-if="ready">
		<wnl-sidenav-slot
			:is-visible="canRenderSidenav"
			:is-detached="!isSidenavMounted"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
			<wnl-course-navigation
				v-if="$currentEditionParticipant.isAllowed('access')"
				:is-lesson="isLesson"
			>
			</wnl-course-navigation>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column" v-if="$currentEditionParticipant.isAllowed('access')">
			<router-view :presence-channel="presenceChannel"/>
		</div>
		<wnl-splash-screen v-else/>
		<wnl-sidenav-slot
			class="course-chat"
			:is-visible="isChatVisible"
			:is-detached="!isChatMounted"
			:has-chat="true"
		>
			<div v-if="isLesson" class="lesson-active-users-container">
				<wnl-active-users message="dashboard.activeUsersLessons" :channel="presenceChannel"/>
			</div>
			<wnl-public-chat :rooms="chatRooms" v-if="$currentEditionParticipant.isAllowed('access')"/>
		</wnl-sidenav-slot>
		<div v-if="$currentEditionParticipant.isAllowed('access') && isChatToggleVisible" class="wnl-chat-toggle">
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
import { mapGetters, mapActions } from 'vuex';
import ActiveUsers from 'js/components/course/dashboard/ActiveUsers';
import PublicChat from 'js/components/chat/PublicChat.vue';
import Navigation from 'js/components/course/Navigation';
import SidenavSlot from 'js/components/global/SidenavSlot';
import MainNav from 'js/components/MainNav';
import SplashScreen from 'js/components/global/splashscreens/SplashScreen';
import { breadcrumb } from 'js/mixins/breadcrumb';
import withChat from 'js/mixins/with-chat';
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';

export default {
	name: 'Course',
	perimeters: [currentEditionParticipant],
	components: {
		'wnl-active-users': ActiveUsers,
		'wnl-course-navigation': Navigation,
		'wnl-public-chat': PublicChat,
		'wnl-sidenav-slot': SidenavSlot,
		'wnl-main-nav': MainNav,
		'wnl-splash-screen': SplashScreen,
	},
	props: ['courseId', 'lessonId', 'screenId', 'slide'],
	computed: {
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
			return typeof this.lessonId !== 'undefined' && this.isLessonAvailable(this.lessonId);
		},
		chatRooms() {
			let courseChatRoom = `courses-${this.courseId}`,
				lessonChatRoom = courseChatRoom + `-lessons-${this.lessonId}`;
			if (this.isLesson) {
				return [
					{name: '#lekcja', channel: lessonChatRoom},
					{name: '#aula', channel: courseChatRoom}
				];
			}

			return [
				{name: '#aula', channel: courseChatRoom},
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
