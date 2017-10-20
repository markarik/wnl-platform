<template>
    <div class="wnl-activity-meter">
        <h1>{{ activityCount }}</h1>
        <h3>{{ activity }}</h3>
    </div>

</template>

<style lang="sass">
    .wnl-activity-meter
        display: flex
        flex-direction: column
        align-items: center
</style>

<script>
export default {
    name: 'ActivityMeter',
    props: ['activityCount', 'activity'],
}
</script>
