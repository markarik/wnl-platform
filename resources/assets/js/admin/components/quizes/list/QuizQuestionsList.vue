<template>
	<div class="flashcards-list">
		<wnl-paginated-list
			:resource-name="'quiz_questions/.filter'"
		>
			<template slot="header">
				<h3 class="title">Lista pytań zamkniętych</h3>
				<router-link
					:to="{ name: 'quiz-creator' }"
					class="button is-success margin bottom"
				>+ Nowe pytanie</router-link>
			</template>
			<template
				slot="list"
				slot-scope="slotParams"
			>
				<wnl-quiz-question-list-item
					v-for="question in slotParams.list"
					:key="question.id"
					:text="question.text"
					:id="question.id"
				/>
			</template>

		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlQuizQuestionListItem from 'js/admin/components/quizes/list/QuizQuestionListItem';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	name: 'QuizQuestionsList',
	components: {
		WnlQuizQuestionListItem,
		WnlPaginatedList
	},
};
</script>
