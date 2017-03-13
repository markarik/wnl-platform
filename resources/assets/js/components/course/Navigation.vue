<template>
	<aside class="wnl-sidenav wnl-left-content">
		<wnl-sidenav :breadcrumbs="breadcrumbs" :items="items"></wnl-sidenav>
	</aside>
</template>

<script>
	import Sidenav from 'js/components/global/Sidenav.vue'
	import * as mutations from 'js/store/mutations-types'
	import { mapGetters, mapMutations } from 'vuex'
	import { resource } from 'js/utils/config'

	export default {
		name: 'Navigation',
		props: ['context', 'isLesson'],
		computed: {
			...mapGetters(['courseName', 'courseGroups', 'courseStructure', 'progressCourse']),
			courseProgress() {
				return this.progressCourse(this.context.courseId)
			},
			breadcrumbs() {
				let breadcrumbs = []

				breadcrumbs.push(this.getCourseItem())

				if (this.isLesson) {
					let lesson = this.courseStructure[resource('lessons')][this.context.lessonId]

					breadcrumbs.push(this.getLessonItem(lesson))
				}

				return breadcrumbs
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
				if (typeof this.courseStructure !== 'object' || this.courseStructure.length === 0) {
					$wnl.debug('Empty structure, WTF?')
					$wnl.debug(this.courseStructure)
					return
				}

				let navigation = []

				for (let i = 0, groupsLen = this.courseGroups.length; i < groupsLen; i++) {
					let groupId = this.courseGroups[i],
						group = this.courseStructure[resource('groups')][groupId]

					navigation.push(this.getGroupItem(group))

					for (let j = 0, lessonsLen = group[resource('lessons')].length; j < lessonsLen; j++) {
						let lessonId = group[resource('lessons')][j],
							lesson = this.courseStructure[resource('lessons')][lessonId]

						navigation.push(this.getLessonItem(lesson))
					}
				}

				return navigation
			},
			getLessonNavigation() {
				if (typeof this.courseStructure !== 'object' || this.courseStructure.length === 0) {
					$wnl.debug('Empty structure, WTF?')
					$wnl.debug(this.courseStructure)
					return
				}

				let navigation = [],
					lesson = this.courseStructure[resource('lessons')][this.context.lessonId]

				for (let i = 0, screensLen = lesson[resource('screens')].length; i < screensLen; i++) {
					let screenId = lesson[resource('screens')][i]
						screen = this.courseStructure[resource('screens')][screenId]

					navigation.push(this.getScreenItem(screen))

					for (let j = 0, sectionsLen = screen[resource('sections')].length; j < sectionsLen; j++) {
						let sectionId = screen[resource('sections')][j],
							section = this.courseStructure[resource('sections')][sectionId]

						navigation.push(this.getSectionItem(section))
					}
				}

				return navigation
			},
			composeItem(text, itemClass, routeName = '', routeParams = {}, isDisabled = false, method = 'push') {
				let to = {}
				if (!isDisabled && routeName.length > 0) {
					to = {
						name: routeName,
						params: routeParams
					}
				}

				return { text, itemClass, to, isDisabled, method }
			},
			getCourseItem() {
				return this.composeItem(
					this.courseName,
					'',
					resource('courses'),
					{
						courseId: this.context.courseId,
					}
				)
			},
			getGroupItem(group) {
				return this.composeItem(
					group.name,
					'small'
				)
			},
			getLessonItem(lesson) {
				let statusClass = ''

				if (this.courseProgress.lessons.hasOwnProperty(lesson.id)) {
					statusClass = ` lesson-${this.courseProgress.lessons[lesson.id].status}`
				}

				return this.composeItem(
					lesson.name,
					`todo${statusClass}`,
					resource('lessons'),
					{
						courseId: lesson.course,
						lessonId: lesson.id,
					},
					!lesson.isAvailable
				)

			},
			getScreenItem(screen) {
				return this.composeItem(
					screen.name,
					'',
					resource('screens'),
					{
						courseId: screen.course,
						lessonId: screen.lesson,
						screenId: screen.id,
					}
				)
			},
			getSectionItem(section) {
				return this.composeItem(
					section.name,
					'small subitem',
					resource('screens'),
					{
						courseId: section.course,
						lessonId: section.lesson,
						screenId: section.screen,
						slide: section.slide,
					},
					false,
					'replace'
				)
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
		}
	}
</script>
