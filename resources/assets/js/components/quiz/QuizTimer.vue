<template>
	<div class="timer sticky">
	{{remaingTime}}
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	.timer.sticky
		position: fixed
		bottom: 10px
		right: 10px
		font-size: 24px
		z-index: 10000
</style>

<script>
	export default {
		name: 'QuizTimer',
		props: ['time'],
		data() {
			return {
				timerId: 0,
				remaingTime: this.time
			}
		},
		methods: {
			startTimer() {
				// passed time is in minutes
				this.timerId = setInterval(this.countDown, 1000);
			},
			stopTimer() {
				clearInterval(this.timerId)
			},
			countDown() {
				if (--this.remaingTime < 0) {
					this.$emit('timesUp')
				}
			}
		},
		beforeDestroy() {
			clearInterval(this.timerId)
		}
	}
</script>
