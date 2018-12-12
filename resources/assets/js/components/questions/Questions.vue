<template>
	<router-view/>
</template>

<script>
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';

export default {
	name: 'Questions',
	perimeters: [currentEditionParticipant],
	mounted() {
		if (!this.$currentEditionParticipant.isAllowed('access')) {
			this.$router.replace({name: 'dashboard'});
		}
	}
};
</script>
