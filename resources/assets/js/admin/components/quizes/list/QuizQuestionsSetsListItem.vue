<template>
	<div class="margin bottom">
		<router-link :to="to">{{id}}. {{name}}</router-link>
	</div>
</template>

<script>
export default {
	props: ['id', 'name'],
	computed: {
		to() {
			return {
				name: 'quiz-sets-edit',
				params: {
					quizQuestionsSetId: this.id,
				},
			};
		},
	},
};
</script>
