<template>
	<div class="moderators-feed">
		<div v-if="emptyTasks" v-t="'tasks.empty'" />
		<div v-else>
			<wnl-task
				class="wnl-task-card"
				v-for="(task, index) in tasks"
				:key="index"
				:task="task"
				:available-moderators="moderators"
				:close-dropdown="closeDropdowns"
				@statusSelected="updateTask"
				@assign="updateTask"
			/>
			<wnl-pagination
				v-if="paginationMeta.lastPage > 1"
				:current-page="paginationMeta.currentPage"
				:last-page="paginationMeta.lastPage"
				@changePage="(page) => $emit('changePage', {page})"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-task-card
		margin: $margin-base auto
</style>

<script>
import { mapGetters, mapActions } from 'vuex';

import Pagination from 'js/components/global/Pagination';
import Task from 'js/components/moderators/ModeratorsTask';

export default {
	name: 'ModeratorsFeed',
	components: {
		'wnl-pagination': Pagination,
		'wnl-task': Task,
	},
	props: {
		moderators: {
			type: Array,
			required: true
		},
		closeDropdowns: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			bodyClicked: false,
		};
	},
	computed: {
		...mapGetters('tasks', ['tasks', 'paginationMeta']),
		...mapGetters(['currentUserId']),
		emptyTasks() {
			return Object.keys(this.tasks).length === 0;
		}
	},
	methods: {
		...mapActions('tasks', ['updateTask']),
		...mapActions(['toggleOverlay']),
	},
};
</script>
