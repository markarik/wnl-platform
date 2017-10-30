<template>
	<div class="moderators-feed">
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
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'
	import { getApiUrl } from 'js/utils/env'
	import {nextTick} from 	'vue'

	import Pagination from 'js/components/global/Pagination'
	import Task from 'js/components/moderators/ModeratorsTask'

	export default {
		name: 'ModeratorsFeed',
		components: {
			'wnl-pagination': Pagination,
			'wnl-task': Task
		},
		data() {
			return {
				moderators: [],
				bodyClicked: false
			}
		},
		computed: {
			...mapGetters('tasks', ['tasks', 'paginationMeta']),
		},
		methods: {
			...mapActions('tasks', ['pullTasks', 'updateTask']),
			onChangePage(page) {
				this.pullTasks({params: {page}})
			},
			clickHandler() {
				this.bodyClicked = true
			},
			onDropdownClosed() {
				nextTick(() => {
					this.bodyClicked = false
				})
			}
		},
		mounted() {
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
			})

			document.addEventListener('click', this.clickHandler)
		},
		beforeDestroy() {
			document.removeEventListener('click', this.clickHandler)
		}
	}
</script>
