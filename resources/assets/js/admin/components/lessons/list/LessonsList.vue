<template>
	<div class="lessons-list">
		<p class="title is-4">Lista lekcji</p>
		<wnl-lesson-list-item v-for="lesson in lessons"
			:name="lesson.name"
			:id="lesson.id">
		</wnl-lesson-list-item>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import LessonsListItem from 'js/admin/components/lessons/list/LessonsListItem.vue'

	export default {
		name: 'LessonsList',
		components: {
			'wnl-lesson-list-item': LessonsListItem,
		},
		data() {
			return {
				lessons: [
					{
						id: 1,
						name: 'Pierwsza',
					},
					{
						id: 2,
						name: 'Druga',
					},
				]
			}
		}
	}
</script>
