<template>
	<div>
		<article class="message is-info">
			<div class="message-header">
				<p>Twój Plan Pracy</p>
			</div>
			<div class="message-body plan-details">
				<span>Twój obecny plan pracy zakłada naukę od <strong>{{planStartDate}}</strong> do <strong>{{planEndDate}}</strong>.</span>
				<span>Aby podejrzeć daty otwarcia poszczególnych lekcji wejdź w Twój Plan Pracy > Ustaw plan ręcznie.</span>
			</div>
		</article>
		<div class="wnl-screen-title">
			<div class="level-left">
				<div class="big strong">
					{{ $t('lessonsAvailability.viewsExplanation') }}
				</div>
			</div>
		</div>
		<div class="views-control">
			<a v-for="view in views"
				 class="panel-toggle view"
				 :class="{'is-active': view.isActive}"
				 :key="view.title"
				 @click="toggleView(view)"
			>{{ view.title }}
				<span class="icon is-small">
					<i class="fa"
						 :class="[view.isActive ? 'fa-check-circle' : 'fa-circle-o']"></i>
				</span>
			</a>
		</div>
		<component :is="activeViewComponent"/>
	</div>
</template>

<style lang="sass" scoped>
@import 'resources/assets/sass/variables'

.plan-details
	display: flex
	flex-direction: column

.views-control
	display: inline-flex
	justify-content: flex-start
	margin-bottom: $margin-base
	.panel-toggle:last-child
		margin-right: $margin-small

</style>

<script>
	import OpenAllPlan from './OpenAllPlan'
	import AutomaticPlan from './AutomaticPlan'
	import DefaultPlan from './DefaultPlan'
	import ManualPlan from './ManualPlan'
	import { first,last } from 'lodash'
	import { mapGetters } from 'vuex'
	import moment from 'moment'

	export default {
		name: 'LessonsAvailabilities',
		data() {
			return {
				views: [
					{
						title: 'Ustaw plan ręcznie',
						component: ManualPlan,
						isActive: true
					},
					{
						title: 'Przywróć domyślny plan',
						component: DefaultPlan,
					},
					{
						title: 'Wygeneruj plan automatycznie',
						component: AutomaticPlan,
					},
					{
						title: 'Otwórz wszystkie lekcje',
						component: OpenAllPlan,
					}
				],
			}
		},
		computed: {
			...mapGetters('course', ['userLessons', 'getRequiredLessons']),
			sortedRequiredUserLessons() {
				return this.requiredLessons.sort((lessonA, lessonB) => {
					return lessonA.startDate - lessonB.startDate
				})
			},
			requiredLessons() {
				return Object.values(this.getRequiredLessons).filter(requiredLesson => {
					if (requiredLesson.is_required && requiredLesson.isAccessible) {
						return requiredLesson
					}
				})
			},
			planStartDate() {
				if (!first(this.sortedRequiredUserLessons)) return

				return moment(first(this.sortedRequiredUserLessons).startDate * 1000).format('LL')
			},
			planEndDate() {
				if (!last(this.sortedRequiredUserLessons)) return

				return moment(last(this.sortedRequiredUserLessons).startDate * 1000).format('LL')
			},
			activeView() {
				return this.views.find(view => view.isActive) || {}
			},
			activeViewComponent() {
				return this.activeView.component
			},
		},
		methods: {
			toggleView(selectedView) {
				this.views = this.views.map(view => {
					if (selectedView.title === view.title) {
						return {
							...view,
							isActive: true
						}
					}
					return {
						...view,
						isActive: false
					}
				})
			},
		}
	}
</script>