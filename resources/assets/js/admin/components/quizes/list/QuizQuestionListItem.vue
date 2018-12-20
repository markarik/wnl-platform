<template>
	<div class="margin bottom item">
		<router-link :to="to" class="link">
			<span class="id">{{id}}. </span>
			<span class="text" v-html="text" />
		</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
@import 'resources/assets/sass/variables'

.link
	display: flex
	justify-content: space-between
	.id
		flex: 0 1 auto
		margin-right: $margin-small
	.text
		flex: 1 0 auto

</style>

<script>
export default {
	name: 'QuizQuestionListItem',
	props: ['id', 'text'],
	computed: {
		to() {
			return {
				name: 'quiz-editor',
				params: {
					quizId: this.id,
				},
			};
		},
	},
};
</script>
