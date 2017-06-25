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
		<p>Nieustająco staramy się ulepszać naszą platformę i czynić ją bogatszą w przydatne funkcje! Nad tymi nowościami pracujemy obecnie:</p>
		<ul>
			<li><strong>Kolekcje</strong> - wyświetlanie zapisanych slajdów, pytań kontrolnych, czy wątków z pytań i odpowiedzi</li>
			<li><strong>Notyfikacje i newsfeed</strong> - bieżące informacje o aktywności uczestników. Ktoś skomentował Twoją odpowiedź? Ktoś odpowiedział na Twoje pytanie? Dowiesz się o tym z notyfikacji. Oprócz tego na głównej stronie kursu stworzymy poszerzony widok ostatnich wydarzeń (jak newsfeed na facebooku). ;)</li>
			<li><strong>Wyszukiwarka</strong> - mamy dostęp do ogrodnej bazy wiedzy medycznej! Teraz trzeba móc łatwo ją przeszukiwać... :)</li>
			<li><strong>Prywatne wiadomości</strong> - czasem po prostu chcemy pogadać z kimś prywatnie. Nasza platforma oczywiście będzie na to pozwalała.</li>
		</ul>
		<p>Jeśli macie pomysł co jeszcze moglibyśmy wziąć na warsztat - tu jest doskonałe miejsce na Wasze sugestie!</p>
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
		name: 'ComingSoonHelp',
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
				query: { whereIn: [ 'name', [ 'Nowe funkcje', 'Sugestie' ] ] }
			})
				.then(response => {
					this.tags = _.values(response.data)
					this.loading = false
				})
				.catch(error => $wnl.logger.error(error))
		},
	}
</script>
