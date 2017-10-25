<template>
	<div class="moderators-feed">
		<div class="pagination-container">
			<wnl-pagination v-if="paginationMeta.lastPage && paginationMeta.lastPage > 1"
				:currentPage="paginationMeta.currentPage"
				:lastPage="paginationMeta.lastPage"
				@changePage="changePage"
			/>
		</div>
		<div v-for="(task, index) in tasks" :key="index">
			{{task.context.event}}
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import { mapGetters } from 'vuex'

	import Pagination from 'js/components/global/Pagination'

	export default {
		name: 'ModeratorsFeed',
		components: {
			'wnl-pagination': Pagination
		},
		computed: {
			...mapGetters('tasks', ['tasks', 'paginationMeta']),
		},
		methods: {
			changePage() {
				console.log('...should change page...')
			}
		}
	}
</script>
