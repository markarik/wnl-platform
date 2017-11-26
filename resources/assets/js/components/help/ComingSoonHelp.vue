<template>
	<div class="content">
		<wnl-page slug="coming-soon-help"
				  :qna="true"
				  :arguments="{'currentUserName': currentUserName}"
		></wnl-page>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import Page from 'js/components/global/Page'
	import {mapGetters} from 'vuex'

	export default {
		name: 'ComingSoonHelp',
		components: {
			'wnl-page': Page
		},
		computed: {
			...mapGetters(['currentUserName']),
		},
	}
</script>
