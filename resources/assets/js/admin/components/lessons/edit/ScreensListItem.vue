<template>
	<div>
		<router-link :to="to">{{name}}</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">

</style>

<script>
	export default {
		name: 'ScreensListItem',
		props: ['id', 'name'],
		computed: {
			to() {
				return {
					name: 'screen-edit',
					params: {
						lessonId: this.$route.params.lessonId,
						screenId: this.id,
					},
				}
			},
		},
	}
</script>
