<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p>Cześć!</p>

		<p>Chcielibyśmy przedstawić osoby, które będą pomagać Wam w nauce w trakcie 2. edycji kursu Więcej niż LEK! <wnl-emoji name="tada"/></p>

		<p>Moderatorzy, poza swoimi obowiązkami na platformie, również uczą się do egzaminu, dzięki czemu są w stanie najlepiej stwierdzić czego Wam potrzeba. Już w pierwszym tygodniu uwzględniliśmy wiele ich doskonałych sugestii!</p>

		<p>Dotychczas oni także uczyli się poruszać po platformie, jednak od dzisiaj przejmują stery dyskusji na naszym 500-osobowego statku. <wnl-emoji name="wink"/></p> Dzięki temu “stacjonarny” zespół lekarzy może skupić się na tworzeniu nowych materiałów oraz doskonaleniu istniejących.</p>

		<p>Poznajcie zatem:</p>

		<p><strong>Dominikę Bernat</strong> i <strong>Zbyszka Nowickiego</strong> - twórców portalu LEKDay, którzy niestrudzenie pobudzają środowisko medyczne do dyskusji nad pytaniami z LEK-u w ramach swojego fanpage’a oraz spędzają niezliczone ilości godzin na dociekaniu poprawnych odpowiedzi.</p>

		<p><strong>Wojciecha Zarembę</strong> - człowieka-orkiestrę, twórcę niezwykłego portalu wuzetki.pl, niezastąpionego źródła wiedzy dla studentów Uniwersytetu Jagiellońskiego (choć oczywiście nie tylko) oraz bazy pytań stworzonej na podstawie egzaminów z tamtej uczelni.</p>

		<p><strong>Artura Bandurę</strong> - naturalny talent pedagogiczny, któremu wielu studentów UM w Poznaniu zawdzięcza zrozumienie biofizyki. <wnl-emoji name="wink"/> 4-letni przewodniczący koła Onkologii i Radioterapii przy Wielkopolskim Centrum Onkologii, wyróżniany prelegent konferencji naukowych oraz organizator konferencji “Magis in medicinae”, poświęconej komunikacji z pacjentem.</p>

		<p><strong>Martę Bromirską</strong> - wieloletnią przewodniczącą koła reumatologicznego GUMed, łącząca swoją działalność z wykładami o profilaktyce osteoporozy, ChZS oraz zespołów bólowych na uniwersytetach 3. wieku w Gdańsku, Rumii i Malborku. Autorka badań i prelegentka konferencji ISSC w Gdańsku.</p>

		<p><strong>Gabrielę Kozę-Natorę</strong> - autorkę książki “LEK na 200”, oraz założycielkę portalu o tej samej nazwie, które są doskonałym źródłem opracowań pytań z dotychczasowych egzaminów oraz pytań autorskich.</p>

		<p>Jak widzicie, ekipa na tej edycji jest niezwykle silna! Z taką załogą z pewnością dopłyniemy do celu! <wnl-emoji name="raised_hands"/></p>

		<p>Trzymamy za Ciebie kciuki i życzymy dalszej radości z nauki!</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-2-moderators'
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
			newsStoreKey() {
				return `seen-dashboard-news-${CURRENT_NEWS}`
			}
		},
		methods: {
			seenCurrentNews() {
				this.showNews = false
				store.set(this.newsStoreKey, true)
			},
		},
		mounted() {
			if (CURRENT_NEWS !== '' &&
				!store.get(this.newsStoreKey) &&
				(REQUIRED_ROLE === '' || this.hasRole(REQUIRED_ROLE))
			) {
				this.showNews = true
			}
		},
	}
</script>
