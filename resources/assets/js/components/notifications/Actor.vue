<template>
	<div>
		<wnl-avatar size="medium"
			:url="message.actors.avatar"
			:fullName="message.actors.full_name"/>
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
