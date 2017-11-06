<template>
	<div class="moderators-feed">
		<div class="quick-filters">
			<span>Szybkie filtry</span>
			<a v-for="(quickFilter, index) in quickFilters"
				class="panel-toggle" :class="{'is-active': quickFilter.isActive}"
				@click="toggleQuickFilter(quickFilter)"
				:key="index"
			>
				{{quickFilter.name}}
				<span class="icon is-small">
					<i class="fa" :class="[quickFilter.isActive ? 'fa-check-circle' : 'fa-circle-o']"></i>
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
				quickFilters: [
					{
						name: 'Przypisane do mnie',
						isActive: false,
						query: () => {
							return {
								where: [['assignee_id', this.currentUserId]]
							}
						}
					},
					{
						name: 'Niezrobione',
						isActive: false,
						query: () => {
							return {
								whereNotIn:['status', ['done']]
							}
						}
					},
					{
						name: 'Nieprzypisane',
						isActive: false,
						query:() => {
							return {
								whereNull: ['assignee_id']
							}
						}
					}
				]
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
			...mapActions('tasks', ['pullTasks', 'updateTask', 'filterTasks']),
			...mapActions(['toggleOverlay']),
			onChangePage(page) {
				this.toggleOverlay({source: 'moderatorsFeed', display: true})
				this.pullTasks({params: {page}})
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
						this.toggleOverlay({source: 'moderatorsFeed', display: false})
					})
			},
			toggleQuickFilter(quickFilter) {
				quickFilter.isActive = !quickFilter.isActive

				this.filter()
			},
			filter() {
				const activeFilters = this.quickFilters.filter(filter => filter.isActive)
				let query = {}

				activeFilters.forEach(filter => {
					query = {...query, ...filter.query()}
				})

				this.filterTasks({params: {query}})
			}
		},
		mounted() {
			this.toggleOverlay({source: 'moderatorsFeed', display: true})

			axios.post(getApiUrl('user_profiles/.search'), {
				query: {
					whereHas: {
						roles: {
							whereIn: ['roles.name', ['moderator', 'admin']]
						}
					},
				}
			}).then(({data: {...users}}) => {
				this.moderators = Object.values(users)
				this.toggleOverlay({source: 'moderatorsFeed', display: false})
			})

			document.addEventListener('click', this.clickHandler)
		},
		beforeDestroy() {
			document.removeEventListener('click', this.clickHandler)
		}
	}
</script>
