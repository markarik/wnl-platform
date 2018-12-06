<template>
	<div class="notification content">
		<button class="delete" @click="$emit('onClose')"></button>
		<p class="has-text-centered"><strong v-html="slug"></strong></p>
		<span v-html="parsedMessage"></span>
	</div>
</template>

<script>
	import {mapGetters} from 'vuex';
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
		},
		computed: {
			...mapGetters(['currentUserName']),
			arguments: function () {
				return {
					currentUserName: this.currentUserName
				};
			},
			parsedMessage: function () {
				return injectArguments(this.message, this.arguments);
			},
		},
	}
</script>
