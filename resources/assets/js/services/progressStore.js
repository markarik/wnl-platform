import axios from 'axios';
import { get, setWith } from 'lodash';

import { getApiUrl } from 'js/utils/env';

// TODO: Mar 9, 2017 - Use config when it's ready
export const STATUS_IN_PROGRESS = 'in-progress';
export const STATUS_COMPLETE = 'complete';

const setCourseProgress = ({ courseId, profileId }, value) => {
	return axios.put(getApiUrl(`users/${profileId}/state/course/${courseId}`), value);
};

const setLessonProgress = ({ courseId, lessonId, profileId, route }, value) => {
	if (route) {
		// We need this to update last route not the previous one.
		// suspected problem: parameters order was mixed somewhere higher
		value.route = route;
	}
	return axios.put(getApiUrl(`users/${profileId}/state/course/${courseId}/lesson/${lessonId}`), {
		lesson: value
	});
};

const completeSection = (lessonState, payload) => {
	const stateWithScreen = _getScreenProgress(lessonState, payload);
	const stateWithSections = _getSectionProgress(stateWithScreen, payload);

	setLessonProgress(payload, stateWithSections);

	return stateWithSections;
};

const completeSubsection = (lessonState, payload) => {
	const { sectionId, screenId, subsectionId } = payload;
	const stateWithScreen = _getScreenProgress(lessonState, payload);
	const stateWithSections = _getSectionProgress(stateWithScreen, payload);


	setWith(stateWithSections, `screens[${screenId}].sections[${sectionId}].subsections`, {
		...get(stateWithSections, `screens[${screenId}].sections[${sectionId}].subsections`, {}),
		[subsectionId]: {
			status: STATUS_COMPLETE
		}
	}, Object);

	setLessonProgress(payload, stateWithSections);

	return stateWithSections;
};

const completeScreen = (lessonState, { screenId, route, ...payload }) => {
	lessonState.route = route;
	lessonState.screens = lessonState.screens || {};
	lessonState.screens[screenId] = lessonState.screens[screenId] || {};
	lessonState.screens[screenId].status = STATUS_COMPLETE;

	setLessonProgress(payload, lessonState);

	return lessonState;
};

const completeLesson = (courseState, payload) => {
	const lessonState = courseState.lessons[payload.lessonId];
	const updatedLessonState = {
		...lessonState,
		status: STATUS_COMPLETE,
		route: payload.route
	};
	const updatedCourseState = {
		...courseState,
		lessons: {
			...courseState.lessons,
			[payload.lessonId]: {
				status: STATUS_COMPLETE
			}
		}
	};


	setLessonProgress(payload, updatedLessonState);
	setCourseProgress(payload, updatedCourseState);

	return updatedLessonState;
};

const startLesson = (courseState, payload) => {
	const lessonState = courseState.lessons[payload.lessonId];
	const updatedLessonState = {
		...lessonState,
		status: STATUS_IN_PROGRESS,
		route: payload.route
	};
	const updatedCourseState = {
		...courseState,
		lessons: {
			...courseState.lessons,
			[payload.lessonId]: {
				status: STATUS_IN_PROGRESS
			}
		}
	};


	setLessonProgress(payload, updatedLessonState);
	setCourseProgress(payload, updatedCourseState);

	return updatedLessonState;
};

const getCourseProgress = async ({ courseId, profileId }) => {
	const { data: { lessons } = {} } = await axios.get(getApiUrl(`users/${profileId}/state/course/${courseId}`));

	return { lessons };
};

const getLessonProgress = async ({ courseId, lessonId, profileId }) => {
	const { data: { lesson } = {} } = await axios.get(getApiUrl(`users/${profileId}/state/course/${courseId}/lesson/${lessonId}`));
	return lesson;
};

const _getScreenProgress = (lessonState = {}, { route, screenId }) => {
	const updatedState = {
		screens: {},
		...lessonState,
		route,
	};

	if (!updatedState.screens[screenId]) {
		updatedState.screens[screenId] = {
			status: STATUS_IN_PROGRESS
		};
	}

	return updatedState;
};

const _getSectionProgress = (lessonState = {}, { screenId, sectionId }) => {
	const updatedState = { ...lessonState };

	if (!updatedState.screens[screenId].sections) {
		updatedState.screens[screenId].sections = {
			[sectionId]: {
				status: STATUS_COMPLETE
			}
		};
	} else {
		updatedState.screens[screenId].sections = {
			...updatedState.screens[screenId].sections,
			[sectionId]: {
				...updatedState.screens[screenId].sections[sectionId],
				status: STATUS_COMPLETE
			}
		};
	}

	return updatedState;
};

export default {
	getCourseProgress,
	getLessonProgress,
	completeSection,
	completeScreen,
	completeLesson,
	completeSubsection,
	startLesson
};
