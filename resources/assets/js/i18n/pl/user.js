export const user = {
    myProfile: {
        publicProfile: 'Profil publiczny',
        previewYourProfileButtonEnabled: 'Zobacz swój profil',
        previewYourProfileButtonDisabled: 'Zapisz, aby zobaczyć profil',
        editYourProfile: 'Edytuj profil',
        displayName: 'Podpisuj mnie jako...',
        displayNamePlaceholder: 'Jak chcesz być przedstawiany na platformie?',
        city: 'Miasto, w którym mieszkasz',
        university: 'Twój uniwersytet',
        specialization: 'Twoja obecna lub przyszła specjalizacja',
        help: 'W czym możesz pomóc innym?',
        helpPlaceholder: 'Np. kardiologia, psychiatria, chirurgia naczyniowa...',
        interests: 'Twoje zainteresowania',
        learning_location: 'Miasto, w którym obecnie się uczysz',
        about: 'Parę słów o Tobie',
        aboutPlaceholder: 'Np. Twój ulubiony cytat',
        public_email: 'Adres e-mail',
    },
    userProfile: {
        editProfileButton: 'Edytuj profil',
        textLoader: 'Szukamy człowieka...',
        helpDefaultDescription: 'Wyedytuj profil i dziel się wiedzą z innymi :)',
        cityDefaultDescription: 'Wyedytuj profil, powiedz nam skąd jesteś :)',
        helpTitle: 'W czym mogę pomóc?',
        bestQuestions: 'Najlepsze Pytania',
        bestAnswers: 'Najlepsze Odpowiedzi',
        about: 'Informacje o uczestniku',
        showContext: 'Pokaż kontekst'
    },
    userAboutLabels: {
        city: 'Miasto, w którym mieszkam',
        university: 'Mój uniwersytet',
        specialization: 'Moja specjalizacja',
        interests: 'Moje zainteresowania',
        about: 'Parę słów o mnie',
        learning_location: 'Miasto, w którym obecnie się uczę'
    },
    userProfileModal: {
        redirectToProfile: 'Zobacz pełen profil',
        sendMessage: 'Wyślij wiadomość',
    },
	lessonsAvailabilities: {
		alertSuccess: 'Udalo się zmienić datę! :)',
		alertError: 'Nie udało się zmienić daty, spróbuj jeszcze raz!',
	},
    progressReset: {
        header: 'Usuwanie postępu',
        progressHeader: 'Postęp w nauce',
        questionsHeader: 'Wyniki pytań kontrolnych',
        collectionsHeader: 'Kolekcje',
        progressButton: 'Wyczyść postęp w nauce',
        questionsButton: 'Wyczyść wszystkie wyniki',
        collectionsButton: 'Wyczyść kolekcje',
		progressWarning: `Poniższy przycisk pozwoli Ci na usunięcie
        dotychczasowego postępu we wszystkich lekcjach na kursie. Po usunięciu
        postępu WSZYSTKIE lekcje będą oznaczone jako niezrobione, a Twój
        dotychczasowy postęp zostanie utracony. Twój czas na platformie NIE
        zostanie zresetowany, podobnie jak postęp w obrębie bazy pytań i pytań
        kontrolnych w lekcjach.`,
		questionsWarning: `Po zatwierdzeniu, usuniemy wszystkie wyniki
        Twoich dotychczasowych podejść do pytań kontrolnych. Dotyczy to zarówno
        pytań rozwiązywanych w ramach lekcji, jak i pozostałych dostępnych w
        bazie pytań. Jedyne wyniki, które nie zostaną wyczyszczone, to wyniki
        próbnych egzaminów.`,
		collectionsWarning: `Naciśnięcie poniższego przycisku spowoduje
        usunięcie wszystkiego z Twojej kolekcji, czyli
        między innymi zapisanych slajdów, pytań kontrolnych, pytań i odpowiedzi.`,
		progressConfirmation: 'Czy na pewno chcesz usunąć cały postęp w nauce?',
		questionsConfirmation: 'Czy na pewno chcesz usunąć wyniki pytań kontrolnych?',
		collectionsConfirmation: 'Czy na pewno chcesz wyczyścić swoją kolekcję?',
		alertError: 'Ups... Coś poszło nie tak. Spróbujesz jesze raz?',
		alertSuccess: `OK, wszystko poszło zgodnie z planem. Jeśli to była
		spektakularna pomyłka, daj nam znać. Jest szansa, że uda się odzyskać
		usunięte informacje ;)`,
    },
	passwordResetHeader: 'Zmiana hasła',
	deleteAccount: {
		header: 'Usuń konto',
		warningHeader: 'Usunięcie konta',
		warning: `<strong>Po wybraniu tej opcji Twoje konto zostanie usunięte. Jeżeli chcesz to zrobić, po kliknięciu pojawi się dymek “Czy na pewno chcesz usunąć swoje konto?”. </strong><br><br> <strong>Twoje konto zostanie usunięte</strong>, oznacza to, że Twoje dane na platformie nie będą widoczne - zamiast imienia i nazwiska pojawi się informacja “Konto usunięte”. Zostanie zachowana treść dodanych przez Ciebie komentarzy i pytań, natomiast Twoje dane nie będą widoczne dla innych użytkowników. <br><br><strong>Uwaga! Twoje dane pozostaną w naszej bazie oraz w dokumentach księgowych przez 5 lat, ze względu na obowiązek przechowywania dokumentacji podatkowej.</strong> W razie roszczeń z Twojej strony lub kontroli podatkowej musimy mieć możliwość ponownego wygenerowania dokumentów sprzedażowych. Natomiast wszystkie dane, które nie są potrzebne do zrealizowania powyższego obowiązku (w tym e-mail, telefon) zostaną usunięte z naszej bazy oraz wszystkich list mailingowych.<br><br> <strong>Po zatwierdzeniu usunięcia nastąpi wylogowanie.</strong>`,
		confirmationHeader: 'Usuń konto',
		confirmationWarning: 'Aby potwierdzić usunięcie konta - wprowadź swoje hasło. Po zatwierdzeniu nastąpi wylogowanie.',
	},
    address: {
        address: 'Adres',
        recipient: 'Osoba odbierjąca przesyłkę',
        street: 'Ulica',
        zip: 'Kod pocztowy',
        city: 'Miasto',
        phone: 'Telefon'
    },
    personalData: {
        header: {
            explanationHeader: 'Twoje wrażliwe dane',
            explanation: 'Informacje, które tu zostawisz będą wykorzystane przez nas do obsługi Twojego zamówienia. Dane nie będą widoczne publicznie. Więcej informacji na ten temat znajdziesz tutaj: link do polityki prywatności.'
        },
        identityNumber: {
            header: 'Numer identyfikacyjny',
            explanation: 'W związku z podjęciem współpracy z CEM, prosimy Cię o uzupełnienie numeru PESEL na Twoim profilu! Te dane nie będą widoczne publicznie, a posłużą nam do celów statystycznych. Twoja pomoc pozwoli nam rozwijać i doskonalić kurs!',
        }
    }
}
