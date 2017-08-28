<template>
	<div>
		<wnl-questions-test v-if="testMode && hasQuestions"
			:questions="questions"
			:time="time * 60"
			:onSelectAnswer="selectAnswer"
			:onCheckQuiz="checkQuestions"
			:getReaction="getReaction"
			@endTest="testMode = false"
		/>
		<div v-else>
			<section>
				<p>Na ile pytań chcesz odpowiedzieć?</p>
				<input type="radio" name="count" value="30" id="countThirty" v-model="testQuestionsCount"/>
				<label for="countThirty">30 pytań</label>
				<input type="radio" name="count" value="50" id="countFifty" v-model="testQuestionsCount"/>
				<label for="countFifty">50 pytań</label>
				<input type="radio" name="count" value="100" id="countHundred" v-model="testQuestionsCount"/>
				<label for="countHundred">100 pytań</label>
				<input type="radio" name="count" value="150" id="countOneFifty" v-model="testQuestionsCount"/>
				<label for="countNinty">150 pytań</label>
				<input type="radio" name="count" value="200" id="countTwoHundred" v-model="testQuestionsCount"/>
				<label for="countTwoHundred">200 pytań</label>
			</section>
			<section>
				<label for="time">Ile czasu chcesz poświęcić?</label>
				<input type="text" name="time" v-model="time"/>
				<span>minut</span>
			</section>
			<button @click="buildTest">No to GO!</button>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {isEmpty} from 'lodash'

	import QuestionsTest from 'js/components/questions/QuestionsTest'

	import {timeBaseOnQuestions} from 'js/services/testBuilder'

	export default {
		name: 'QuestionsTestBuilder',
		components: {
			'wnl-questions-test': QuestionsTest,
		},
		props: {
			getReaction: {
				default: () => {},
				type: Function,
			},
			questions: {
				default: () => [],
				type: Array,
			},
			testMode: {
				default: false,
				type: Boolean,
			},
		},
		data() {
			return {
				testQuestionsCount: 0,
				time: 0,
			}
		},
		computed: {
			hasQuestions() {
				return !isEmpty(this.questions)
			},
		},
		methods: {
			buildTest() {
				// TODO: Allow to change time
				this.$emit('buildTest', {count: this.testQuestionsCount, time: this.time})
			},
			checkQuestions() {
				this.$emit('checkQuestions')
			},
			selectAnswer(payload) {
				this.$emit('selectAnswer', payload)
			},
		},
		watch: {
			testQuestionsCount() {
				this.time = timeBaseOnQuestions(this.testQuestionsCount)
			},
		}
	}
</script>
