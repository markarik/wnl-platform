import { resources } from 'js/utils/constants';

export function configValue(key) {
	return $wnl.config[key];
}

export function resource(key) {
	return configValue('papi').resources[key];
}

export const modelToResourceMap = {
	'App\\Models\\QnaQuestion': 'qna_questions',
	'App\\Models\\QuizQuestion': 'quiz_questions',
	'App\\Models\\Slide': 'slides',
	'App\\Models\\QnaAnswer': 'qna_answers',
	'App\\Models\\Lesson': resources.lessons,
	'App\\Models\\Group': resources.groups
};

export const getModelByResource = (resource) => {
	return Object.keys(modelToResourceMap).find((model) => modelToResourceMap[model] === resource);
};

