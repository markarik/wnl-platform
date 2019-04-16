<template>
	<span class="timer" @click="$emit('clicked')">
		<span v-show="!hideTime">{{hms}}</span>
		<span v-show="!hideIcon" class="icon is-small">
			<i class="fa" :class="hourglassClass" />
		</span>
	</span>
</template>

<style lang="sass" ref="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.timer
		user-select: none

	.icon
		color: $color-background-gray
</style>

<script>
import { msFromSeconds, hmsFromSeconds } from 'js/utils/time';

export default {
	name: 'QuizTimer',
	props: {
		time: {
			required: true,
			type: Number,
		},
		hideIcon: {
			default: false,
			type: Boolean,
		},
		hideTime: {
			default: false,
			type: Boolean,
		},
	},
	data() {
		return {
			timerId: 0,
			remainingTime: this.time
		};
	},
	computed: {
		hms() {
			return this.time > 60 * 60
				? hmsFromSeconds(this.remainingTime)
				: msFromSeconds(this.remainingTime);
		},
		hourglassClass() {
			if (this.time === this.remainingTime) return 'fa-hourglass-1';
			return `fa-hourglass-${Math.ceil((this.time - this.remainingTime) * 3 / this.time)}`;
		},
	},
	methods: {
		startTimer() {
			// passed time is in minutes
			this.timerId = setInterval(this.countDown, 1000);
		},
		stopTimer() {
			clearInterval(this.timerId);
		},
		countDown() {
			if (--this.remainingTime <= 0) {
				this.$emit('timesUp');
			}
		}
	},
	beforeDestroy() {
		clearInterval(this.timerId);
	}
};
</script>
