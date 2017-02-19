<template>
	<div class="columns is-gapless">
		<div class="column wnl-background-gray">
			<wnl-sidenav :api-url="navigationApiUrl"></wnl-sidenav>
		</div>
		<div class="column is-half">
			<router-view></router-view>
		</div>
		<div class="column">

		</div>
	</div>
</template>

<script>
	import Sidenav from './Sidenav.vue'

	export default {
		name: 'Lesson',
		props: ['courseId', 'lessonId'],
		computed: {
			navigationApiUrl() {
				return $fn.getApiUrl('lessons/' + this.lessonId + '/nav')
			}
		},
		components: {
			'wnl-sidenav': Sidenav
		}
	}
</script>
