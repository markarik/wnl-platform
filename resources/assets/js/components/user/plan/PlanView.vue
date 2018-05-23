<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('lessonsAvailability.header') }}
				</div>
			</div>
		</div>
		<article class="message is-info">
			<div class="message-header">
				<p>Twój Plan Pracy</p>
			</div>
			<div class="message-body">
				<span>Twój obecny plan pracy zakłada naukę od <strong>{{planStartDate}}</strong> do <strong>{{planEndDate}}</strong>.</span>
				<span>Aby podejrzeć daty otwarcia poszczególnych lekcji wejdź w Ustaw plan ręcznie.</span>
			</div>
		</article>
		<wnl-lessons-planner/>
	</div>
</template>

<script>
	import moment from 'moment';
	import {mapGetters} from 'vuex'
	import {first, last} from 'lodash';
	import LessonsPlanner from './LessonsPlanner';

	export default {
		components: {
			'wnl-lessons-planner': LessonsPlanner
		},
		computed: {
			...mapGetters('course', ['userLessons']),
			sortedUserLessons() {
				return this.userLessons.sort((lessonA, lessonB) => {
					return lessonA.startDate - lessonB.startDate
				})
			},
			planStartDate() {
				if (!first(this.sortedUserLessons)) return

				return moment(first(this.sortedUserLessons).startDate * 1000).format('LL')
			},
			planEndDate() {
				if (!last(this.sortedUserLessons)) return

				return moment(last(this.sortedUserLessons).startDate * 1000).format('LL')
			}
		}
	}
</script>

