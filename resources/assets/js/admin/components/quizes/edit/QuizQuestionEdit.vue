<template>
	<wnl-quiz-question-editor :quiz-question-id="$route.params.quizQuestionId" />
</template>

<script>
import WnlQuizQuestionEditor from 'js/admin/components/quizes/edit/QuizQuestionEditor';

export default {
	components: {
		WnlQuizQuestionEditor
	}
};
</script>
