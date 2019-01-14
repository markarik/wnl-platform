<template>
	<div>
		<nav class="tabs is-uppercase small">
			<ul>
				<li class="is-active">
					<a>
						<span class="icon is-small"><i class="fa fa-plus" aria-hidden="true"></i></span>
						<span>Dodaj</span>
					</a>
				</li>
				<li>
					<a>
						<span class="icon is-small"><i class="fa fa-pencil" aria-hidden="true"></i></span>
						<span>Edytuj</span>
					</a>
				</li>
				<li>
					<a>
						<span class="icon is-small"><i class="fa fa-compress" aria-hidden="true"></i></span>
						<span>Połącz</span>
					</a>
				</li>
				<li>
					<a>
						<span class="icon is-small"><i class="fa fa-close" aria-hidden="true"></i></span>
						<span>Usuń</span>
					</a>
				</li>
			</ul>
		</nav>
		<div>
			<h5 class="title is-5 is-uppercase"><strong>Nadrzędne pojęcie</strong></h5>
			<span class="info">Pozostaw puste, aby dodać pojęcie na 1. poziomie taksonomii.</span>
			<input class="input" />
			<h5 class="title is-5 is-uppercase"><strong>Tag źródłowy</strong></h5>
			<span class="info">Wybierz tag, na podstawie którego chcesz utworzyć pojęcie, lub utwórz nowy.</span>
			<input class="input" />
			<h5 class="title is-5 is-uppercase"><strong>Notatka</strong></h5>
			<span class="info">(Opcjonalnie) Dodaj notatkę niewidoczną dla użytkowników.</span>
			<textarea class="textarea" />
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.info
		color: #aaa
</style>


<script>
export default {
	props: {
	},
};
</script>
