<template lang="html">
	<span>
		<wnl-avatar
				:url="event.actors.avatar"
				:fullName="event.actors.full_name"
				size="small"/>
		<a>{{ event.actors.full_name }}</a>
	</span>
</template>

<style lang="sass">
</style>

<script>
	export default {props: ['event']}
</script>
