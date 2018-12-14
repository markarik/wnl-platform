import {getApiUrl} from 'js/utils/env';

const saveQuizProgress = (userId, state, recordedAnswers = []) => {
	if (!state.retry) {
		const { setId, setName, attempts, isComplete, questionsIds, quiz_questions: quizQuestionsRaw } = state;
		const quiz_questions = {};
		Object.keys(quizQuestionsRaw).forEach((questionId) => {
			quiz_questions[questionId] = {
				isResolved: quizQuestionsRaw[questionId].isResolved
			};
		});
		axios.put(getApiUrl(`quiz_results/${userId}/quiz/${setId}`), {
			quiz: {
				quiz_questions, setId, setName, attempts, isComplete, questionsIds,
			},
			recordedAnswers
		});
	}
};

const getQuizProgress = async (setId, userId) => {
	const { data: { quiz } } = await axios.get(getApiUrl(`quiz_results/${userId}/quiz/${setId}`));
	return quiz;
};


export default {
	getQuizProgress,
	saveQuizProgress
};
