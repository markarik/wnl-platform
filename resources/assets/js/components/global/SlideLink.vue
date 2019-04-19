<template>
	<router-link :to="to" :target="blankPage">
		<slot />
	</router-link>
</template>

<script>
import { get } from 'lodash';

export default {
	name: 'SlideLink',
	props: {
		context: {
			required: true,
			type: Object,
			validator: value => {
				return get(value, 'course.id')
                        && get(value, 'lesson.id')
                        && get(value, 'screen.id')
                        && get(value, 'orderNumber');
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
				name: 'lessons',
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
