export const questions = {
	dashboard: {
		notifications: {
			close: 'Ukryj',
			heading: 'Ostatnie dyskusje',
			toggle: 'Pokaż dyskusje',
			toggleBar: 'Dyskusje',
		},
		plan: {
			create: {
				cta: 'Zaplanuj pracę',
				heading: 'Zaplanuj pracę z pytaniami!',
				tip: 'Plan pracy pomoże Ci określić tempo, którym spokojnie rozwiążesz wszystkie pytania!',
			},
			heading: 'Plan pracy',
		},
		stats: {
			error: 'Ups... Niestety, nie udało nam się załadować Twoich statystyk... Spróbujesz jeszcze raz? Jeśli problem będzie się powtarzał, daj nam znać w zakładce Pomoc > Pomoc techniczna. Przepraszamy!',
			heading: 'Twoje statystyki',
			mockExam: 'Poniżej znajduje się lista Twoich wyników z próbnych egzaminów.',
			scores: 'Ilość rozwiązanych pytań i procent prawidłowych odpowiedzi',
		},
		ui: {
			deleteModal: {
				title: 'Czy aby na pewno?',
				text: `Po zatwierdzeniu, usuniemy wszystkie wyniki Twoich dotychczasowych podejść do pytań kontrolnych.
					Dotyczy to zarówno pytań rozwiązywanych w ramach lekcji, jak i pozostałych dostępnych w bazie pytań.
					Jedyne wyniki, które nie zostaną wyczyszczone, to wyniki próbnych egzaminów.`
			}

		}
	},
	filters: {
		activeFiltersReview: 'Aktywne filtry: {filters}',
		activeHeading: 'Aktywne filtry',
		allQuestions: 'Wszystkie pytania',
		search: 'Wyszukiwanie',
		autorefresh: 'Odświeżaj automatycznie',
		filteringResult: 'Pasujące pytania:',
		filteringResultFrom: 'z {totalCount} w bazie',
		items: {
			'correct': 'Rozwiązane poprawnie',
			'exams': 'Egzaminy',
			'incorrect': 'Rozwiązane błędnie',
			'subjects': 'Przedmioty i tematy',
			'planned': 'Zaplanowane na dziś',
			'all': 'Wszystkie zaplanowane',
			'resolution': 'Status',
			'unresolved': 'Nierozwiązane',
			'collection': 'Zapisane w Kolekcjach',
		},
		searchHeading: 'Wyszukaj po frazie',
		searchPlaceholder: 'Wpisz frazę...',
		searchButton: 'Szukaj',
		heading: 'Wybierz filtry',
		hide: 'Schowaj filtry',
		refresh: 'Odśwież',
		show: 'Pokaż filtry',
		submit: 'Wybierz pasujące pytania',
		preserveProgress: 'Zachowaj rozwiązane pytania z poprzedniego planu',
		preserveProgressTip: 'Jeżeli odznaczysz tę opcję, ilość rozwiązanych pytań w nowym planie będzie wynosiła 0.',
	},
	nav: {
		dashboard: 'Dashboard',
		planner: 'Zaplanuj pracę',
		solving: 'Rozwiązuj pytania',
		stats: 'Sprawdź statystyki',
		mockExam: 'Rozwiąż próbny LEK'
	},
	question: {
		edit: 'Edytuj pytanie',
		tags: 'Tagi',
	},
	plan: {
		change: 'Zmieniam plan',
		dontChange: 'Nie zmieniam planu',
		headings: {
			change: 'Zmień plan',
			create: 'Stwórz nowy plan',
			dates: '1. Ile masz czasu?',
			endDate: 'Koniec',
			questions: '3. Ile pytań chcesz rozwiązać?',
			slackDays: '2. Ile dni wolnych planujesz?',
			startDate: 'Start',
			summary: '4. Podsumowanie planu',
		},
		options: {
			all: 'Wszystkie pytania',
			custom: 'Własny zakres',
			unresolvedAndIncorrect: 'Nierozwiązane + rozwiązane błędnie',
			count: 'Pytań: {count}',
		},
		progress: {
			average: {
				congrats: ' - gratulacje!',
				greater: ' i jest większa lub równa planowanej ',
				is: 'Twoja dzienna średnia wynosi ',
				less: ' i jest mniejsza, niż planowana ',
				newAverage: {
					header: 'Aby zrealizować plan rozwiązuj ',
					closure: 'dziennie'
				}
			},
			currentPlan: 'Obecny plan',
			day: 'Dzień {day}',
			explain: 'Rozwiązane pytania',
			heading: 'Jak Ci idzie?',
		},
		solvePlanned: 'Rozwiązuj zaplanowane na dzisiaj',
		start: {
			heading: 'Zaczynasz {date}',
			tip: 'Obecny plan zakłada zrobienie {count} pytań w {days} dni, co daje średnio {average} pytań na dzień.',
		},
		submit: 'Akceptuję plan!',
		summaryAverage: 'pytań na dzień',
		summaryCount: 'pytań w',
		summaryDays: 'dni, daje średnio',
		summaryTip: 'poniżej 100: plan spokojny, 100 - 200: plan intensywny, powyżej 200: plan hardkor',
		tips: {
			endDate: 'Kliknij, aby zmienić końcową datę',
			slackDays: 'Wpisz ilość dni, w których nie planujesz przerabiać pytań',
			startDate: 'Kliknij, aby zmienić początkową datę',
		}
	},
	solving: {
		abort: 'Przerwij',
		activeQuestionTip: 'Kliknij ponownie na wybraną odpowiedź, aby potwierdzić ją i sprawdzić wynik. Następnie, możesz kliknąć dwa razy w dowolną odpowiedź, aby przejść do następnego pytania.',
		confirm: {
			no: 'Nie, przerywam',
			title: 'Może jednak chcesz dokończyć test?',
			unanswered: 'Pytań bez odpowiedzi: {count}',
			yes: 'Tak, chcę dokończyć!',
		},
		current: 'Pytanie {number} z ',
		hideAnswers: 'Ukryj rozwiązania',
		new: 'Nowy test',
		resolve: 'Sprawdź wyniki',
		results: {
			correct: 'Rozwiązane poprawnie',
			displayOnly: 'Wyświetl tylko:',
			incorrect: 'Rozwiązane błędnie',
			unanswered: 'Nierozwiązane',
		},
		score: 'Wynik:',
		setAsCurrent: 'Ustaw jako aktualne',
		showAnswers: 'Pokaż rozwiązania',
		tabs: {
			current: 'Aktualne pytanie ({current})',
			list: 'Lista pytań ({count})',
			test: 'Sprawdź się!',
		},
		test: {
			headers: {
				answered: 'Rozwiązanych: {answered}/{total}',
				count: 'Na ile pytań chcesz odpowiedzieć?',
				remaining: 'Pozostało:',
				time: 'Ile czasu chcesz poświęcić?',
			},
			preset: {
				time: 'Test potrwa {time} minut.',
			},
			start: 'Rozpocznij test!',
			title: 'Rozwiąż zestaw ułożony na podstawie aktywnych filtrów...',
		},
		unanswered: {
			all: 'Pokaż wszystkie pytania',
			filter: 'Pokaż tylko nierozwiązane',
		},
		withNumber: 'Pytanie {number}',
	},
	zeroState: 'Oho, nie mamy pasujących pytań... Spróbujesz wyłączyć niektóre z filtrów?',
}
