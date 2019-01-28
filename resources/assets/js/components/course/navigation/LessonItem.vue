<template>
	<li class="item" :class="[itemClass, { disabled: item.isDisabled }]">
		<router-link
			class="item-wrapper"
			:class="{'router-link-exact-active': item.active, 'is-disabled': item.isDisabled, 'is-completed': item.completed}"
			:to="to"
		>
			<div class="sidenav-icon-wrapper">
				<span class="icon is-small">
					<i title="W trakcie..." class="fa fa-ellipsis-h" v-if="isInProgress"></i>
					<i title="Zrobione!" class="fa fa-check-square-o" v-else-if="isComplete"></i>
					<i title="Jeszcze przed Tobą" class="fa fa-square-o" v-else></i>
				</span>
			</div>
			<span class="sidenav-item-content">
				{{lessonItem.text}}
			</span>
		</router-link>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	=subitem-indent($nestLevel)
		margin-left: $margin-base + $margin-base * $nestLevel

	.item-wrapper
		height: 100%
		width: 100%
		user-select: none

	.is-grouped
		padding-left: $margin-base

	.has-icon
		.icon
			color: $color-inactive-gray

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

		&:hover
			color: $color-ocean-blue

		&.router-link-exact-active
			background: $color-background-lighter-gray
			font-weight: $font-weight-regular
			transition: background-color $transition-length-base

		&.is-active
			font-weight: $font-weight-regular

	.todo
		a:before
			color: $color-background-gray
			content: '○'
			margin-left: $margin-tiny

		a.is-completed:before
			content: '✓'

	.subitem
		+subitem-indent(0)

		.icon.is-small
			margin-right: 0

	.subitem--second
		+subitem-indent(2)

	.toggle
		color: $color-background-gray
		transition: all $transition-length-base
</style>

<script>
import {mapGetters} from 'vuex';
import navigation from 'js/services/navigation';

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
		courseProgress() {
			return this.getCourseProgress(1);
		},
		isInProgress() {
			return this.hasClass('in-progress');
		},
		isComplete() {
			return this.hasClass('complete');
		},
		itemClass() {
			return this.lessonItem.itemClass;
		},
		to() {
			return this.lessonItem.to;
		},
		lessonItem() {
			const lesson = this.item.model;
			let cssClass = 'is-grouped with-progress', iconClass = '', iconTitle = '';

			if (this.courseProgress.lessons && this.courseProgress.lessons.hasOwnProperty(lesson.id)) {
				cssClass = `${cssClass} ${this.courseProgress.lessons[lesson.id].status}`;
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
				iconClass,
				iconTitle
			});
		}
	},
	methods: {
		hasClass(className) {
			return !!this.itemClass && this.itemClass.indexOf(className) > -1;
		}
	},
};
</script>
