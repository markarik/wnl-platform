<template>
	<div class="moderators-feed">
		<wnl-task v-for="(task, index) in tasks"
			:key="index"
			:task="task"
			@statusSelected="onStatusSelected"
		/>
		<wnl-pagination v-if="paginationMeta.lastPage > 1"
			:currentPage="paginationMeta.currentPage"
			:lastPage="paginationMeta.lastPage"
			@changePage="onChangePage"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import Pagination from 'js/components/global/Pagination'
	import Task from 'js/components/notifications/feeds/moderators/ModeratorsTask'

	export default {
		name: 'ModeratorsFeed',
		components: {
			'wnl-pagination': Pagination,
			'wnl-task': Task
		},
		computed: {
			...mapGetters('tasks', ['tasks', 'paginationMeta']),
		},
		methods: {
			...mapActions('tasks', ['pullTasks', 'updateTask']),
			onChangePage(page) {
				this.pullTasks({params: {page}})
			},
			onStatusSelected(payload) {
				this.updateTask(payload)
			}
		}
	}
</script>
