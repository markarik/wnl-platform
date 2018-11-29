<template>
	<div class="notification content">
		<button class="delete" @click="$emit('onClose')"></button>
		<p class="has-text-centered"><strong v-html="slug"></strong></p>
		<span v-html="message"></span>
	</div>
</template>

<script>
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
	}
</script>
