<template>
	<wnl-quiz-question-editor :isEdit="true"></wnl-quiz-question-editor>
</template>

<script>
import QuizQuestionEditor from 'js/admin/components/quizes/edit/QuizQuestionEditor';

export default {
	name: 'QuizQuestionEdit',
	components: {
		'wnl-quiz-question-editor': QuizQuestionEditor
	}
};
</script>
