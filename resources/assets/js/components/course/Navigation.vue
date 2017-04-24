<template>
	<aside class="wnl-sidenav wnl-left-content">
		<wnl-sidenav :breadcrumbs="breadcrumbs" :items="items" :itemsHeading="itemsHeading"></wnl-sidenav>
	</aside>
</template>

<script>
	import Sidenav from 'js/components/global/Sidenav.vue'
	import * as mutations from 'js/store/mutations-types'
	import { mapGetters } from 'vuex'
	import { resource } from 'js/utils/config'

	export default {
		name: 'Navigation',
		props: ['context', 'isLesson'],
		computed: {
			...mapGetters('course', [
				'name',
				'groups',
				'structure',
				'getLesson'
			]),
			...mapGetters('progress', {
				getCourseProgress: 'getCourse'
			}),
			isStructureEmpty() {
				return typeof this.structure !== 'object' || this.structure.length === 0
			},
			courseProgress() {
				return this.getCourseProgress(this.context.courseId)
			},
			breadcrumbs() {
				let breadcrumbs = []

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
					$wnl.debug('Empty structure, WTF?')
					$wnl.debug(this.structure)
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
					$wnl.debug('Empty structure, WTF?')
					$wnl.debug(this.structure)
					return
				}

				let navigation = [],
					lesson = this.structure[resource('lessons')][this.context.lessonId]

				if (!lesson.hasOwnProperty(resource('screens'))) {
					return navigation
				}
				for (let i = 0, screensLen = lesson[resource('screens')].length; i < screensLen; i++) {
					let screenId = lesson[resource('screens')][i]
						screen = this.structure[resource('screens')][screenId]

					navigation.push(this.getScreenItem(screen))

					if (!screen.hasOwnProperty(resource('sections'))) {
						continue
					}
					for (let j = 0, sectionsLen = screen[resource('sections')].length; j < sectionsLen; j++) {
						let sectionId = screen[resource('sections')][j],
							section = this.structure[resource('sections')][sectionId]

						navigation.push(this.getSectionItem(section))
					}
				}

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
				iconTitle = ''
			) {
				let to = {}
				if (!isDisabled && routeName.length > 0) {
					to = {
						name: routeName,
						params: routeParams
					}
				}

				return { text, itemClass, to, isDisabled, method, iconClass, iconTitle }
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
				return this.composeItem(
					section.name,
					'small subitem has-icon',
					resource('screens'),
					{
						courseId: section[resource('editions')],
						lessonId: section[resource('lessons')],
						screenId: section[resource('screens')],
						slide: section.slide,
					},
					false,
					'replace',
					'fa-angle-right',
					section.name
				)
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
		}
	}
</script>
