export function configValue(key) {
	return $wnl.config[key];
}

export function resource(key) {
	return configValue('papi').resources[key];
}

export const invisibleTags = [
	'Prezentacja',
];

export const modelToResourceMap = {
	'App\\Models\\QnaQuestion': 'qna_questions',
	'App\\Models\\QuizQuestion': 'quiz_questions',
	'App\\Models\\Slide': 'slides',
	'App\\Models\\QnaAnswer': 'qna_answers',
};

export const getModelByResource = (resource) => {
	return Object.keys(modelToResourceMap).find((model) => modelToResourceMap[model] === resource);
};

