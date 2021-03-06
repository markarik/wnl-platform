<template>
	<div v-if="nextLessonAvailable || nextLessonDate" class="next-lesson">
		<div class="next">{{next}}</div>
		<div>
			<span
				v-for="(groupName, index) in groupNames"
				:key="index"
				class="group"
			>{{groupName}} <span class="icon is-small"><i class="fa fa-angle-right" /></span> </span>
			<span class="lesson">{{lessonName}}</span>
		</div>
		<div class="cta">
			<router-link
				v-if="nextLessonAvailable"
				class="button is-primary"
				:class="{'is-outlined': status === 'in-progress'}"
				:to="to"
				@click.native="trackNextLessonClick"
			>
				{{callToAction}}
			</router-link>
			<span v-else class="text">{{$t('dashboard.progress.none-CTA', {date: nextLessonDate})}}</span>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.next-lesson
		margin-bottom: $margin-big

		div
			text-align: center

	.next
		font-weight: $font-weight-bold
		margin-bottom: $margin-medium
		text-transform: uppercase

	.group
		font-size: $font-size-minus-1
		letter-spacing: 1px
		text-transform: uppercase

	.lesson
		font-size: $font-size-plus-1


	.cta
		font-size: $font-size-minus-1

		.button
			margin-top: $margin-medium
			font-size: $font-size-minus-2

		.text
			color: $color-gray

</style>

<script>
import { truncate } from 'lodash';
import { mapGetters } from 'vuex';
import { resource } from 'js/utils/config';
import { timeFromDate } from 'js/utils/time';
import context from 'js/consts/events_map/context.json';
import emits_events from 'js/mixins/emits-events';

const STATUS_NONE = 'none';

export default {
	name: 'NextLesson',
	mixins: [emits_events],
	computed: {
		...mapGetters('course', [
			'getGroupsByLessonId',
			'getLessons',
			'getLesson',
			'isLessonAvailable',
			'nextLesson'
		]),
		...mapGetters('progress', [
			'wasLessonStarted',
			'getFirstLessonIdInProgress',
			'isLessonComplete',
		]),
		callToAction() {
			return this.$t(`dashboard.progress.${this.status}-CTA`);
		},
		courseId() {
			return this.$route.params.courseId;
		},
		groupNames() {
			if (!this.nextLesson.id) {
				return [];
			}

			return this.getGroupsByLessonId(this.nextLesson.id).map(group => group.name);
		},
		nextLessonAvailable() {
			return this.status !== STATUS_NONE;
		},
		lessonName() {
			return truncate(this.nextLesson.name, { length: 30 });
		},
		next() {
			return this.$t(`dashboard.progress.${this.status}`);
		},
		nextLessonDate() {
			if (this.nextLesson.startDate) {
				return timeFromDate(new Date(this.nextLesson.startDate * 1000));
			}
			return false;
		},
		status() {
			return this.nextLesson.status;
		},
		to() {
			return {
				name: resource('lessons'),
				params: {
					courseId: this.courseId,
					lessonId: this.nextLesson.id,
				}
			};
		},
	},
	methods: {
		trackNextLessonClick() {
			this.$trackUserEvent({
				feature: context.dashboard.features.next_lesson.value,
				action: context.dashboard.features.next_lesson.actions.click_link.value,
				target: this.nextLesson.id,
				context: context.dashboard.value,
			});
		}
	},
};
</script>
