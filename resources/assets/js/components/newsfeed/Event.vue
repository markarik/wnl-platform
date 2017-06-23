<template lang="html">
	<div class="wnl-newsfeed-notification">
		{{ formattedTime }}
		<component :is="componentName" :event="event">
		</component>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import QnaAnswerPosted from '../newsfeed/events/QnaAnswerPosted'
	import {timeFromS} from 'js/utils/time'

	export default {
		name: 'wnl-newsfeed-event',
		props: ['event'],
		computed: {
			componentName() {
				return `wnl-event-${this.event.event}`
			},
			formattedTime () {
				return timeFromS(this.event.timestamp)
			}
		},
		components: {
			'wnl-event-qna-answer-posted': QnaAnswerPosted
		}
	}
</script>
