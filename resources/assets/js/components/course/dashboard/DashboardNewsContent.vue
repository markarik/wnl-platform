<template>
	<div class="notification content">
		<button class="delete" @click="$emit('onClose')"></button>
		<p class="has-text-centered"><strong v-html="slug"></strong></p>
		<span @click="$emit('onContentClick', $event)" v-html="parsedMessage"></span>
	</div>
</template>

<script>
import injectArguments from 'js/utils/injectArguments';

export default {
	name: 'DashboardNewsContent',
	props: {
		slug: {
			type: String,
			default: ''
		},
		message: {
			type: String,
			default: ''
		},
		messageArguments: {
			type: Object,
			default: () => {}
		},
	},
	computed: {
		parsedMessage: function () {
			return injectArguments(this.message, this.messageArguments);
		},
	},
};
</script>
