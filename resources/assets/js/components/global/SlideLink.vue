<template>
	<router-link :to="to" :target="blankPage">
        <slot></slot>
	</router-link>
</template>

<script>

export default {
	name: 'SlideLink',
	props: {
		context: {
			required: true,
			type: Object,
			validator: value => {
				return _.get(value, 'course.id')
                        && _.get(value, 'lesson.id')
                        && _.get(value, 'screen.id')
                        && _.get(value, 'orderNumber');
			}
		},
		blankPage: {
			type: String,
		},
	},
	computed: {
		slideNumber() {
			return this.context.orderNumber + 1;
		},
		to() {
			return {
				name: 'screens',
				params: {
					courseId: this.context.course.id,
					lessonId: this.context.lesson.id,
					screenId: this.context.screen.id,
					slide: this.slideNumber,
				},
				query: {
					slide: this.context.id
				}
			};
		},
	},
};
</script>
