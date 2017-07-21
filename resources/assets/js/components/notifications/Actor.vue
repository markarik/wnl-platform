<template>
	<div>
		<wnl-avatar
			size="medium"
			:url="message.actors.avatar"
			:fullName="message.actors.full_name"
		></wnl-avatar>
	</div>
</template>

<script>
	export default {
		name: 'Actor',
		props: {
			message: {
				required: true,
				type: Object,
			},
		},
	}
</script>
