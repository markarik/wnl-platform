<template>
	<div class="margin bottom">
		<router-link :to="to">{{ structurable.name }}</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">

</style>

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
