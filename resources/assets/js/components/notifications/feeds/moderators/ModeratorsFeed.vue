<template>
	<div class="moderators-feed">
		<div v-for="(task, index) in tasks" :key="index">
			{{task.context.event}}
		</div>
		<wnl-pagination v-if="paginationMeta.lastPage > 1"
			:currentPage="paginationMeta.currentPage"
			:lastPage="paginationMeta.lastPage"
			@changePage="changePaginationPage"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

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
			...mapActions('tasks', ['changePaginationPage'])
		}
	}
</script>
