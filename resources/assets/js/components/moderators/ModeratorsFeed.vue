<template>
	<div class="moderators-feed">
		<wnl-alert v-if="updatedTasks.length > 0" type="info">
			<div class="notification-container">
				<span class="notification-text">Pojawiły się nowe notyfikacje.</span>
				<button @click="onRefresh" class="button">Odśwież</button>
			</div>
		</wnl-alert>
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
	import { getApiUrl } from 'js/utils/env'
	import {nextTick} from 	'vue'

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
				bodyClicked: false
			}
		},
		computed: {
			...mapGetters('tasks', ['tasks', 'paginationMeta', 'updatedTasks']),
		},
		methods: {
			...mapActions('tasks', ['pullTasks', 'updateTask']),
			...mapActions(['toggleOverlay']),
			onChangePage(page) {
				this.toggleOverlay({source: 'moderatorsFeed', display: true})
				this.pullTasks({params: {page}})
					.then(() => {
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
						this.toggleOverlay({source: 'moderatorsFeed', display: false})
					})
			}
		},
		mounted() {
			this.toggleOverlay({source: 'moderatorsFeed', display: true})

			axios.post(getApiUrl('user_profiles/.search'), {
				query: {
					whereHas: {
						roles: {
							// use role name not id
							whereIn: ['roles.id', [1,2]]
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
