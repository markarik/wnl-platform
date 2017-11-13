<template>
	<div class="moderators-feed">
		<div class="quick-filters">
			<span v-t="'tasks.quickFilters.title'"/>
			<a v-for="(quickFilter, index) in quickFilters"
				class="panel-toggle" :class="{'is-active': quickFilter.isActive}"
				@click="onQuickFilterChange(quickFilter)"
				:key="index"
				v-t="quickFilter.name"
			/>
			<span>Sortuj</span>
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
		<wnl-alert v-if="updatedTasks.length > 0" type="info" @onDismiss="updatedTasks.length = 0">
			<div class="notification-container">
				<span class="notification-text">Pojawiły się nowe notyfikacje.</span>
				<button @click="onRefresh" class="button" v-t="'ui.action.refresh'"/>
			</div>
		</wnl-alert>
		<div v-if="emptyTasks" v-t="'tasks.empty'"/>
		<div v-else>
			<wnl-task class="wnl-task-card" v-for="(task, index) in tasks"
				:key="index"
				:task="task"
				:availableModerators="moderators"
				:closeDropdown="bodyClicked"
				@statusSelected="updateTask"
				@dropdownClosed="onDropdownClosed"
				@assign="updateTask"
			/>
			<wnl-pagination v-if="paginationMeta.lastPage > 1"
				:currentPage="paginationMeta.currentPage"
				:lastPage="paginationMeta.lastPage"
				@changePage="onChangePage"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-task-card
		margin: $margin-base auto

	.notification-container
		display: flex

	.notification-text
		width: 100%

	.button
		border-radius: 0

	.quick-filters
		margin-bottom: $margin-big
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'
	import {nextTick} from 	'vue'
	import { getApiUrl } from 'js/utils/env'
	import {scrollToTop} from 'js/utils/animations'

	import Pagination from 'js/components/global/Pagination'
	import Task from 'js/components/moderators/ModeratorsTask'
	import Alert from 'js/components/global/GlobalAlert'

	export default {
		name: 'ModeratorsFeed',
		components: {
			'wnl-pagination': Pagination,
			'wnl-task': Task,
			'wnl-alert': Alert
		},
		data() {
			return {
				moderators: [],
				bodyClicked: false,
				quickFilters: this.initialQuickFilters(),
				sorting: this.initialSorting()
			}
		},
		computed: {
			...mapGetters('tasks', ['tasks', 'paginationMeta', 'updatedTasks']),
			...mapGetters(['currentUserId']),
			emptyTasks() {
				return Object.keys(this.tasks).length === 0
			}
		},
		methods: {
			...mapActions('tasks', ['pullTasks', 'updateTask']),
			...mapActions(['toggleOverlay']),
			onChangePage(page) {
				this.toggleOverlay({source: 'moderatorsFeed', display: true})
				this.pullTasks({page, query: this.buildQuery()})
					.then(() => {
						scrollToTop()
						this.toggleOverlay({source: 'moderatorsFeed', display: false})
					})
			},
			clickHandler() {
				this.bodyClicked = true
			},
			onDropdownClosed() {
				nextTick(() => {
					this.bodyClicked = false
				})
			},
			onRefresh() {
				this.toggleOverlay({source: 'moderatorsFeed', display: true})
				this.pullTasks()
					.then(() => {
						scrollToTop()
						this.quickFilters = this.initialQuickFilters()

						this.toggleOverlay({source: 'moderatorsFeed', display: false})
					})
			},
			onQuickFilterChange(quickFilter) {
				quickFilter.isActive = !quickFilter.isActive

				this.pullTasks({query: this.buildQuery()})
			},
			onSortClick(sort) {
				if (sort.isActive) {
					sort.dir = sort.dir === 'desc' ? 'asc' : 'desc'
				} else {
					this.sorting.forEach(sort => sort.isActive = false)
					sort.isActive = true
				}

				this.pullTasks({...this.buildQuery()})
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
						name: 'Po dacie stworzenia',
						dir: 'desc',
						isActive: true,
						order: (dir = 'desc') => {
							return {'created_at': dir}
						}
					}
				]
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
			const promisedTasks = this.pullTasks({query: this.buildQuery()})

			Promise.all([promisedModerators, promisedTasks])
				.then(([moderatorsResponse, tasks]) => {
					const {data: {...users}} = moderatorsResponse
					this.moderators = Object.values(users)
					this.toggleOverlay({source: 'moderatorsFeed', display: false})
				});

			document.addEventListener('click', this.clickHandler)
		},
		beforeDestroy() {
			document.removeEventListener('click', this.clickHandler)
		}
	}
</script>
