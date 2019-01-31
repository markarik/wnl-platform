<script>
// This component is temporary and should be removed once we don't have notifications
// with this component's route context
export default {
	beforeRouteEnter(to, from, next) {
		return next({
			name: 'quiz-question',
			params: {
				quizQuestionId: to.params.id
			}
		});
	}
};
</script>
