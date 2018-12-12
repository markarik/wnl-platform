<template>
	<aside class="sidenav-aside course-sidenav">
		<wnl-sidenav :breadcrumbs="breadcrumbs" :items="items" :itemsHeading="itemsHeading" :options="sidenavOptions"></wnl-sidenav>
	</aside>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.course-sidenav
		flex: 1
		min-width: $sidenav-min-width
		overflow: auto
		padding: 7px 0
		width: $sidenav-width
</style>

<script>
import _ from 'lodash';
import { mapGetters } from 'vuex';

import Sidenav from 'js/components/global/Sidenav.vue';

import * as mutations from 'js/store/mutations-types';
import { resource } from 'js/utils/config';
import {STATUS_COMPLETE} from '../../services/progressStore';
import navigation from 'js/services/navigation';

export default {
	name: 'Navigation',
	components: {
		'wnl-sidenav': Sidenav
	},
	props: ['context', 'isLesson'],
	computed: {
		...mapGetters(['currentUserRoles', 'lessonState']),
		...mapGetters('course', [
			'name',
			'groups',
			'structure',
			'getLesson',
			'getScreens',
			'isLessonAvailable',
		]),
		...mapGetters('progress', {
			getCourseProgress: 'getCourse',
			getScreenProgress: 'getScreen',
			getLessonProgress: 'getLesson',
			getSectionProgress: 'getSection'
		}),
		sidenavOptions() {
			return {
				hasGroups: !this.isLesson,
				showSubitemsCount: true,
			};
		},
		isStructureEmpty() {
			return typeof this.structure !== 'object' || this.structure.length === 0;
		},
		courseProgress() {
			return this.getCourseProgress(this.context.courseId);
		},
		breadcrumbs() {
			let breadcrumbs = [], courseItem;

			breadcrumbs.push(this.getCourseItem());

			return breadcrumbs;
		},
		itemsHeading() {
			if (this.isLesson) {
				return this.getLesson(this.context.lessonId).name;
			}
		},
		items() {
			if (this.isLesson) {
				return this.getLessonNavigation();
			} else {
				return this.getCourseNavigation();
			}
		},
	},
	methods: {
		getCourseNavigation() {
			if (this.isStructureEmpty) {
				$wnl.logger.debug('Empty structure, WTF?');
				$wnl.logger.debug(this.structure);
				return;
			}

			let navigation = [];

			if (this.groups.length === 0) {
				return navigation;
			}
			for (let i = 0, groupsLen = this.groups.length; i < groupsLen; i++) {
				let groupId = this.groups[i],
					group = this.structure[resource('groups')][groupId];

				const groupItem = this.getGroupItem(group);
				navigation.push(groupItem);

				if (!group.hasOwnProperty(resource('lessons'))) {
					continue;
				}

				groupItem.subitems = [];

				for (let j = 0, lessonsLen = group[resource('lessons')].length; j < lessonsLen; j++) {
					let lessonId = group[resource('lessons')][j],
						lesson = this.structure[resource('lessons')][lessonId];

					groupItem.subitems.push(this.getLessonItem(lesson));
				}
			}

			return navigation;
		},
		getLessonNavigation() {
			if (this.isStructureEmpty) {
				$wnl.logger.debug('Empty structure, WTF?');
				$wnl.logger.debug(this.structure);
				return;
			}

			let navigation = [],
				lesson = this.structure[resource('lessons')][this.context.lessonId],
				screens = this.getScreens(lesson.id);

			if (!lesson.hasOwnProperty(resource('screens'))) {
				return navigation;
			}

			screens.forEach((screen) => {
				navigation.push(this.getScreenItem(screen));

				if (screen.hasOwnProperty(resource('sections'))) {
					let sectionsIds = screen.sections;

					sectionsIds.forEach((sectionId, index) => {
						let section = this.structure[resource('sections')][sectionId];
						navigation.push(this.getSectionItem(section));

						if (section.hasOwnProperty('subsections')) {
							let subsectionsIds = section.subsections;

							subsectionsIds.forEach((subsectionId, index) => {
								let subsection = this.structure['subsections'][subsectionId];
								navigation.push(this.getSubsectionItem(subsection, section));
							});
						}
					});
				}
			});

			return navigation;
		},
		getCourseItem() {
			return navigation.composeItem({
				text: this.name,
				itemClass: 'has-icon',
				routeName: resource('courses'),
				routeParms: {
					courseId: this.context.courseId,
				},
				iconClass: 'fa-home',
				iconTitle: 'Strona główna kursu'
			});
		},
		getGroupItem(group) {
			return navigation.composeItem({text: group.name, itemClass: 'heading small'});
		},
		getLessonItem(lesson, withProgress = true) {
			let cssClass = 'is-grouped ', iconClass = '', iconTitle = '';

			if (withProgress) {
				cssClass += 'with-progress';

				if (this.courseProgress.lessons && this.courseProgress.lessons.hasOwnProperty(lesson.id)) {
					cssClass = `${cssClass} ${this.courseProgress.lessons[lesson.id].status}`;
				}
			} else {
				cssClass += 'has-icon';
				iconClass = 'fa-graduation-cap';
				iconTitle = 'Obecna lekcja';
			}

			return navigation.composeItem({
				text: lesson.name,
				itemClass: cssClass,
				routeName: resource('lessons'),
				routeParams: {
					courseId: lesson[resource('editions')],
					lessonId: lesson.id,
				},
				isDisabled: !this.isLessonAvailable(lesson.id),
				iconClass,
				iconTitle
			});

		},
		getScreenItem(screen) {
			const params = {
				courseId: screen[resource('editions')],
				lessonId: screen[resource('lessons')],
				screenId: screen.id,
			};

			const lesson = this.getLessonProgress(params.courseId, params.lessonId);
			const screens = lesson && lesson.screens || [];
			const completed = screens[screen.id] && screens[screen.id].status === STATUS_COMPLETE;
			const itemProps = {
				text: screen.name,
				itemClass: 'todo',
				routeName: resource('screens'),
				routeParams: params,
				completed
			};

			if (screen.slides_count) {
				return navigation.composeItem({...itemProps, meta: `(${screen.slides_count})`});
			}

			return navigation.composeItem(itemProps);
		},
		getSectionItem(section) {
			const params = {
				courseId: section[resource('editions')],
				lessonId: section[resource('lessons')],
				screenId: section[resource('screens')],
				slide: section.slide,
			};

			const screen = this.getScreenProgress(params.courseId, params.lessonId, params.screenId);
			const sections = screen && screen.sections || {};
			const isSectionActive = this.lessonState.activeSection === section.id;

			return navigation.composeItem({
				text: section.name,
				itemClass: 'small subitem todo',
				routeName: resource('screens'),
				routeParams: params,
				method: 'replace',
				iconClass: 'fa-angle-right',
				iconTitle: section.name,
				completed: !!sections[section.id],
				active: isSectionActive,
				meta: `(${section.slidesCount})`
			});
		},
		getSubsectionItem(subsection, section) {
			const params = {
				courseId: subsection[resource('editions')],
				lessonId: subsection[resource('lessons')],
				screenId: subsection[resource('screens')],
				slide: subsection.slide,
			};

			const sectionProgress = this.getSectionProgress(params.courseId, params.lessonId, params.screenId, section.id) || {};
			const completedSubsections = sectionProgress.subsections || {};
			const isSubsectionActive = this.lessonState.activeSubsection === subsection.id;

			return navigation.composeItem({
				text: subsection.name,
				itemClass: 'small subitem--second todo',
				routeName: resource('screens'),
				routeParams: params,
				method: 'replace',
				iconClass: 'fa-angle-right',
				iconTitle: subsection.name,
				completed: !!completedSubsections[subsection.id],
				active: isSubsectionActive,
				meta: `(${subsection.slidesCount})`
			});
		}
	}
};
</script>
