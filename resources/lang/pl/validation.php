<?php

return [

	"accepted"             => "Potrzebujemy Twojej zgody!", //yes, 1, true
	"active_url"           => "To nie wygląda jest prawidłowy adres URL...",
	"after"                => "Ta wartość musi być datą późniejszą niż :date.",
	"alpha"                => "To pole może zawierać tylko litery",
	"alpha_dash"           => "To pole może zawierać tylko litery, cyfry i podkreślenia.",
	"alpha_num"            => "To pole może zawierać tylko litery i cyfry.",
	"alpha_spaces"         => "To pole może zawierać tylko litery, spacje oraz myślniki.",
	"alpha_comas"          => "To pole może zawierać tylko litery, liczby, przecinki, kropki, spacje, dwukropki, średniki, nawiasy okrągłe, cudzysłów oraz myślniki",
	"array"                => "To pole musi być tablicą.",
	"before"               => "To pole musi być datą wcześniejszą niż :date.",
	"between"              => [
		"numeric" => "To pole musi być wartością pomiędzy :min i :max.",
		"file"    => "To pole musi mieć pomiędzy :min a :max kilobajtów.",
		"string"  => "To pole musi mieć pomiędzy :min a :max znaków.",
		"array"   => "To pole musi mieć pomiędzy :min a :max pozycji.",
	],
	"boolean"              => "To pole musi być true lub false",
	"confirmed"            => "Niestety, to pole nie zgadza się ze swoim potwierdzeniem.",
	"date"                 => "To pole nie jest prawidłową datą.",
	"date_format"          => "To pole nie zgadza się z formatem :format.",
	"different"            => "Te pola muszą być różne.",
	"digits"               => "To pole musi mieć :digits cyfr.",
	"digits_between"       => "To pole musi mieć pomiędzy :min a :max cyfr.",
	"email"                => "To pole musi być prawdziwym adresem e-mail.",
	"exists"               => "Wybrana wartość nie istnieje.",
	"image"                => "To pole  musi być obrazkiem.",
	"in"                   => "Wybrany jest nieprawidłowy.",
	"integer"              => "To pole musi być liczbą.",
	"ip"                   => "To pole musi być poprawnym adresem IP.",
	"max"                  => [
		"numeric" => "To pole nie może być większe niż :max.",
		"file"    => "To pole nie może być większe niż :max kilobajtów.",
		"string"  => "To pole nie może być dłuższe niż :max znaków.",
		"array"   => "To pole nie może mieć więcej niż :max pozycji.",
	],
	"mimes"                => "To pole musi być plikiem typu: :values.",
	"min"                  => [
		"numeric" => "To pole musi większy lub równy :min.",
		"file"    => "To pole musi mieć co najmniej :min kilobajtów.",
		"string"  => "To pole musi mieć co najmniej :min znaków.",
		"array"   => "To pole musi mieć co najmniej :min pozycji.",
	],
	"not_in"               => "Wybrana wartość jest nieprawidłowa.",
	"numeric"              => "To pole musi być liczbą.",
	"regex"                => "Ten format jest nieprawidłowy",
	"required"             => "To pole jest polem wymaganym.",
	"required_if"          => "To pole jest polem wymaganym, gdy :other ma wartość :value.",
	"required_with"        => "To pole jest polem wymaganym, gdy :values jest zdefiniowane.",
	"required_with_all"    => "To pole jest polem wymaganym, gdy :values są zdefiniowane.",
	"required_without"     => "To pole jest polem wymaganym, gdy :values nie jest zdefiniowane.",
	"required_without_all" => "To pole jest polem wymaganym, gdy żadne z :values nie są zdefiniowane.",
	"same"                 => "Te pola muszą być takie same.",
	"size"                 => [
		"numeric" => "To pole musi być mniejsze, niż :size.",
		"file"    => "Ten plik nie może mieć więcej, niż :size.",
		"string"  => "To pole musi mieć :size znaków.",
		"array"   => "To pole musi zawierać :size pozycji.",
	],
	"unique"               => "Ta wartość jest już zajęta.",
	"url"                  => "Ten adres URL jest nieprawidłowy.",
	"timezone"             => "Ta wartość musi być prawidłową strefą czasową.",
	"morph_exists"         => "Wybrana wartość nie istnieje.",
	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/
	'custom' => [
		'attribute-name' => [
			'rule-name' => 'custom-message',
		],
	],
	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/
	'attributes' => [
		'username' => 'nazwa użytkownika'
	],

];
