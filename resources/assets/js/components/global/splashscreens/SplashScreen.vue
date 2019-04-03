<template>
    <wnl-upcoming-edition v-if="$upcomingEditionParticipant.isAllowed('access')"/>
    <wnl-no-access v-else/>
</template>

<script>
import { set } from 'vue';
import upcomingEditionParticipant from 'js/perimeters/upcomingEditionParticipant';
import WnlNoAccess from 'js/components/global/splashscreens/NoAccess';
import WnlUpcomingEdition from 'js/components/global/splashscreens/UpcomingEdition';

export default {
	components: { WnlNoAccess, WnlUpcomingEdition },
	perimeters: [upcomingEditionParticipant],
};
</script>
