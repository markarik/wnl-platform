export const tasks = {
	empty: 'Gratulacje! Wszystkie powiadomienia sprawdzone!',
	quickFilters: {
		title: 'Szybkie filtry',
		filters: {
			unassigned: 'Nieprzypisane',
			my: 'Przypisane do mnie',
			notDone: 'Niezrobione'
		}
	},
	filters: {
		byType: {
			title: 'Filtrowanie Po Typie',
			slide: 'Slajdy',
			qna: 'Dyskujse (QnA)',
			quiz_question: 'Pytania Kontrolne',
		},
		byLesson: {
			title: 'Filtrowanie Po Przedmiocie',
		}
	},
	sorting: {
		title: 'Sortowanie',
		options: {
			byCreatedAt: 'Po dacie utworzenia',
			byUpdatedAt: 'Po dacie modyfikacji'
		}
	},
	task: {
		defaultTitle: 'Brak Tytułu',
		fields: {
			status: 'Status',
			assignee: 'Ogarniacz',
			eventsCount: 'Liczba wątków',
			createdAt: 'Utworzono',
			updatedAt: 'Zmodyfikowano'
		},
		status: {
			open: 'Do wzięcia',
			inProgress: 'Robi się',
			done: 'Pozamiatane',
			reopen: 'Otwarty ponownie',
			unknown: 'Status nieznany'
		},
		action: {
			go: 'Jedziesz szwagier',
		}
	}
};
