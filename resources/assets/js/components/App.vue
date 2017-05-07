<template>
	<div id="app">
		<wnl-navbar :show="true"></wnl-navbar>
		<div class="wnl-main">
			<router-view></router-view>
		</div>
	</div>
</template>

<script>
	// Import global components
	import Navbar from 'js/components/global/Navbar.vue'
	import store from 'store'
	import { mapActions } from 'vuex'
	import { swalConfig } from 'js/utils/swal'

	const CACHE_VERSION = 1

	export default {
		name: 'App',
		components: {
			'wnl-navbar': Navbar
		},
		methods: {
			...mapActions(['setupCurrentUser']),
			displayScreenResolutionInfo() {
				const resolutionInfoKey = `has-seen-resolution-info-${CACHE_VERSION}`
				const resolutionInfoValue = 1
				const resolutionLimit = 960

				if (store.get(resolutionInfoKey) !== resolutionInfoValue &&
					window.innerWidth < resolutionLimit) {
					this.$swal(swalConfig({
						html: `
							<p class="normal margin bottom">
								Sprawdź demo na komputerze lub tablecie!
							</p>
							<p class="small strong margin bottom">Na razie aplikacja działa najlepiej przy szerokości ekranu większej niż 960px.</p>
							<p class="small">
								Pracujemy już nad wersją mobilną platformy, na razie jednak nie jest ona
								dostosowana pod małe rozdzielczości ekranu. :(
							</p>`,
						title: `Wersja mobilna`,
						type: 'info',
					}))
					store.set(resolutionInfoKey, resolutionInfoValue)
				}
			}
		},
		created: function () {
			this.setupCurrentUser()
			this.displayScreenResolutionInfo()
		}
	}
</script>
