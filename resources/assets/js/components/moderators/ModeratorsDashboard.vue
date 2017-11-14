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
				<div class="quick-actions-container">
					<div class="quick-action">
						<span v-t="'tasks.quickFilters.title'"/>
						<a v-for="(quickFilter, index) in quickFilters"
							class="panel-toggle" :class="{'is-active': quickFilter.isActive}"
							@click="onQuickFilterChange(quickFilter)"
							:key="index"
							v-t="quickFilter.name"
						/>
					</div>
					<div class="quick-action">
						<span v-t="'tasks.sorting.title'"/>
						<a v-for="(sort, index) in sorting"
							class="panel-toggle"
							:class="{'is-active': sort.isActive}"
							@click="onSortClick(sort)"
							:key="index"
						>
							{{sort.name}}
							<span class="icon is-small">
								<i class="fa" :class="[sort.dir === 'desc' ? 'fa-arrow-down' : 'fa-arrow-up']"></i>
							</span>
						</a>
					</div>
				</div>
				<wnl-alert v-if="updatedTasks.length > 0" type="info" @onDismiss="updatedTasks.length = 0">
					<div class="notification-container">
						<span class="notification-text">Pojawiły się nowe notyfikacje.</span>
						<button @click="fetchLatest" class="button" v-t="'ui.action.refresh'"/>
					</div>
				</wnl-alert>

				<wnl-moderators-feed
					@refresh="onRefresh"
				/>
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

	.notification-container
		display: flex

		.notification-text
			width: 100%

		.button
			border-radius: 0

	.quick-actions-container
		margin-bottom: $margin-big

		.quick-action
			margin-bottom: $margin-base
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import { getApiUrl } from 'js/utils/env'
	import {scrollToTop} from 'js/utils/animations'

	import MainNav from 'js/components/MainNav'
	import ModeratorsFeed from 'js/components/moderators/ModeratorsFeed'
	import PublicChat from 'js/components/chat/PublicChat'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import Accordion from 'js/components/global/accordion/Accordion'
	import withChat from 'js/mixins/with-chat'
	import Alert from 'js/components/global/GlobalAlert'


	export default {
		name: 'ModeratorsDashboard',
		data() {
			return {
				quickFilters: this.initialQuickFilters(),
				sorting: this.initialSorting()
			}
		},
		components: {
			'wnl-main-nav': MainNav,
			'wnl-moderators-feed': ModeratorsFeed,
			'wnl-public-chat': PublicChat,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-accordion': Accordion,
			'wnl-alert': Alert
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
			...mapGetters('tasks', ['updatedTasks']),
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
							value: "slide"
						}, {
							name: "Pytania Kontrolne",
							value: "quiz_question"
						}, {
							name: "Pytania w Dyskusjach (QnA)",
							value: "qna_question"
						}, {
							name: "Odpowiedzi w Dyskusjach (QnA)",
							value: "qna_answer"
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
			...mapActions(['toggleChat', 'currentUserId', 'toggleOverlay']),
			...mapActions('tasks', ['pullTasks']),
			onItemToggled(item) {
				console.log('item...', item)
			},
			buildQuery() {
				const activeFilters = this.quickFilters.filter(filter => filter.isActive)
				const activeSorting = this.sorting.filter(filter => filter.isActive)
				let query = {}
				let order = {}

				activeFilters.forEach(filter => {
					query = {...query, ...filter.query()}
				})

				activeSorting.forEach(filter => {
					order = {...order, ...filter.order(filter.dir)}
				})

				return {
					query,
					order
				}
			},
			onRefresh({...params}) {
				this.toggleOverlay({source: 'moderatorsFeed', display: true})
				this.pullTasks({...this.buildQuery(), ...params})
					.then(() => {
						scrollToTop()
						this.toggleOverlay({source: 'moderatorsFeed', display: false})
					})
			},
			fetchLatest() {
				this.quickFilters = this.initialQuickFilters()
				this.sorting = this.initialSorting()
				this.onRefresh()
			},
			initialQuickFilters() {
				return [
						{
							name: this.$t('tasks.quickFilters.filters.my'),
							isActive: false,
							query: () => {
								return {
									where: [['assignee_id', this.currentUserId]]
								}
							}
						}, {
							name: this.$t('tasks.quickFilters.filters.notDone'),
							isActive: true,
							query: () => {
								return {
									whereNotIn:['status', ['done']]
								}
							}
						}, {
						name: this.$t('tasks.quickFilters.filters.unassigned'),
						isActive: false,
						query:() => {
							return {
								whereNull: ['assignee_id']
							}
						}
					}
				]
			},
			initialSorting() {
				return [
					{
						name: this.$t('tasks.sorting.options.byCreatedAt'),
						dir: 'desc',
						isActive: true,
						order: (dir = 'desc') => {
							return {'created_at': dir}
						}
					},
					{
						name: this.$t('tasks.sorting.options.byUpdatedAt'),
						dir: 'desc',
						isActive: false,
						order: (dir = 'desc') => {
							return {'updated_at': dir}
						}
					}
				]
			},
			onQuickFilterChange(quickFilter) {
				quickFilter.isActive = !quickFilter.isActive

				this.pullTasks(this.buildQuery())
			},
			onSortClick(sort) {
				if (sort.isActive) {
					sort.dir = sort.dir === 'desc' ? 'asc' : 'desc'
				} else {
					this.sorting.forEach(sort => sort.isActive = false)
					sort.isActive = true
				}

				this.pullTasks(this.buildQuery())
			}
		},
		mounted() {
			this.toggleOverlay({source: 'moderatorsFeed', display: true})

			const promisedModerators = axios.post(getApiUrl('user_profiles/.search'), {
				query: {
					whereHas: {
						roles: {
							whereIn: ['roles.name', ['moderator', 'admin']]
						}
					},
				}
			})
			const promisedTasks = this.pullTasks({...this.buildQuery()})

			Promise.all([promisedModerators, promisedTasks])
				.then(([moderatorsResponse, tasks]) => {
					const {data: {...users}} = moderatorsResponse
					this.moderators = Object.values(users)
					this.toggleOverlay({source: 'moderatorsFeed', display: false})
				});
		},
		watch: {
			'$route.query.chatChannel' (newVal) {
				newVal && !this.isChatVisible && this.toggleChat();
			}
		},
	}
</script>
