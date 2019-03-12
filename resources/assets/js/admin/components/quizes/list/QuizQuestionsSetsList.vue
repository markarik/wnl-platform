<template>
	<div class="quiz-questions-sets-list" >
		<wnl-paginated-list
			:resource-name="'quiz_sets/.filter'"
		>
			<template slot="header">
				<h3 class="title">Lista zestawów pytań zamkniętych</h3>
				<router-link
					:to="{ name: 'quiz-sets-edit', params: { quizQuestionsSetId: 'new' } }"
					class="button is-success margin bottom"
				>+ Nowy zestaw</router-link>
			</template>
			<template slot="list" slot-scope="slotParams">
				<wnl-quiz-questions-sets-list-item v-for="quizQuestionsSet in slotParams.list"
					:key="quizQuestionsSet.id"
					:name="quizQuestionsSet.name"
					:id="quizQuestionsSet.id"
				/>
			</template>
		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlQuizQuestionsSetsListItem from 'js/admin/components/quizes/list/QuizQuestionsSetsListItem';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	components: {
		WnlQuizQuestionsSetsListItem,
		WnlPaginatedList
	},
};
</script>
