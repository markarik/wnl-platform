<template>
	<div class="columns is-gapless">
		<div class="column">
			<wnl-sidenav :api-url="navigationApiUrl"></wnl-sidenav>
		</div>
		<div class="column is-half">

		</div>
		<div class="column">

		</div>
	</div>
</template>

<script>
	import Sidenav from './Sidenav.vue'

	export default {
		name: 'Course',
		props: ['courseId'],
		computed: {
			navigationApiUrl() {
				return $fn.getApiUrl('courses/' + this.courseId + '/nav')
			}
		},
		components: {
			'wnl-sidenav': Sidenav
		}
	}
</script>
