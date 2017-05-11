<template>
	<div>
		<p class="title is-3">Edycja lekcji</p>
		<div class="lessons-container">
			<wnl-lesson-editor v-if="lessonId"></wnl-lesson-editor>
			<wnl-lessons-list v-else></wnl-lessons-list>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import LessonsList from 'js/admin/components/lessons/list/LessonsList.vue'
	import LessonEditor from 'js/admin/components/lessons/edit/LessonEditor.vue'

	export default {
		name: 'Lessons',
		components: {
			'wnl-lessons-list': LessonsList,
			'wnl-lesson-editor': LessonEditor,
		},
		computed: {
			lessonId() {
				return this.$route.params.lessonId
			}
		},
	}
</script>
