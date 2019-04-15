<template>
	<div class="user-info full-width-sidenav-slot">
		<span class="label" v-t="label"></span>
		<span class="info">{{info}}</span>
	</div>
</template>

<script>
export default {
	name: 'UserInfo',
	props: ['label', 'info'],
};
</script>

<style lang="sass">
    @import 'resources/assets/sass/variables'

    .user-info
        border-bottom: $border-light-gray
        margin-bottom: $margin-base
        overflow-wrap: break-word
        padding-bottom: $margin-base
        width: 100%
        .label
            color: $color-gray
            font-size: $font-size-plus-1
            font-weight: $font-weight-regular
            text-transform: uppercase
        .info
            display: inline-block
            max-width: 100%

</style>
