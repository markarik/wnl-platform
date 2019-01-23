<template>
	<div class="margin bottom">
		<router-link :to="to">{{ name }}</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">

</style>

<script>
export default {
	name: 'LessonNodeElement',
	props: ['structurable'],
	data(){
		return {...this.structurable}
	},
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
