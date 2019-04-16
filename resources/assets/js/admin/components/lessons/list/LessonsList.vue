<template>
	<div class="lessons-list">
		<wnl-paginated-list
			:resource-name="'lessons/.filter'"
		>
			<p slot="header" class="title is-4">
				Lista lekcji
				<router-link class="button is-success" :to="{name: 'lessons', params: { lessonId: 'new' } }">+ Dodaj lekcję</router-link>
			</p>
			<template slot="list" slot-scope="slotParams">
				<wnl-lesson-list-item
					v-for="lesson in slotParams.list"
					:id="lesson.id"
					:key="lesson.id"
					:name="lesson.name"
				>
				</wnl-lesson-list-item>
			</template>
		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlLessonListItem from 'js/admin/components/lessons/list/LessonsListItem.vue';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	name: 'LessonsList',
	components: {
		WnlLessonListItem,
		WnlPaginatedList
	},
};
</script>
