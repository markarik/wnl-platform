<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Ogłoszenia
				</div>
			</div>
		</div>
		<article class="margin bottom">
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item metadata">
						Niedziela, 2 lipca 2017
					</div>
				</div>
			</div>
			<div class="content">
				<p class="strong">Cześć!</p>

				<p>Za nami pierwsze dwa tygodnie kursu! Dla nas również były one niezwykłą lekcją. Wasze pytania, sugestie, a także krytyka, nauczyły nas bardzo wiele o tym co jest dla Was ważne. Pozwoliły też zweryfikować nasze plany na najbliższe tygodnie.</p>

				<p>Rozwiązanie wszystkich problemów w ramach tej edycji jest zadaniem ponad nasze siły. Zrobimy jednak co w naszej mocy, aby odpowiedzieć działaniem na najbardziej palące z nich.</p>

				<p>Chcemy dziś podzielić się z Wami kolejnymi krokami, które podejmiemy. Przy okazji mamy też parę ogłoszeń. :) Zaczynamy!</p>

				<h3>Ankieta</h3>
				<p>Dziękujemy wszystkim, którzy poświęcili swój czas i dostarczyli nam niezwykle istotnych informacji o swoim zadowoleniu (i niezadowoleniu), motywacji oraz ocenie kursu. Postaramy się w przyszłym tygodniu opracować wyniki i podzielić się bardziej szczegółowo naszymi wnioskami. Jesteśmy ciekawi, czy zgodzicie się z nimi. :)</p>

				<h3>Prezentacje - zmiany, poprawki, nowe zagadnienia</h3>
				<ol>
					<li>W ramach testów, uruchomiliśmy naszemu zespołowi notyfikacje o wszelkiej aktywności na platformie. Pozwala nam to dużo szybciej reagować na zadawane przez Was pytania, zwłaszcza w komentarzach do slajdów. Możecie już dodawać je bez obaw, że umkną naszej uwadze. :)</li>
					<li>Na dzień dzisiejszy, wszystkie Wasze sugestie zmian w prezentacjach zostały uwzględnione, a slajdy poprawione. Dotyczy to zarówno zmian merytorycznych, jak i literówek. ;)</li>
					<li>W połowie kursu wzbogacimy prezentacje z Interny o dodatkowe zagadnienia, które pojawiły się w pytaniach do LEK-u, a których obecnie brakuje w prezentacjach. Pediatria, Chirurgia oraz wszystkie kolejne lekcje podlegają już poprawionemu procesowi, który uwzględnia szczegółową analizę bazy pytań i opracowanie wszystkich pojawiających się tam zagadnień.</li>
					<li>Do każdej lekcji dodamy ekran "Errata do prezentacji". Opublikujemy tam wykaz zmian, które wprowadzamy w prezentacjach. Dzięki temu nie będziecie musieli przeglądać wszystkich slajdów, aby przekonać się czy zmiany, na które czekaliście, zostały już wprowadzone. Dotyczy to zarówno Interny, jak i wszystkich kolejnych przedmiotów.</li>
					<li>Dodaliśmy więcej sekcji do prezentacji, poprawiając nawigację i ten sam poziom szczegółowości stosujemy dla kolejnych przedmiotów. Pracujemy ponadto nad uzupełnieniem lub dodaniem dodatkowych informacji, które ułatwią Wam nawigację po slajdach - slajdów tytułowych, poprawionych podtytułów. Wszystko po to, aby zmniejszyć ryzyko zgubienia kontekstu.</li>
				</ol>

				<h3>Pytania otwarte</h3>

				<p>W ankietach przewijała się również sugestia wzbogacenia pytań otwartych o linki do powiązanego materiału w prezentacjach. Najlepszą odpowiedzią na ten problem będzie wyszukiwarka, która pozwoli łatwo zlokalizować nawet bardzo szczegółowe informacje, nie tylko pośród slajdów, ale też pytań kontrolnych oraz systemu pytań i odpowiedzi. Planujemy publikację jej pierwszej wersji jeszcze w tym miesiącu.</p>

				<p>Zachęcamy także do wydrukowania sobie zestawów pytań i opracowania tych, które sprawiają Wam trudność.</p>

				<h3>Nowy osoba w zespole czuwającym nad merytoryką kursu</h3>

				<p>W zeszłym tygodniu do zespołu moderatorów dołączył dr hab. n. med. Mariusz Puszczewicz, prof. UMP. Pan Profesor będzie odpowiadał na pytania zadane na platformie oraz tworzył na ich podstawie program warsztatów z Interny.</p>

				<h3>Rozwój platformy</h3>

				<p>Po dwóch tygodniach prac stabilizujących platformę, oraz rozwiązywania niewykrytych wcześniej problemów, wracamy do pracy nad jej nowymi funkcjami! Możecie spodziewać się ich w następującej kolejności:</p>

				<ol>
					<li>Kolekcje zapisanych slajdów, pytań kontrolnych, oraz wątków z systemu pytań i odpowiedzi</li>
					<li>Powiadomienia o aktywności dotyczących zadanych przez Was pytań, udzielonych odpowiedzi, czy skomentowanych slajdów</li>
					<li>Wyszukiwarka wiedzy</li>
					<li>Baza pytań - 4000 pytań kontrolnych z możliwością tworzenia własnych zestawów, przerabiania problematycznych pytań i szybkich testów powtórkowych</li>
				</ol>

				<h3>Avatary</h3>

				<p>Na koniec zachęcamy do ustawienia avatarów - teraz widać kto uczy się razem z nami! Dzięki temu lepiej się poznamy! :)</p>

				<p class="margin top">Uff, to już wszystko na dziś! Pracy jest sporo, zarówno przed Wami, jak i przed nami. ;) Na szczęście, możemy też stwierdzić, że motywacja jest po obu stronach wyjątkowo silna!</p>

				<p>Z pozdrowieniami,</p>

				<p><em>Zespół Więcej niż LEK</em></p>
			</div>
		</article>
		<wnl-qna :tags="tags" v-if="isLoading" reactionsDisabled="true"></wnl-qna>
	</div>
</template>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import {mapActions} from 'vuex'

	import Qna from 'js/components/qna/Qna'
	import {getApiUrl} from 'js/utils/env'

	export default {
		name: 'News',
		components: {
			'wnl-qna': Qna,
		},
		data() {
			return {
				tags: [],
				isLoading: true
			}
		},
		methods: {
			...mapActions('qna', ['fetchQuestionsByTags'])
		},
		mounted() {
			axios.post(getApiUrl('tags/.search'), {
				query: { where: [ ['name', '=', 'Ogłoszenia'] ], }
			})
				.then(response => this.tags = _.values(response.data))
				.then(() => this.fetchQuestionsByTags({tags: this.tags}))
				.then(() => {
					this.isLoading = false
				})
				.catch(error => $wnl.logger.error(error))
		},
		watch: {
			'tags' (newValue) {
				this.fetchQuestionsByTags({tags: newValue})
			}
		}
	}
</script>
