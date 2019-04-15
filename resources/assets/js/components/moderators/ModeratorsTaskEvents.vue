<template>
	<div>
		<h5>Ostatnia aktywność:</h5>
		<div class="moderators-notification">
			<div class="notification-content">
				<div class="notification-header">
					<span class="actor">{{lastEvent.data.actors.full_name}}</span>
					<span class="action">{{eventAction(lastEvent)}}</span>
					<span class="object" v-if="eventObject(lastEvent)">{{eventObject(lastEvent)}}</span>
					<span class="object-text wrap" v-if="objectText">{{objectText}}</span>
					<span class="subject wrap">{{eventText(lastEvent)}}</span>
				</div>
			</div>
		</div>
		<div
			class="moderators-notification"
			v-show="expanded"
			v-for="(event, index) in rest"
			:key="index"
		>
			<div class="notification-content">
				<div class="notification-header">
					<span class="actor">{{event.data.actors.full_name}}</span>
					<span class="action">{{eventAction(event)}}</span>
					<span class="object" v-if="eventObject(event)">{{eventObject(event)}}</span>
					<span class="subject wrap">{{eventText(event)}}</span>
				</div>
			</div>
		</div>
		<p v-if="hasMore">
			<a class="secondary-link toggable-icon" @click="toggleEvents">
				{{expanded ? $t('ui.action.hide') : $t('ui.action.showAll')}}
				<span class="icon"><i class="fa" :class="iconClass"></i></span>
			</a>
		</p>

	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.center
		text-align: center
		padding: $margin-small

	.toggable-icon
		display: flex
		align-items: center
		justify-content: center

		.icon .fa
			font-size: 0.825rem

	.moderators-notification
		align-items: flex-start
		border: $border-light-gray
		border-radius: $border-radius-small
		display: flex
		flex: 1 auto
		font-size: $font-size-minus-1
		justify-content: space-between
		margin: $margin-tiny
		padding: $margin-medium
		position: relative
		transition: background-color $transition-length-base
	.actor
		font-weight: bold
	.notification-content
		flex: 1 auto
		padding: 0 $margin-medium
		.notification-header
			line-height: $line-height-minus
		.object
			font-weight: $font-weight-bold
		.object-text,
		.subject
			&::before
				content: '« '
			&::after
				content: ' »'
		.object-text
			color: $color-gray
		.subject
			font-size: $font-size-base
			line-height: $line-height-minus
			margin: $margin-small 0
		.time
			align-items: center
			color: $color-background-gray
			display: flex
			flex-direction: row
			font-size: $font-size-minus-1
			justify-content: space-between
			margin-top: $margin-tiny
			&.is-mobile
				flex-direction: column
			.icon
				margin-right: $margin-tiny
			.actions
				.button + .button
					margin-left: $margin-small
			.status-text
				margin-right: $margin-small
				text-transform: uppercase
				&.in-progress
					color: $color-blue
				&.done
					color: $color-green
</style>

<script>
import { decode } from 'he';
import { last, truncate, camelCase } from 'lodash';
import { mapGetters } from 'vuex';

export default {
	props: {
		events: {
			type: Array,
			required: true
		},
	},
	data() {
		return {
			expanded: false
		};
	},
	computed: {
		...mapGetters('course', ['getLesson']),
		lastEvent() {
			return last(this.events);
		},
		iconClass() {
			return this.expanded ? 'fa-chevron-up' : 'fa-chevron-down';
		},
		text() {
			return decode(truncate(this.lastEvent.data.subject.text, { length: 256 }));
		},
		objectText() {
			if (!this.lastEvent.data.objects) return false;

			return decode(truncate(this.lastEvent.data.objects.text, { length: 256 }));
		},
		hasMore() {
			return this.events.length > 1;
		},
		action() {
			return this.$t(`notifications.events.${camelCase(this.lastEvent.data.event)}`);
		},
		rest() {
			return this.events.slice(0, this.events.length - 1);
		}
	},
	methods: {
		toggleEvents() {
			this.expanded = !this.expanded;
		},
		eventAction(event) {
			return this.$t(`notifications.events.${camelCase(event.data.event)}`);
		},
		eventText(event) {
			return decode(truncate(event.data.subject.text, { length: 256 }));
		},
		eventObject(event) {
			const objects = event.data.objects;
			const subject = event.data.subject;
			if (!objects && !subject) return false;

			// Qna Quesiton posted
			if (subject && !objects) {
				return this.$tc(`notifications.objects.${camelCase(subject.type)}`, 1);
			}

			return this.$tc(`notifications.objects.${camelCase(objects.type)}`, 1);
		},
	}
};
</script>
