import {getCurrentUser} from './user';
import {getApiUrl} from 'js/utils/env';

const saveQuizProgress = (setId, state, recordedAnswers = []) => {
	if (!state.retry) {

		getCurrentUser().then(({user_id}) => {
			const { setId, setName, attempts, isComplete, questionsIds, quiz_questions: quizQuestionsRaw } = state
			const quiz_questions = {}
			Object.keys(quizQuestionsRaw).forEach((questionId) => {
				quiz_questions[questionId] = {
					isResolved: quizQuestionsRaw[questionId].isResolved
				}
			})
			axios.put(getApiUrl(`quiz_results/${user_id}/quiz/${setId}`), {
				quiz: {
					quiz_questions, setId, setName, attempts, isComplete, questionsIds,
				},
				recordedAnswers
			});
		});
	}
};

const getQuizProgress = (setId) => {
	return new Promise((resolve) => {
			getCurrentUser().then(({user_id}) => {
				axios.get(getApiUrl(`quiz_results/${user_id}/quiz/${setId}`)).then(({data: {quiz}}) => {
					resolve(quiz)
				});
			})
	});
};


export default {
	getQuizProgress,
	saveQuizProgress
}
