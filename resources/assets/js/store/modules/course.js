import _ from 'lodash';
import {set} from 'vue';
import {getApiUrl} from 'js/utils/env';
import {resources} from 'js/utils/constants';
import * as types from 'js/store/mutations-types';
import { modelToResourceMap, getModelByResource } from 'js/utils/config';

function _getCourseStructure(courseId = 1) {
	return axios.get(getApiUrl(`course_structure_nodes/${courseId}`), {
		params: {
			include: 'groups,lessons,course,structurable'
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
	sections: {},
	subsections: {},
	screens: {},
	structure: []
};

// Getters
const getters = {
	ready: state => state.ready,
	courseId: state => state.id,
	name: state => state.name,
	getNode: state => nodeId => state.structure.find(node => node.id === nodeId),
	getChildrenNodes: state => parentId => state.structure.filter(node => node.parent_id === parentId),
	getAncestorNodesById: (state, getters) => nodeId => {
		const ancestors = [];

		let currentNode = getters.getNode(nodeId);

		while (currentNode.parent_id) {
			currentNode = getters.getNode(currentNode.parent_id);
			ancestors.unshift(currentNode);
		}

		return ancestors;
	},
	groups: state => {
		return state.structure.filter(node => node.structurable_type === getModelByResource(resources.groups))
			.map(node => node.model);
	},
	getGroup: (state, getters) => groupId => {
		const castedGroupId = groupId.toString();
		return getters.groups.find(group => group.id.toString() === castedGroupId) || {};
	},
	getGroupsByLessonId: (state, getters) => lessonId => {
		return getters.getAncestorNodesById(getters.getNodeByLessonId(lessonId).id).map(node => node.model);
	},
	getNodeByLessonId: (state) => lessonId => {
		return state.structure.find(node => node.model.id === lessonId && node.structurable_type === getModelByResource(resources.lessons));
	},
	getLessons: state => {
		return state.structure.filter(node => node.structurable_type === getModelByResource(resources.lessons))
			.map(node => node.model);
	},
	getRequiredLessons: (state, getters) => {
		return getters.getLessons.filter(lesson => lesson.is_required && lesson.isAccessible);
	},
	userLessons: (state, getters) => {
		return getters.getLessons.filter(lesson => lesson.isAccessible);
	},
	getLesson: (state, getters) => lessonId => {
		return getters.getLessons.find(lesson => lesson.id.toString() === lessonId.toString()) || {};
	},
	isLessonAvailable: (state, getters) => lessonId => {
		const lesson = getters.getLesson(lessonId);
		return lesson && lesson.isAvailable;
	},
	getScreen: state => screenId => state.screens[screenId] || {},
	getScreensForLesson: state => lessonId => {
		const castedLessonId = lessonId.toString();
		return Object.values(state.screens)
			.filter(screen => {
				return screen.lessons && screen.lessons.toString() === castedLessonId;
			})
			.sort((screenA, screenB) => screenA.order_number - screenB.order_number);
	},
	getSection: state => sectionId => state.section[sectionId] || {},
	getSectionsForScreen: state => screenId => {
		const castedScreenId = screenId.toString();

		return Object.values(state.sections)
			.filter(section => {
				return section.screens.toString() === castedScreenId;
			});
	},
	getScreenSectionsCheckpoints: (state, getters) => screenId => {
		const sections = getters.getSectionsForScreen(screenId);

		return sections.map((section) => section.slide);
	},
	getSubsectionsForSection: state => sectionId => {
		const castedSectionId = sectionId.toString();

		return Object.values(state.subsections)
			.filter(subsection => {
				return subsection.sections.toString() === castedSectionId;
			});
	},
	getSectionSubsectionsCheckpoints: (state, getters) => sectionId => {
		const subsections = getters.getSubsectionsForSection(sectionId);
		return subsections.map((subsections) => subsections.slide);
	},
	getAdjacentScreenId: (state, getters) => (lessonId, currentScreenId, direction) => {
		let screens = getters.getScreensForLesson(lessonId);

		if (!screens.length) return;

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

		const inProgressLesson = rootGetters['progress/getFirstLessonInProgress'](state.id);

		if (inProgressLesson) {
			inProgressLesson.status = STATUS_IN_PROGRESS;

			return inProgressLesson;
		} else {
			const sortedLessons = getters.getLessons;

			for (let i = 0; i < sortedLessons.length; i++) {
				const lesson = sortedLessons[i];
				const isAvailable = lesson.isAvailable;
				const isAccessible = lesson.isAccessible;
				if (isAvailable &&
					!rootGetters['progress/wasLessonStarted'](state.id, lesson.id)
				) {
					lesson.status = STATUS_AVAILABLE;
					return lesson;
				} else if (!isAvailable && isAccessible) {
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
	[types.SET_STRUCTURE](state, payload) {
		set(state, 'structure', payload);
	},
	[types.SET_SCREENS](state, screens) {
		set(state, 'screens', {
			...state.screens,
			...screens
		});
	},
	[types.SET_SECTIONS](state, sections) {
		set(state, 'sections', {
			...state.sections,
			...sections
		});
	},
	[types.SET_SUBSECTIONS](state, subsections) {
		set(state, 'subsections', {
			...state.subsections,
			...subsections
		});
	},
	[types.SET_COURSE](state, {name, id}) {
		set(state, 'name', name);
		set(state, 'id', id);
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
		const { data } = await axios.get(getApiUrl(`lessons/${lessonId}/screens`), {
			params: {
				include: 'sections.subsections'
			}
		});
		const {included, ...screensUnordered} = data;
		const screens = Object.values(screensUnordered).reduce((screens, screen) => {
			screens[screen.id] = screen;
			return screens;
		}, {});
		const sections = included ? included.sections : {};
		const subsections = included ? included.subsections : {};

		commit(types.SET_SCREENS, screens);
		commit(types.SET_SECTIONS, sections);
		commit(types.SET_SUBSECTIONS, subsections);
	},
	async setStructure({commit}, courseId = 1) {
		const response = await _getCourseStructure(courseId);
		const {data: {included, ...structureObj}} = response;
		const structure = Object.values(structureObj);
		const {courses} = included;

		const withIncludes = structure.map(node => {
			const include = modelToResourceMap[node.structurable_type];
			const value = included[include][node.structurable_id];

			return {
				...node,
				model: value
			};
		});

		commit(types.SET_STRUCTURE, withIncludes);
		commit(types.SET_COURSE, courses[courseId]);
	},
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
