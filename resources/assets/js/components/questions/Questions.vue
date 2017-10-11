<template>
	<router-view/>
</template>

<script>
	import firstEditionParticipant from 'js/perimeters/firstEditionParticipant'

	export default {
		name: 'Questions',
		perimeters: [firstEditionParticipant],
		mounted() {
			if (!this.$firstEditionParticipant.isAllowed('access')) {
				this.$router.replace({name: 'dashboard'})
			}
		}
	}
</script>
