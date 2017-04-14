<template>
	<div>
		<wnl-quiz-question v-for="(question, index) in unresolvedQuestions"
			:answers="question.answers"
			:index="index"
			:text="question.text"
			:total="total"
		></wnl-quiz-question>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import _ from 'lodash'
	import QuizQuestion from 'js/components/course/screens/quiz/QuizQuestion.vue'

	export default {
		name: 'Quiz',
		data() {
			return {
				questions: [
					{
						id: 1,
						text: 'W celu obniżenia stężenia potasu we krwi stosuję się wszystkie metody niżej wymienione poza:',
						isResolved: false,
						answers: [
							{
								id: 1,
								text: '1mg/kg m.c. furosemidu i.v.',
								is_correct: false,
							},
							{
								id: 2,
								text: '5mg salbutamolu w nebulizacji',
								is_correct: true,
							},
							{
								id: 3,
								text: '10 j. krótko działającej insuliny i 25 g glukozy i.v. w ciągu 15-30 min.',
								is_correct: false,
							},
							{
								id: 4,
								text: '10 ml 10% CaCl2 i.v.',
								is_correct: false,
							},
							{
								id: 5,
								text: 'wodorowęglan sodu 1mmol/kg m.c. i.v.',
								is_correct: false,
							},
						],
					},
					{
						id: 2,
						text: 'Spoczynkowe EKG może dać prawidłowy zapis u osób z:',
						isResolved: false,
						answers: [
							{
								id: 6,
								text: 'dławicą piersiową stabilną',
								is_correct: true,
							},
							{
								id: 7,
								text: 'sercowym zespole X',
								is_correct: false,
							},
							{
								id: 8,
								text: 'OZW bez uniesienia odcinka ST',
								is_correct: false,
							},
							{
								id: 9,
								text: 'poprawne odpowiedzi A i B',
								is_correct: false,
							},
							{
								id: 10,
								text: 'poprawne odpowiedzi A, B i C',
								is_correct: false,
							},
						],
					},
				],
			}
		},
		computed: {
			total() {
				return _.size(this.questions)
			},
			unresolvedQuestions() {
				return this.questions.filter((question) => !question.isResolved)
			},
		},
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
	}
</script>
