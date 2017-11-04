<template>
	<div class="content">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Nad czym pracujemy?
				</div>
			</div>
		</div>
		<p class="strong">Hej {{currentUserName}}!</p>
		<p>Nieustająco staramy się ulepszać naszą platformę i czynić ją bogatszą w przydatne funkcje!</p>
		<p>Jeśli macie pomysł co jeszcze moglibyśmy wziąć na warsztat - tu jest doskonałe miejsce na Wasze sugestie!</p>
		<wnl-qna :sortingEnabled="true" :tags="tags" reactionsDisabled="true"></wnl-qna>
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
		name: 'ComingSoonHelp',
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
				query: { whereIn: [ 'name', [ 'Nowe funkcje', 'Sugestie' ] ] }
			})
				.then(response => this.tags = _.values(response.data))
				.then(() => this.fetchQuestionsByTags({tags: this.tags}))
				.catch(error => $wnl.logger.error(error))
		},
		watch: {
			'tags' (newValue) {
				this.fetchQuestionsByTags({tags: newValue})
			}
		}
	}
</script>
