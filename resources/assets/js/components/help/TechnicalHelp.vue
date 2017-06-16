<template>
	<div>
		<h2>Pomoc techniczna</h2>
		<p class="strong">Serwus {{currentUserName}}!</p>
		<p>"Więcej niż LEK" to nie tylko przedsięwzięcie medyczne, ale też technologiczne. ;) Jako zespół, pracujemy po to, by Wasza nauka była przyjemna i bezproblemowa! Gdyby jednak powinęła nam się noga - jesteśmy tutaj, aby odpowiadać na Wasze problemy i jak najszybciej naprawiać błędy. :)</p>
		<wnl-qna :tags="tags" v-if="!loading"></wnl-qna>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import {mapGetters} from 'vuex'

	import Qna from 'js/components/qna/Qna'
	import {getApiUrl} from 'js/utils/env'

	export default {
		name: 'TechnicalHelp',
		components: {
			'wnl-qna': Qna,
		},
		data() {
			return {
				loading: true,
				tags: [],
			}
		},
		computed: {
			...mapGetters(['currentUserName']),
		},
		mounted() {
			axios.post(getApiUrl('tags/.search'), {
				query: { where: [ ['name', '=', 'Pomoc techniczna'] ], }
			})
				.then(response => {
					this.tags = _.values(response.data)
					this.loading = false
				})
				.catch(error => $wnl.logger.error(error))
		},
	}
</script>
