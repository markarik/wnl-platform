<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
				:isVisible="isSidenavVisible"
				:isDetached="!isSidenavMounted"
		>
			<wnl-accordion
					:dataSource="filters"
					:config="accordionConfig"
					:loading="false"
					@itemToggled="onItemToggled"
					class="full-width"
				/>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<div class="scrollable-main-container">
				<a target="_blank" href="https://calendar.google.com/calendar/embed?src=8pohe9d278hobn46cuq3rgqpgg%40group.calendar.google.com&ctz=Europe%2FWarsaw">Grafik</a>
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
	import Accordion from 'js/components/global/accordion/Accordion'

	export default {
		name: 'ModeratorsDashboard',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-moderators-feed': ModeratorsFeed,
			'wnl-public-chat': PublicChat,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-accordion': Accordion
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
			filters() {
				return {
					'by-object-type': {
						name: "Filtrowanie po typie",
						items: [{
							name: "Slajdy",
							value: "slides"
						}, {
							name: "Pytania Kontrolne",
							value: "quiz_questions"
						}, {
							name: "Dyskusje (QnA)",
							value: "qna"
						}],
						message: 'objects',
						type: 'tags'
					}
				}
			},
			accordionConfig() {
				return {
					disableEmpty: true,
					isMobile: false,
					itemsNameSource: 'questions.filters.items',
					expanded: ['by-object-type']
				}
			}
		},
		methods: {
			...mapActions(['toggleChat']),
			onItemToggled(item) {
				debugger
			}
		},
		watch: {
			'$route.query.chatChannel' (newVal) {
				newVal && !this.isChatVisible && this.toggleChat();
			}
		},
	}
</script>
