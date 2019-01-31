const TIME_FOR_QUESTION = 1.2; // one minute 12 seconds

export const timeBaseOnQuestions = (questionsCount) => {
	return Math.floor(questionsCount * TIME_FOR_QUESTION);
};