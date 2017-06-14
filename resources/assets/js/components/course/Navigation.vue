<template>
	<aside class="course-sidenav">
		<wnl-sidenav :breadcrumbs="breadcrumbs" :items="items" :itemsHeading="itemsHeading"></wnl-sidenav>
	</aside>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.course-sidenav
		// border-right: $border-light-gray;
		max-width: $sidenav-max-width
		min-width: $sidenav-min-width
		overflow: auto
		padding: $column-padding
		width: $sidenav-width
</style>

<script>
	import _ from 'lodash'
	import { mapGetters } from 'vuex'

	import Sidenav from 'js/components/global/Sidenav.vue'

	import * as mutations from 'js/store/mutations-types'
	import { resource } from 'js/utils/config'

	export default {
		name: 'Navigation',
		props: ['context', 'isLesson'],
		computed: {
			...mapGetters('course', [
				'name',
				'groups',
				'structure',
				'getLesson',
				'getScreens'
			]),
			...mapGetters('progress', {
				getCourseProgress: 'getCourse',
				getScreenProgress: 'getScreen',
			}),
			isStructureEmpty() {
				return typeof this.structure !== 'object' || this.structure.length === 0
			},
			courseProgress() {
				return this.getCourseProgress(this.context.courseId)
			},
			breadcrumbs() {
				let breadcrumbs = [], courseItem

				breadcrumbs.push(this.getCourseItem())

				return breadcrumbs
			},
			itemsHeading() {
				if (this.isLesson) {
					return this.getLesson(this.context.lessonId).name
				}
			},
			items() {
				if (this.isLesson) {
					return this.getLessonNavigation()
				} else {
					return this.getCourseNavigation()
				}
			},
		},
		methods: {
			getCourseNavigation() {
				if (this.isStructureEmpty) {
					$wnl.logger.debug('Empty structure, WTF?')
					$wnl.logger.debug(this.structure)
					return
				}

				let navigation = []

				if (this.groups.length === 0) {
					return navigation
				}
				for (let i = 0, groupsLen = this.groups.length; i < groupsLen; i++) {
					let groupId = this.groups[i],
						group = this.structure[resource('groups')][groupId]

					navigation.push(this.getGroupItem(group))

					if (!group.hasOwnProperty(resource('lessons'))) {
						continue
					}
					for (let j = 0, lessonsLen = group[resource('lessons')].length; j < lessonsLen; j++) {
						let lessonId = group[resource('lessons')][j],
							lesson = this.structure[resource('lessons')][lessonId]

						navigation.push(this.getLessonItem(lesson))
					}
				}

				return navigation
			},
			getLessonNavigation() {
				if (this.isStructureEmpty) {
					$wnl.logger.debug('Empty structure, WTF?')
					$wnl.logger.debug(this.structure)
					return
				}

				let navigation = [],
					lesson = this.structure[resource('lessons')][this.context.lessonId],
					screens = this.getScreens(lesson.id)

				if (!lesson.hasOwnProperty(resource('screens'))) {
					return navigation
				}

				screens.forEach((screen) => {
					navigation.push(this.getScreenItem(screen))

					if (screen.hasOwnProperty(resource('sections'))) {
						screen.sections.forEach((sectionId) => {
							let section = this.structure[resource('sections')][sectionId]
							navigation.push(this.getSectionItem(section))
						});
					}
				});

				return navigation
			},
			composeItem(
				text,
				itemClass,
				routeName = '',
				routeParams = {},
				isDisabled = false,
				method = 'push',
				iconClass = '',
				iconTitle = '',
			    completed = false
			) {
				let to = {}
				if (!isDisabled && routeName.length > 0) {
					to = {
						name: routeName,
						params: routeParams
					}
				}

				return { text, itemClass, to, isDisabled, method, iconClass, iconTitle, completed }
			},
			getCourseItem() {
				return this.composeItem(
					this.name,
					'has-icon',
					resource('courses'),
					{
						courseId: this.context.courseId,
					},
					false,
					'push',
					'fa-home',
					'Strona główna kursu'
				)
			},
			getGroupItem(group) {
				return this.composeItem(
					group.name,
					'heading small'
				)
			},
			getLessonItem(lesson, asTodo = true) {
				let cssClass = '', iconClass = '', iconTitle = ''

				if (asTodo) {
					cssClass += 'todo'

					if (this.courseProgress.lessons.hasOwnProperty(lesson.id)) {
						cssClass = `${cssClass} ${this.courseProgress.lessons[lesson.id].status}`
					}
				} else {
					cssClass += 'has-icon'
					iconClass = 'fa-graduation-cap'
					iconTitle = 'Obecna lekcja'
				}

				return this.composeItem(
					lesson.name,
					cssClass,
					resource('lessons'),
					{
						courseId: lesson[resource('editions')],
						lessonId: lesson.id,
					},
					!lesson.isAvailable,
					'push',
					iconClass,
					iconTitle
				)

			},
			getScreenItem(screen) {
				let itemClass = '', iconClass = '', iconTitle = ''

				const icons = {
					'end': 'fa-star',
					'quiz': 'fa-check-square-o',
					'html': 'fa-file-text-o',
					'slideshow': 'fa-television',
					'widget': 'fa-question',
				}

				if (icons.hasOwnProperty(screen.type)) {
					itemClass = 'has-icon with-border'
					iconClass = icons[screen.type]
					iconTitle = screen.name
				}

				return this.composeItem(
					screen.name,
					itemClass,
					resource('screens'),
					{
						courseId: screen[resource('editions')],
						lessonId: screen[resource('lessons')],
						screenId: screen.id,
					},
					false,
					'push',
					iconClass,
					iconTitle,
				)
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

				return this.composeItem(
					section.name,
					'small subitem has-icon',
					resource('screens'),
					params,
					false,
					'replace',
					'fa-angle-right',
					section.name,
					!!sections[section.id]
				)
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
		}
	}
</script>
