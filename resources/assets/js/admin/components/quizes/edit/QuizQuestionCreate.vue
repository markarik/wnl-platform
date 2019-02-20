<template>
	<wnl-quiz-question-editor :isEdit="false"></wnl-quiz-question-editor>
</template>

<script>
import QuizQuestionEditor from 'js/admin/components/quizes/edit/QuizQuestionEditor';

export default {
	name: 'QuizQuestionCreate',
	components: {
		'wnl-quiz-question-editor': QuizQuestionEditor
	}
};
</script>
