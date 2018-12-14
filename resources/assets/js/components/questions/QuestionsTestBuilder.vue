<template>
	<div class="questions-test-builder" :class="{'is-desktop': isLargeDesktop}">
		<wnl-questions-test v-if="hasQuestions"
			:questions="questions"
			:time="time * 60"
			:onSelectAnswer="selectAnswer"
			:getReaction="getReaction"
			:testProcessing="testProcessing"
			:testResults="testResults"
			@checkQuiz="(payload) => $emit('checkQuiz', payload)"
			@endQuiz="$emit('endQuiz')"
			@userEvent="proxyUserEvent"
			@testStart="onTestStart"
		/>
		<div v-else>
			<p class="test-builder-title">
				{{$t('questions.solving.test.title')}}
			</p>
			<div class="box has-text-centered">
				<section>
					<p class="test-builder-header">
						<span class="icon is-small">
							<i class="fa fa-signal"></i>
						</span>
						{{$t('questions.solving.test.headers.count')}}
					</p>
					<ul class="set-sizes">
						<li v-for="size, index in sizesToChoose"
							class="set-sizes-option"
							:class="{'is-selected': size === testQuestionsCount}"
							:key="index"
							v-text="size"
							@click="testQuestionsCount = size"
						></li>
					</ul>
				</section>
				<section v-if="canChangeTime">
					<p class="test-builder-header">
						<span class="icon is-small">
							<i class="fa fa-hourglass-start"></i>
						</span>
						{{$t('questions.solving.test.headers.time')}}
					</p>
					<input class="input-clean" max="999" maxlength="3" type="number" v-model="time"/>
					<span class="time-unit">{{$t('units.time.minutes')}}</span>
				</section>
				<section v-else>
					<p class="test-preset-time">
						<span class="icon is-small">
							<i class="fa fa-hourglass-start"></i>
						</span>
						{{$t('questions.solving.test.preset.time', {time: this.time})}}
					</p>
				</section>
				<a class="button is-small is-primary" @click="buildTest">
					{{$t('questions.solving.test.start')}}
				</a>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	section
		padding-bottom: $margin-big

	.test-builder-title
		color: $color-background-gray
		font-size: $font-size-minus-1
		margin: $margin-small 0

	.test-builder-header
		font-size: $font-size-minus-1
		letter-spacing: 1px
		margin-bottom: $margin-small
		text-align: center
		text-transform: uppercase

	.icon
		color: $color-background-gray
		margin-right: $margin-small

	.set-sizes
		+flex-center()

		.set-sizes-option
			+flex-center()
			+clickable()
			border: $border-light-gray
			border-color: $color-ocean-blue
			border-radius: $border-radius-small
			color: $color-ocean-blue
			font-size: $font-size-minus-1
			font-weight: $font-weight-bold
			height: 3em
			margin: 0 $margin-small
			transition: all $transition-length-base
			width: 3em

			&.is-selected
				background: $color-ocean-blue
				color: $color-white
				transition: all $transition-length-base

	.input-clean
		border: 0
		border-bottom: 1px solid $color-ocean-blue
		box-shadow: none
		color: $color-ocean-blue
		font-size: $font-size-plus-2
		font-weight: $font-weight-bold
		padding-left: $margin-base
		text-align: center
		width: 4em

		&:active,
		&:focus
			box-shadow: none
			outline: none

	.time-unit
		font-size: $font-size-minus-1
</style>

<script>
import {isEmpty} from 'lodash';
import {mapGetters} from 'vuex';

import QuestionsTest from 'js/components/questions/QuestionsTest';

import {timeBaseOnQuestions} from 'js/services/testBuilder';
import emits_events from 'js/mixins/emits-events';
import context from 'js/consts/events_map/context.json';

export default {
	name: 'QuestionsTestBuilder',
	components: {
		'wnl-questions-test': QuestionsTest,
	},
	mixins: [emits_events],
	props: {
		getReaction: {
			default: () => {},
			type: Function,
		},
		questions: {
			default: () => [],
			type: Array,
		},
		questionsPoolSize: {
			default: 0,
			type: Number,
		},
		presetOptions: {
			default: () => {},
			type: Object,
		},
		setSizes: {
			default: () => [15, 30, 50, 100, 200],
			type: Array,
		},
		testMode: {
			default: false,
			type: Boolean,
		},
		testProcessing: {
			default: false,
			type: Boolean,
		},
		testResults: {
			default: () => {},
			type: Object,
		},
	},
	data() {
		return {
			testQuestionsCount: 0,
			time: 0,
		};
	},
	computed: {
		...mapGetters(['isLargeDesktop']),
		canChangeTime() {
			return this.presetOptions.hasOwnProperty('canChangeTime')
				? this.presetOptions.canChangeTime
				: true;
		},
		hasQuestions() {
			return !isEmpty(this.questions);
		},
		sizesToChoose() {
			if (this.presetOptions.hasOwnProperty('sizesToChoose')) {
				return this.presetOptions.sizesToChoose;
			}

			if (this.questionsPoolSize > Math.max.apply(null, this.setSizes)) {
				return this.setSizes;
			}

			const sufficientSizes = this.setSizes.filter(n => n < this.questionsPoolSize);
			sufficientSizes.push(this.questionsPoolSize);

			return sufficientSizes;
		},
	},
	methods: {
		buildTest() {
			this.$emit('buildTest', {count: this.testQuestionsCount, time: this.time});
		},
		selectAnswer(payload) {
			this.$emit('selectAnswer', payload);
		},
		onTestStart() {
			this.emitUserEvent({
				action: context.questions_bank.subcontext.test_yourself.actions.new_test.value
			});
		}
	},
	mounted() {
		if (this.presetOptions.hasOwnProperty('testQuestionsCount')) {
			this.testQuestionsCount = this.presetOptions.testQuestionsCount;
		} else {
			this.testQuestionsCount = this.sizesToChoose[0];
		}
	},
	created() {
		if (this.presetOptions.hasOwnProperty('time')) {
			this.time = this.presetOptions.time;
		}
	},
	watch: {
		testQuestionsCount() {
			this.time = timeBaseOnQuestions(this.testQuestionsCount);
		},
	}
};
</script>
