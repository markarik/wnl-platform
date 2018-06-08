<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="has-text-centered"><strong>OGŁOSZENIE</strong></p>

		<p class="strong">Cześć {{currentUserName}}! 👋</p>

		<p class="strong">3. edycję kursu czas zacząć!</p>
		<p>W ciągu najbliższych miesięcy spędzisz tu sporo czasu, więc bardzo polecamy zapoznanie się ze <router-link :to="{ name: 'lessons', params: { courseId: 1, lessonId: 16 } }">Wstępem do kursu</router-link>, a zwłaszcza ekranem <router-link :to="{ name: 'screens', params: { courseId: 1, lessonId: 16, screenId: 82 } }">Obsługa platformy</router-link>. 😉</p>

		<p class="has-text-centered margin bottom">
			<router-link class="button is-primary is-outlined" :to="{ name: 'lessons', params: { courseId: 1, lessonId: 16 } }">Odwiedź Wstęp do kursu</router-link>
		</p>

		<p class="margin top">Jeśli czujesz już gotowość do nauki - <strong>zacznij od rozwiązania Wstępnego LEK-u.</strong> Wszystkie instrukcje znajdziesz w lekcji <router-link :to="{ name: 'lessons', params: { courseId: 1, lessonId: 85 } }">Wstępny LEK!</router-link></p>

		<p class="has-text-centered margin bottom">
			<router-link class="button is-primary" :to="{ name: 'lessons', params: { courseId: 1, lessonId: 85 } }">Rozwiąż wstępny LEK!</router-link>
		</p>

		<p><strong>Ważna informacja!</strong> Jeżeli Twoja praca z kursem rozpoczęła się już wcześniej (np. 15 maja), a teraz chcesz przywrócić domyślny plan kursu - możesz to zrobić w zakładce <router-link :to="{ name: 'lessons-availabilites' }">KONTO > Plan pracy</router-link>. WAŻNE! Nie musisz usuwać zapisanych w Kolekcjach pytań, postępu w lekcjach, ani rozwiązanych pytań kontrolnych. 🙂</p>

		<p>Życzymy powodzenia i owocnej pracy z kursem!</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-3-course-beginning'
	const DISPLAY_FROM = new Date('2018-06-09 03:00:00') // new Date() or empty string
	const DISPLAY_UNTIL = '' // new Date() or empty string
	const REQUIRED_ROLE = ''

	export default {
		name: 'DashboardNews',
		data() {
			return {
				showNews: false
			}
		},
		computed: {
			...mapGetters(['currentUserName', 'hasRole']),
			hasNews() {
				return CURRENT_NEWS !== ''
			},
			hasRequiredRole() {
				return REQUIRED_ROLE === '' || this.hasRole(REQUIRED_ROLE)
			},
			hasSeenNews() {
				return !!store.get(this.newsStoreKey)
			},
			isNewsTimely() {
				const now = new Date()
				return (!(DISPLAY_FROM instanceof Date) || DISPLAY_FROM < now) &&
				(!(DISPLAY_UNTIL instanceof Date) || DISPLAY_UNTIL > now)
			},
			newsStoreKey() {
				return `seen-dashboard-news-${CURRENT_NEWS}`
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
