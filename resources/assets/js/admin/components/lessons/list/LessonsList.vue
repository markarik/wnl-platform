<template>
	<div class="lessons-list">
		<wnl-paginated-list
			:resource-url="getApiUrl('lessons/.filter')"
		>
			<p class="title is-4" slot="header">
				Lista lekcji
				<router-link class="button is-success" :to="{name: 'lessons', params: { lessonId: 'new' } }">+ Dodaj lekcję</router-link>
			</p>
			<template slot="list" slot-scope="slotParams">
				<wnl-lesson-list-item v-for="lesson in slotParams.list"
					:key="lesson.id"
					:name="lesson.name"
					:id="lesson.id">
				</wnl-lesson-list-item>
			</template>
		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlLessonListItem from 'js/admin/components/lessons/list/LessonsListItem.vue';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

import { getApiUrl } from 'js/utils/env';

export default {
	name: 'LessonsList',
	components: {
		WnlLessonListItem,
		WnlPaginatedList
	},
	methods: {
		getApiUrl
	}
};
</script>
