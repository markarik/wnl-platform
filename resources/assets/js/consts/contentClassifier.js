export const CONTENT_TYPES = {
	ANNOTATION: 'annotation',
	FLASHCARD: 'flashcard',
	QUIZ_QUESTION: 'quizQuestion',
	SLIDE: 'slide',
};

export const CONTENT_TYPE_TO_RESOURCE_ROUTE = {
	[CONTENT_TYPES.ANNOTATION]: 'annotations',
	[CONTENT_TYPES.QUIZ_QUESTION]: 'quiz_questions',
	[CONTENT_TYPES.FLASHCARD]: 'flashcards',
	[CONTENT_TYPES.SLIDE]: 'slides'
};
