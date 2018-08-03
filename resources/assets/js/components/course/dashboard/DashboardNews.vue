<template>
	<div class="margin bottom">
		<div class="notification content" v-if="showNews">
			<button class="delete" @click="seenCurrentNews"></button>
			<p>Serdecznie witamy Cię w naszym wirtualnym pokoju nauki&nbsp;do&nbsp;LEK&#8209;u!&nbsp;<wnl-emoji name="raised_hands"/></p>

			<p>Jest to wersja demonstracyjna platformy, więc możesz ją śmiało zwiedzać, klikać na co masz ochotę i pisać... no, może nie wszystko. <wnl-emoji name="wink"/></p>

			<p>Jeśli wiesz niewiele na temat kursu Więcej niż LEK, zapraszamy Cię do zapoznania się z lekcją <router-link :to="introLessonRoute">O kursie</router-link>. Znajdziesz w niej odpowiedzi na wiele pytań, w tym to najważniejsze - dlaczego warto uczyć się z nami? <wnl-emoji name="thinking_face"/></p>

			<p>Może jednak być tak, że o kursie wiesz już niemal wszystko, ale odwiedzasz nas, żeby bliżej zapoznać się z lekcjami i samą platformą. W takim razie zapraszamy prosto do przykładowych lekcji, które znajdziesz w&nbsp;<span class="icon is-small" v-if="isTouchScreen"><i class="fa fa-bars"></i></span>&nbsp;MENU!</p>

			<!-- &nbsp;<wnl-emoji name="mortar_board"/> -->

			<p>Oprócz lekcji, w MENU mamy dziś również Kolekcje - zbiór zapisanych przez Ciebie slajdów i pytań kontrolnych, oraz Pytania - naszą autorską aplikację do pracy z obszerną bazą 3500 pytań! Brzmi dobrze? <wnl-emoji name="smirk"/></p>

			<p>Życzymy Ci przyjemnego korzystania z platformy i... do zobaczenia na kursie!</p>

			<p class="has-text-centered">
				<router-link class="button is-primary is-outlined is-small" :to="introLessonRoute">
					Przejdź do lekcji O kursie
				</router-link>
			</p>

			<p>P.S. W razie problemów, w MENU znajdziesz też Pomoc! <wnl-emoji name="ambulance"/></p>
		</div>
		<div class="has-text-centered small" v-else>
			<a @click="showNews = true">Pokaż powitanie</a>
		</div>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'
	import { snakeCase } from 'lodash'
	import { getFirstLessonId } from '../../../utils/env'

	const CURRENT_NEWS = 'demo-welcome'
	const REQUIRED_ROLE = ''

	export default {
		name: 'DashboardNews',
		data() {
			return {
				showNews: false
			}
		},
		computed: {
			...mapGetters(['currentUserName', 'hasRole', 'isTouchScreen']),
			newsStoreKey() {
				return `seen-dashboard-news-${CURRENT_NEWS}-${snakeCase(this.currentUserName)}`
			},
			introLessonRoute() {
				return {
					name: 'lessons',
					params: {
						courseId: 1,
						lessonId: getFirstLessonId(),
					},
				}
			},
		},
		methods: {
			seenCurrentNews() {
				this.showNews = false
				store.set(this.newsStoreKey, true)
			},

		},
		mounted() {
			this.showNews = (this.hasNews && !this.hasSeenNews &&
				this.hasRequiredRole && this.isNewsTimely)
		},
	}
</script>
