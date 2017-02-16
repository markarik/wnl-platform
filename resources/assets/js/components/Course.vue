<template>
	<div class="columns is-gapless">
		<div class="column" style="background: #fcfcfc;">
			<wnl-sidenav></wnl-sidenav>
		</div>
		<div class="column is-half">

		</div>
		<div class="column" style="background: #fcfcfc;">

		</div>
	</div>
</template>

<script>
	import Sidenav from './Sidenav.vue'

	export default {
		name: 'Course',
		components: {
			'wnl-sidenav': Sidenav
		}
	}
</script>
