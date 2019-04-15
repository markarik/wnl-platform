<template>
	<div class="wnl-quiz-stats">
		<div class="level">
			<div class="level-item has-text-centered">
				<div>
					<p class="heading">Pierwszy wynik</p>
					<p class="title">{{firstAttemptScore}}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-quiz-stats
		margin: $margin-base 0
</style>

<script>
import { mapGetters } from 'vuex';

export default {
	name: 'QuizStats',
	computed: {
		...mapGetters('quiz', ['getAttempts']),
		attempts() {
			return this.getAttempts.length;
		},
		firstAttemptScore() {
			if (this.attempts > 0) {
				return `${this.getAttempts[0].score}%`;
			}

			return 'Brak';
		},
	},
};
</script>
