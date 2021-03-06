<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted" />
			<aside class="sidenav-aside help-sidenav">
				<wnl-sidenav
					:items="sidenavItems"
					items-heading="Pomoc"
				/>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<div class="scrollable-main-container">
				<router-view
					:arguments="templateArguments"
					:slug="$route.name"
					@userEvent="onUserEvent"
				/>
			</div>
		</div>
		<wnl-sidenav-slot
			:is-visible="isChatVisible"
			:is-detached="!isChatMounted"
			:has-chat="hasChat"
		>
			<wnl-public-chat
				v-if="hasChat"
				:rooms="chatRooms"
				title="W czym możemy Ci pomóc?"
			/>
		</wnl-sidenav-slot>
		<div
			v-if="hasChat && isChatToggleVisible"
			class="wnl-chat-toggle"
			@click="toggleChat"
		>
			<span class="icon is-big">
				<i class="fa fa-chevron-left" />
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
import { mapActions, mapGetters } from 'vuex';

import MainNav from 'js/components/MainNav';
import PublicChat from 'js/components/chat/PublicChat';
import Sidenav from 'js/components/global/Sidenav';
import SidenavSlot from 'js/components/global/SidenavSlot';
import withChat from 'js/mixins/with-chat';
import context from 'js/consts/events_map/context.json';
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';

export default {
	name: 'Help',
	perimeters: [currentEditionParticipant],
	components: {
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
			'isChatToggleVisible',
			'currentUserName'
		]),
		...mapGetters('course', ['ready']),
		templateArguments() {
			return {
				currentUserName: {
					value: this.currentUserName
				}
			};
		},
		// TODO the list of items in nav should be generated automatically - PLAT-1198
		sidenavItems() {
			const navItems = [
				{
					text: 'Obsługa platformy',
					itemClass: 'has-icon',
					to: {
						name: 'help-service',
						params: {},
					},
					isDisabled: false,
					method: 'push',
					iconClass: 'fa-cog',
					iconTitle: 'Obsługa platformy',
				},
				{
					text: 'Gwarancja satysfakcji',
					itemClass: 'has-icon',
					to: {
						name: 'satisfaction-guarantee',
						params: {},
					},
					isDisabled: false,
					method: 'push',
					iconClass: 'fa-diamond',
					iconTitle: 'Gwarancja satysfakcji',
				},
				{
					text: 'Skróty klawiszowe',
					itemClass: 'has-icon',
					to: {
						name: 'key-shortcuts',
						params: {},
					},
					isDisabled: false,
					method: 'push',
					iconClass: 'fa-key',
					iconTitle: 'Skróty klawiszowe',
				}
			];

			if (this.$currentEditionParticipant.isAllowed('access')) {
				navItems.unshift(
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
						text: 'Najczęściej zadawane pytania',
						itemClass: 'has-icon',
						to: {
							name: 'help-faq',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-question-circle',
						iconTitle: 'Najczęściej zadawane pytania',
					}
				);
			}

			return navItems;
		},
		chatRooms() {
			return [
				{ name: '#pomoc', channel: 'help-tech' },
			];
		},
		hasChat() {
			return this.$currentEditionParticipant.isAllowed('access');
		}
	},
	watch: {
		'$route.query.chatChannel'(newVal) {
			newVal && !this.isChatVisible && this.toggleChat();
		}
	},
	methods: {
		...mapActions(['toggleChat']),
		onUserEvent(payload) {
			this.$trackUserEvent({
				context: context.help.value,
				...payload
			});
		}
	},
};
</script>
