<template>
	<div class="lessons-list">
		<p class="title is-4">
			Lista lekcji
			<router-link class="button is-success" :to="{name: 'lessons', params: { lessonId: 'new' } }">+ Dodaj lekcję</router-link>
		</p>
		<wnl-lesson-list-item v-for="lesson in allLessons"
			:key="lesson.id"
			:name="lesson.name"
			:id="lesson.id">
		</wnl-lesson-list-item>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import axios from 'axios'
	import {mapGetters} from 'vuex'

	import LessonsListItem from 'js/admin/components/lessons/list/LessonsListItem.vue'

	import { getApiUrl } from 'js/utils/env'

	export default {
		name: 'LessonsList',
		components: {
			'wnl-lesson-list-item': LessonsListItem,
		},
		computed: {
			...mapGetters('lessons', ['allLessons'])
		},
		mounted() {
			axios.get(getApiUrl('lessons/all'))
				.then((response) => {
					this.lessons = response.data
				})
		}
	}
</script>
