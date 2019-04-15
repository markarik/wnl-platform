<template>
	<div class="item" :class="[itemClass, { disabled: lessonItem.isDisabled }]">
		<router-link
			class="item-wrapper"
			:class="{'router-link-exact-active': lessonItem.active, 'is-disabled': lessonItem.isDisabled, 'is-completed': lessonItem.completed}"
			:to="to"
		>
			<div class="sidenav-icon-wrapper">
				<span class="icon is-small">
					<i
						title="W trakcie..."
						class="fa fa-ellipsis-h"
						v-if="isInProgress"
					></i>
					<i
						title="Zrobione!"
						class="fa fa-check-square-o"
						v-else-if="isComplete"
					></i>
					<i
						title="Jeszcze przed Tobą"
						class="fa fa-square-o"
						v-else
					></i>
				</span>
			</div>
			<span class="sidenav-item-content">
				{{lessonItem.text}}
			</span>
		</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.item.disabled
		color: $color-lighter-gray

		&:hover:not(.disabled)
			color: $color-ocean-blue

	.item-wrapper
		height: 100%
		width: 100%
		user-select: none

	.is-grouped
		padding-left: $margin-base

	.icon.is-small
		font-size: $font-size-minus-1
		margin-top: -1px
		margin-right: $margin-tiny

	.sidenav-icon-wrapper
		margin-right: 5px

		.icon
			margin-top: 0

	.sidenav-item-meta
		color: $color-background-gray
		font-size: $font-size-minus-3
		line-height: $line-height-plus

	a
		transition: background-color $transition-length-base

		&.is-disabled
			color: $color-lighter-gray

		&:hover:not(.is-disabled)
			color: $color-ocean-blue

		&.router-link-exact-active
			font-weight: $font-weight-regular
			transition: background-color $transition-length-base

		&.is-active
			font-weight: $font-weight-regular

</style>

<script>
import { mapGetters } from 'vuex';

import navigation from 'js/services/navigation';
import { STATUS_COMPLETE, STATUS_IN_PROGRESS } from 'js/services/progressStore';

export default {
	name: 'LessonItem',
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	computed: {
		...mapGetters('progress', {
			getCourseProgress: 'getCourse',
		}),
		...mapGetters('course', [
			'isLessonAvailable',
		]),
		courseId() {
			return this.$route.params.courseId;
		},
		courseProgress() {
			return this.getCourseProgress(this.courseId);
		},
		lessonProgress() {
			return this.courseProgress.lessons
				&& this.courseProgress.lessons[this.item.model.id]
				&& this.courseProgress.lessons[this.item.model.id].status;
		},
		isInProgress() {
			return this.lessonProgress === STATUS_IN_PROGRESS;
		},
		isComplete() {
			return this.lessonProgress === STATUS_COMPLETE;
		},
		itemClass() {
			return this.lessonItem.itemClass;
		},
		to() {
			return this.lessonItem.to;
		},
		lessonItem() {
			const lesson = this.item.model;
			let cssClass = 'is-grouped with-progress';

			if (this.lessonProgress) {
				cssClass = `${cssClass} ${this.lessonProgress}`;
			}

			return navigation.composeItem({
				text: lesson.name,
				itemClass: cssClass,
				routeName: 'lessons',
				routeParams: {
					courseId: this.courseId,
					lessonId: lesson.id,
				},
				isDisabled: !this.isLessonAvailable(lesson.id),
				iconClass: '',
				iconTitle: ''
			});
		}
	},
};
</script>
