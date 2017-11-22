<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			direction="column"
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<wnl-accordion
					:dataSource="filters"
					:config="accordionConfig"
					:loading="false"
					@itemToggled="onItemToggled"
					class="full-width"
				/>
			<div class="filter-title full-width">
				<span class="text">Filtrowanie Po Ogarniaczu</span>
			</div>
			<wnl-moderators-autocomplete
				:show="showAutocomplete"
				:usersList="moderators"
				:onItemChosen="search"
				:initialValue="autocompleteUser.full_name"
				@close="showAutocomplete = false"
				@show="showAutocomplete = true"
				@clear="search"
			/>
			<wnl-accordion
					:dataSource="subjectFilters"
					:config="accordionConfigByLesson"
					:loading="false"
					@itemToggled="onTagSelect"
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
						<button @click="onRefresh" class="button" v-t="'ui.action.refresh'"/>
					</div>
				</wnl-alert>

				<wnl-moderators-feed
					v-if="moderators.length > 0"
					:moderators="moderators"
					:closeDropdowns="bodyClicked"
					@changePage="fetchTasks"
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

	.filter-title
		background: $color-background-light-gray
		font-size: $font-size-minus-1
		letter-spacing: 1px
		padding: $margin-medium $margin-small $margin-medium $margin-medium
		text-transform: uppercase

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
	import {nextTick} from 	'vue'

	import { getApiUrl } from 'js/utils/env'
	import {scrollToTop} from 'js/utils/animations'
	import {FILTER_TYPES, buildFiltersByPath, parseFilters} from 'js/services/apiFiltering'

	import MainNav from 'js/components/MainNav'
	import ModeratorsFeed from 'js/components/moderators/ModeratorsFeed'
	import ModeratorsAutocomplete from 'js/components/moderators/ModeratorsAutocomplete'
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
				sorting: this.initialSorting(),
				filters: this.initialFilters(),
				selectedByTypeFilters: this.buildByTypeFiltering(),
				selectedBySubjectFilters: {},
				subjectFilters: {},
				moderators: [],
				showAutocomplete: false,
				autocompleteUser: {},
				bodyClicked: false
			}
		},
		components: {
			'wnl-main-nav': MainNav,
			'wnl-moderators-feed': ModeratorsFeed,
			'wnl-moderators-autocomplete': ModeratorsAutocomplete,
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
			...mapGetters(['currentUserId']),
			chatRooms() {
				return [
					{name: '#moderatorzy', channel: 'moderatorzy'},
				]
			},
			accordionConfig() {
				return {
					disableEmpty: false,
					isMobile: false,
					expanded: ['task-subject_type'],
					selectedElements: this.activeFiltersByType
				}
			},
			accordionConfigByLesson() {
				return {
					disableEmpty: false,
					isMobile: false,
					showCounts: false,
					selectedElements: this.activeFiltersByLesson
				}
			},
			activeFiltersByType() {
				return Object.keys(this.selectedByTypeFilters).filter(key => this.selectedByTypeFilters[key])
			},
			activeFiltersByLesson() {
				return Object.keys(this.selectedBySubjectFilters).filter(key => this.selectedBySubjectFilters[key])
			}
		},
		methods: {
			...mapActions(['toggleChat', 'toggleOverlay']),
			...mapActions('tasks', ['pullTasks']),
			onItemToggled({path, selected}) {
				this.selectedByTypeFilters[path] = selected
				this.fetchTasks()
			},
			onTagSelect({path, selected}) {
				this.selectedBySubjectFilters[path] = selected
				this.fetchTasks()
			},
			buildRequestParams() {
				const activeQuickFilters = this.quickFilters.filter(filter => filter.isActive)
				const parsedFilters = []
				activeQuickFilters.forEach(filter => {
					parsedFilters.push({
						[filter.group]: filter.value()
					})
				})
				parsedFilters.push(...parseFilters(this.activeFiltersByType, this.filters, this.currentUserId))
				parsedFilters.push(...parseFilters(this.activeFiltersByLesson, this.subjectFilters, this.currentUserId))

				if (this.autocompleteUser.user_id) {
					parsedFilters.push({
						'task-assignee': {user_id: this.autocompleteUser.user_id}
					})
				}

				const activeSorting = this.sorting.find(filter => filter.isActive)
				const order = {
					...activeSorting.order(activeSorting.dir)
				}

				return {
					filters: parsedFilters,
					order
				}
			},
			fetchTasks({...params}) {
				this.toggleOverlay({source: 'moderatorsFeed', display: true})
				this.pullTasks({...this.buildRequestParams(), ...params})
					.then(() => {
						scrollToTop()
						this.toggleOverlay({source: 'moderatorsFeed', display: false})
					})
			},
			onRefresh() {
				this.quickFilters = this.initialQuickFilters()
				this.sorting = this.initialSorting()
				this.selectedBySubjectFilters = this.buildByLessonFiltering()
				this.selectedByTypeFilters = this.buildByTypeFiltering()
				this.fetchTasks()
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
			initialQuickFilters() {
				return [
					{
						group: 'task-assignee',
						value: () => ({user_id: this.currentUserId}),
						isActive: true,
						name: this.$t('tasks.quickFilters.filters.my')
					},
					{
						group: 'task-status',
						value: () => ({
							excluded: ['done'],
							included: []
						}),
						isActive: true,
						name: this.$t('tasks.quickFilters.filters.notDone')
					},
					{
						group: 'task-assignee',
						value: () => ({user_id: null}),
						isActive: false,
						name: this.$t('tasks.quickFilters.filters.unassigned')
					}
				]
			},
			initialFilters() {
				return {
					'task-subject_type': {
						name: this.$t('tasks.filters.byType.title'),
						type: FILTER_TYPES.LIST,
						items: [{
							name: this.$t('tasks.filters.byType.slide'),
							value: "slide",
						}, {
							name: this.$t('tasks.filters.byType.quiz_question'),
							value: "quiz_question"
						}, {
							name: this.$t('tasks.filters.byType.qna'),
							value: "qna"
						}],
					},
				}
			},
			buildByTypeFiltering() {
				return buildFiltersByPath(this.initialFilters())
			},
			buildByLessonFiltering() {
				return buildFiltersByPath(this.subjectFilters)
			},
			onQuickFilterChange(quickFilter) {
				quickFilter.isActive = !quickFilter.isActive
				this.pullTasks(this.buildRequestParams())
			},
			onSortClick(sort) {
				if (sort.isActive) {
					sort.dir = sort.dir === 'desc' ? 'asc' : 'desc'
				} else {
					this.sorting.forEach(sort => sort.isActive = false)
					sort.isActive = true
				}

				this.pullTasks(this.buildRequestParams())
			},
			search(user = {}) {
				const {filters, ...rest} = this.buildRequestParams();
				this.autocompleteUser = user

				this.pullTasks(this.buildRequestParams())
				.catch(() => {
					this.autocompleteUser = {}
				})
				this.showAutocomplete = false
			},
			clickHandler() {
				this.bodyClicked = true
				this.showAutocomplete = false
				nextTick(() => {
					this.bodyClicked = false
				})
			},
			parseSubjectFilters(filters) {
				return {
					'task-labels': {
						name: this.$t('tasks.filters.byLesson.title'),
						type: FILTER_TYPES.LIST,
						items: filters.map(filter => {
							return {
								name: filter.name,
								value: filter.name,
								items: filter.categories.map(childFilter => {
									return {
										name: childFilter.name,
										value: childFilter.name
									}
								})
							}
						})
					}
				}
			}
		},
		mounted() {
			document.addEventListener('click', this.clickHandler)

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
			const promisedTasks = this.pullTasks(this.buildRequestParams())
			const promisedFilters = axios.post(getApiUrl('tasks/.filterList'))

			Promise.all([promisedModerators, promisedTasks, promisedFilters])
				.then(([moderatorsResponse, tasks, filters]) => {
					const {data: {...users}} = moderatorsResponse
					this.moderators = Object.values(users)
					this.toggleOverlay({source: 'moderatorsFeed', display: false})
					this.subjectFilters = this.parseSubjectFilters(filters.data)
					this.selectedBySubjectFilters = this.buildByLessonFiltering()
				}).catch(error => {
					this.toggleOverlay({source: 'moderatorsFeed', display: false})
					this.$store.dispatch('addAlert', {
						text: this.$t('ui.error.somethingWentWrongUnofficial'),
						type: 'error'
					});
					$wnl.logger.error(error);
				});
		},
		beforeDestroy() {
			document.removeEventListener('click', this.clickHandler)
		},
		watch: {
			'$route.query.chatChannel' (newVal) {
				newVal && !this.isChatVisible && this.toggleChat();
			}
		},
	}
</script>
