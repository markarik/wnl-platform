<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p>Cześć!</p>

		<p>Chcielibyśmy przedstawić osoby, które będą pomagać Wam w nauce w trakcie 2. edycji kursu Więcej niż LEK! <wnl-emoji name="tada"/></p>

		<p>Moderatorzy, poza swoimi obowiązkami na platformie, również uczą się do egzaminu, dzięki czemu są w stanie najlepiej stwierdzić czego Wam potrzeba. Już w pierwszym tygodniu uwzględniliśmy wiele ich doskonałych sugestii!</p>

		<p>Dotychczas oni także uczyli się poruszać po platformie, jednak od dzisiaj przejmują stery dyskusji na naszym 500-osobowego statku. <wnl-emoji name="wink"/></p> Dzięki temu “stacjonarny” zespół lekarzy może skupić się na tworzeniu nowych materiałów oraz doskonaleniu istniejących.</p>

		<p>Poznajcie zatem zespół w składzie:</p>

		<p><wnl-emoji name="muscle"/> <strong>Dominika Bernat</strong> i <strong>Zbyszek Nowicki</strong> - twórcy portalu LEKDay, którzy niestrudzenie pobudzają środowisko medyczne do dyskusji nad pytaniami z LEK-u w ramach swojego fanpage’a oraz spędzają niezliczone ilości godzin na dociekaniu poprawnych odpowiedzi.</p>

		<p><wnl-emoji name="+1"/> <strong>Wojciech Zaremba</strong> - człowiek-orkiestra, twórca niezwykłego portalu wuzetki.pl, niezastąpionego źródła wiedzy dla studentów Uniwersytetu Jagiellońskiego (choć oczywiście nie tylko) oraz bazy pytań stworzonej na podstawie egzaminów z tejże uczelni.</p>

		<p><wnl-emoji name="clap"/> <strong>Artur Bandura</strong> - naturalny talent pedagogiczny, zaczynał od przedegzaminowych seminariów z biofizyki dla znajomych, potem rozpoczął działalność koła Onkologii i Radioterapii przy Wielkopolskim Centrum Onkologii. Wyróżniany prelegent konferencji naukowych oraz inicjator pierwszych dwóch edycji konferencji “Magis in medicinae”, poświęconej komunikacji z pacjentem. Obecnie na stażu w Gdańsku, a popołudniami współuczestnik przygody z "Więcej niż LEK".</p>

		<p><wnl-emoji name="raised_hands"/> <strong>Marta Bromirska</strong> - wieloletnia przewodnicząca koła reumatologicznego GUMed, łącząca swoją działalność z licznymi wykładami m.in. o profilaktyce osteoporozy, leczeniu choroby zwyrodnieniowej oraz zespołów bólowych na uniwersytetach 3. wieku w Gdańsku, Rumi i Malborku. Autorka badań i prelegentka konferencji ISSC w Gdańsku. Po godzinach miłośniczka kinematografii oraz impresjonizmu.</p>

		<p><wnl-emoji name="ok_hand"/> <strong>Gabriela Koza-Natora</strong> - współautorka książki “LEK na 200”, oraz portalu o tej samej nazwie, które są doskonałym źródłem opracowań pytań z dotychczasowych egzaminów oraz pytań autorskich.</p>

		<p>Jak widzicie, ekipa na tej edycji jest niezwykle silna! Z taką załogą z pewnością dopłyniemy do celu! <wnl-emoji name="wink"/></p>

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
