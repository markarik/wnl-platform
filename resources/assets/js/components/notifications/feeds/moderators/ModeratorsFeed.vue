<template>
	<div class="moderators-feed">
		<div v-for="(task, index) in tasks" :key="index">
			{{task.context.event}}
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import { mapGetters } from 'vuex'


	export default {
		name: 'ModeratorsFeed',
		computed: {
			...mapGetters('tasks', ['tasks']),
		},
	}
</script>
