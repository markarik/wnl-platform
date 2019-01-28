import _ from 'lodash';
import {set, delete as destroy} from 'vue';
import {getApiUrl} from 'js/utils/env';
import {resource} from 'js/utils/config';
import * as types from 'js/store/mutations-types';
import { modelToResourceMap } from 'js/utils/config';

// Helper functions
function getCourseApiUrl(courseId) {
	return getApiUrl(
		`${resource('courses')}/${courseId}/structure
		?include=groups.lessons.screens.sections.subsections,
		groups.lessons.screens.tags
		&user=current&exclude=screens.content`
	);
}

function _getCourseStructure(courseId = 1) {
	return axios.get(getApiUrl(`course_structure_nodes/${courseId}`), {
		params: {
			include: 'groups,lessons'
		}
	});
}

const STATUS_NONE = 'none';
const STATUS_IN_PROGRESS = 'in-progress';
const STATUS_AVAILABLE = 'available';

// Namespace
const namespaced = true;

// Initial state
const state = {
	ready: false,
	id: 0,
	name: '',
	groups: [],
	structure: {},
	lessons: {}
};

// Getters
const getters = {
	ready: state => state.ready,
	courseId: state => state.id,
	name: state => state.name,
	groups: state => state[resource('groups')],
	structure: state => state.structure,
	getGroup: state => (groupId) => state.structure[resource('groups')][groupId] || {},
	getLessons: state => state.structure[resource('lessons')] || {},
	getRequiredLessons: (state, getters, rootState, rootGetters) => {
		return Object.values(getters.getLessons)
			.filter(lesson => lesson.is_required && lesson.isAccessible);
	},
	userLessons: (state, getters) => {
		return Object.values(getters.getLessons)
			.filter(lesson => lesson.isAccessible);
	},
	getLesson: state => (lessonId) => _.get(state.structure[resource('lessons')], lessonId, {}),
	isLessonAvailable: (state) => (lessonId) => {
		return state.structure[resource('lessons')][lessonId].isAvailable;
	},
	isLessonAccessible: (state) => (lessonId) => {
		return state.structure[resource('lessons')][lessonId].isAccessible;
	},
	getScreen: state => (screenId) => state.structure[resource('screens')][screenId],
	getSection: state => (sectionId) => _.get(state.structure['sections'], sectionId, {}),
	getSections: state => (sections) => {
		return sections
			.map((sectionId) => _.get(state.structure, `sections.${sectionId}`, {}));
	},
	getSubsections: state => (subsections) => subsections.map((subsectionId) => _.get(state.structure, `subsections.${subsectionId}`, {})) || [],
	getScreenSectionsCheckpoints: (state, getters) => (screenId) => {
		const sectionsIds = getters.getScreen(screenId).sections;
		const sections = getters.getSections(sectionsIds);

		return sections.map((section) => section.slide);
	},
	getSectionSubsectionsCheckpoints: (state, getters) => (sectionId) => {
		const subsectionsIds = getters.getSection(sectionId).subsections;
		const subsections = getters.getSubsections(subsectionsIds);

		return subsections.map((subsections) => subsections.slide);
	},
	getScreens: state => (lessonId) => {
		if (!state.lessons[lessonId]) return [];
		return state.lessons[lessonId].screens
			.sort((screenA, screenB) => screenA.order_number - screenB.order_number);
	},
	getAdjacentScreenId: (state, getters) => (lessonId, currentScreenId, direction) => {
		let screens = getters.getScreens(lessonId);

		if (_.isEmpty(screens)) return undefined;

		let currentScreenIndex = _.findIndex(screens, {'id': parseInt(currentScreenId)}),
			adjScreenIndex;

		if (direction === 'next') {
			adjScreenIndex = currentScreenIndex + 1;
			if (currentScreenIndex >= 0 && adjScreenIndex < screens.length) {
				return screens[adjScreenIndex].id;
			}
		} else if (direction === 'previous') {
			adjScreenIndex = currentScreenIndex - 1;
			if (currentScreenIndex > 0) {
				return screens[adjScreenIndex].id;
			}
		}

		return undefined;
	},
	nextLesson: (state, getters, rootState, rootGetters) => {
		if (typeof getters.getLessons === 'undefined' || !rootGetters['progress/getCourse'](state.id)) {
			return {};
		}

		const inProgressId = rootGetters['progress/getFirstLessonIdInProgress'](state.id);

		if (inProgressId > 0) {
			const lesson = getters.getLesson(inProgressId);
			lesson.status = STATUS_IN_PROGRESS;

			return lesson;
		} else {
			const sortedLessonsIds = Object.keys(getters.getLessons).sort((keyA, keyB) => {
				const lessonA = getters.getLessons[keyA];
				const lessonB = getters.getLessons[keyB];

				const byOrderNumber = lessonA.order_number - lessonB.order_number;
				if (byOrderNumber === 0) {
					return lessonA.id - lessonB.id;
				}
				return byOrderNumber;
			}).map(Number);

			for (let i = 0; i < sortedLessonsIds.length; i++) {
				const lessonId = sortedLessonsIds[i];
				const isAvailable = getters.isLessonAvailable(lessonId);
				const isAccessible = getters.isLessonAccessible(lessonId);
				if (isAvailable &&
					!rootGetters['progress/wasLessonStarted'](state.id, lessonId)
				) {
					const lesson = getters.getLesson(lessonId);
					lesson.status = STATUS_AVAILABLE;
					return lesson;
				} else if (!isAvailable && isAccessible) {
					const lesson = getters.getLesson(lessonId);
					lesson.status = STATUS_NONE;
					return lesson;
				}
			}
		}

		return {
			status: STATUS_NONE
		};
	}
};

// Mutations
const mutations = {
	[types.COURSE_READY] (state) {
		set(state, 'ready', true);
	},
	[types.SET_NEW_STRUCTURE](state, payload) {
		set(state, 'newStructure', payload);
	},
	[types.SET_SCREENS](state, {screens, lessonId}) {
		set(state.lessons, lessonId, {
			screens
		});
	},
	[types.SET_STRUCTURE] (state, data) {
		set(state, 'id', data.id);
		set(state, 'name', data.name);
		set(state, resource('groups'), data[resource('groups')]);
		set(state, 'structure', data.included);
	},
	[types.COURSE_REMOVE_GROUP] (state, payload) {
		state.groups.splice(payload.index, 1);
		destroy(state.structure.groups, payload.id);
		payload.lessons.forEach((lesson) => {
			destroy(state.structure.lessons, lesson);
		});
	},
	[types.COURSE_SET_LESSON_AVAILABILITY] (state, payload) {
		set(state.structure.lessons[payload.lessonId], 'isAvailable', payload.status);
	},
	[types.COURSE_UPDATE_LESSON_START_DATE] (state, payload) {
		set(state.structure.lessons[payload.lessonId], 'startDate', payload.start_date);
	},
	[types.SET_SCREEN_CONTENT] (state, {data, screenId}) {
		set(state.structure.screens[screenId], 'content', data.content);
	},
};

// Actions
const actions = {
	setup({commit, dispatch, rootGetters}, courseId) {
		return new Promise((resolve, reject) => {
			Promise.all([
				dispatch('setStructure', courseId),
				dispatch('progress/setupCourse', courseId, {root: true}),
			])
				.then(() => {
					$wnl.logger.debug('Course ready, yay!');
					commit(types.COURSE_READY);
					return resolve();
				}, reason => {
					commit(types.COURSE_READY);
					$wnl.logger.error(reason);
					return reject(reason);
				});
		});
	},
	async setupLesson({commit}, lessonId) {
		const response = await axios.get(getApiUrl(`lessons/${lessonId}/screens`));
		commit(types.SET_SCREENS, {
			screens: response.data,
			lessonId
		});
	},
	async setStructureNew({commit}, courseId = 1) {
		const response = await _getCourseStructure(courseId);
		const {data: {included, ...structureObj}} = response;
		const structure = Object.values(structureObj);

		const withIncludes = structure.map(node => {
			const include = modelToResourceMap[node.structurable_type];
			const value = included[include][node.structurable_id];

			return {
				...node,
				model: value
			};
		});
		commit(types.SET_NEW_STRUCTURE, withIncludes);
	},
	setStructure({commit, rootGetters, dispatch}, courseId = 1) {
		dispatch('setStructureNew');
		return axios.get(getCourseApiUrl(courseId, rootGetters.currentUserId))
			.then(response => {
				commit(types.SET_STRUCTURE, response.data);
			});
	},
	setLessonAvailabilityStatus({commit}, payload) {
		commit(types.COURSE_SET_LESSON_AVAILABILITY, payload);
	},
	updateLessonStartDate({commit}, payload) {
		commit(types.COURSE_UPDATE_LESSON_START_DATE, payload);
	},
	fetchScreenContent({commit}, screenId) {
		return axios.get(getApiUrl(`screens/${screenId}`))
			.then(({data}) => {
				commit(types.SET_SCREEN_CONTENT, {data, screenId});
			});
	}
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
