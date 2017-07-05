<template>
	<div class="content">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Pomoc techniczna
				</div>
			</div>
		</div>
		<p class="strong">Serwus {{currentUserName}}!</p>
		<p>"Więcej niż LEK" to nie tylko przedsięwzięcie medyczne, ale też technologiczne. ;) Jako zespół, pracujemy po to, by Wasza nauka była przyjemna i bezproblemowa! Gdyby jednak powinęła nam się noga - jesteśmy tutaj, aby odpowiadać na Wasze problemy i jak najszybciej naprawiać błędy. :)</p>
		<p>Jeśli tylko cos Was irytuje, lub nie działa - tutaj jest najlepsze miejsce, aby nam to zgłosić! :)</p>
		<wnl-qna :tags="tags" reactionsDisabled="true"></wnl-qna>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import {mapGetters, mapActions} from 'vuex'

	import Qna from 'js/components/qna/Qna'
	import {getApiUrl} from 'js/utils/env'

	export default {
		name: 'TechnicalHelp',
		components: {
			'wnl-qna': Qna,
		},
		data() {
			return {
				tags: [],
			}
		},
		computed: {
			...mapGetters(['currentUserName']),
		},
		methods: {
			...mapActions('qna', ['fetchQuestionsByTags'])
		},
		mounted() {
			axios.post(getApiUrl('tags/.search'), {
				query: { whereIn: [ 'name', [ 'Błędy', 'Pomoc techniczna' ] ] }
			})
				.then(response => this.tags = _.values(response.data))
				.then(() => this.fetchQuestionsByTags({tags: this.tags}))
				.catch(error => $wnl.logger.error(error))
		},
	}
</script>
