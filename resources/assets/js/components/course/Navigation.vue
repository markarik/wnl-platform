<template>
	<aside class="wnl-sidenav wnl-left-content">
		<wnl-sidenav :breadcrumbs="breadcrumbs" :items="items"></wnl-sidenav>
	</aside>
</template>

<script>
	import Sidenav from 'js/components/global/Sidenav.vue'
	import * as mutations from 'js/store/mutations-types'
	import { mapGetters, mapMutations } from 'vuex'
	import { routes } from 'js/utils/constants'

	export default {
		name: 'Navigation',
		props: ['context', 'isLesson'],
		computed: {
			...mapGetters(['courseName','courseStructure']),
			breadcrumbs() {
				let breadcrumbs = []

				breadcrumbs.push(this.getCourseItem())

				if (this.isLesson) {
					let lesson = this.courseStructure.lessons[this.context.lessonId]

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

				for (let i = 0, groupsLen = this.courseStructure.groups.length; i < groupsLen; i++) {
					let group = this.courseStructure.groups[i]

					navigation.push(this.getGroupItem(group))

					for (let j = 0, lessonsLen = group.lessons.length; j < lessonsLen; j++) {
						let lessonId = group.lessons[j],
							lesson = this.courseStructure.lessons[lessonId]

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
					lesson = this.courseStructure.lessons[this.context.lessonId]

				for (let i = 0, screensLen = lesson.screens.length; i < screensLen; i++) {
					let screen = lesson.screens[i]
					navigation.push(this.getScreenItem(screen))

					for (let j = 0, sectionsLen = screen.sections.length; j < sectionsLen; j++) {
						navigation.push(this.getSectionItem(screen.sections[j]))
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
					routes.courses,
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
				return this.composeItem(
					lesson.name,
					'todo',
					routes.lessons,
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
					routes.screens,
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
					routes.screens,
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
