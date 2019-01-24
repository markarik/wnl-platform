<template>
	<div class="margin bottom">
		<router-link :to="to">{{ structurable.name }}</router-link>
	</div>
</template>

<script>
export default {
	name: 'LessonNodeElement',
	props: {
		structurable: {
			required: true,
			type: Object,
		}
	},
	computed: {
		to() {
			return {
				name: 'lessons',
				params: {
					lessonId: this.structurable.id,
				},
			};
		},
	},
};
</script>
