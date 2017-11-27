<template>
	<wnl-avatar
		:fullName="message.actors.full_name"
		:size="size"
		:url="message.actors.avatar"
		:userId="message.actors.id"/>
</template>

<script>
	export default {
		name: 'Actor',
		props: {
			message: {
				required: true,
				type: Object,
			},
			size: {
				default: 'medium',
				type: String,
			},
		},
	}
</script>
