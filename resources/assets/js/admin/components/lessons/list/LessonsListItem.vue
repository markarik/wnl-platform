<template>
	<div class="margin bottom">
		<router-link :to="to">{{name}}</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">

</style>

<script>
export default {
	name: 'LessonsListItem',
	props: ['id', 'name'],
	computed: {
		to() {
			return {
				name: 'lessons',
				params: {
					lessonId: this.id,
				},
			};
		},
	},
};
</script>
