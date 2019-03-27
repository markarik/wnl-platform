<template>
	<wnl-modal @closeModal="$emit('closeModal')" v-if="visible">
		<div class="satisfaction-guarantee-modal normal">
			<h4 class="margin bottom">{{title}}</h4>
			<p class="strong margin bottom">
				⚠️ Ta akcja może wypłynąć na Twoją Gwarancję Satysfakcji.
			</p>
			<p class="margin bottom">Jeśli planujesz rozpoczęcie nauki od nowa pamiętaj, że należy spełnić konkretne kryteria, aby Gwarancja Satysfakcji była przyznana
			</p>
			<p class="margin bottom text-dimmed satisfaction-guarantee-modal__extra-info">
				<i class="fa fa-info-circle"/>
				<span>
					Wszystkie informacje na temat Gwarancji Satysfakcji znajdziesz w
					<a href="/app/help/guarantee" target="_blank">POMOC > Gwarancja Satysfakcji.</a>
				</span>
			</p>
			<p class="satisfaction-guarantee-modal__actions">
				<button class="button is-outlined" @click="$emit('closeModal')">Zamknij</button>
				<button class="button is-primary" @click="$emit('submit')">Rozumiem</button>
			</p>
		</div>
	</wnl-modal>
</template>

<script>
import WnlModal from 'js/components/global/Modal';

export default {
	components: {WnlModal},
	props: {
		visible: {
			type: Boolean,
			default: false
		},
		title: {
			type: String,
			required: true
		}
	}
};
</script>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.satisfaction-guarantee-modal
		text-align: center
		display: flex
		flex-direction: column
		max-width: 480px
		margin: 0 auto

		&__extra-info
			border-top: $border-light-gray
			padding-top: $margin-base
			margin-bottom: $margin-big

			.fa
				line-height: inherit
				margin-right: $margin-small
			a
				white-space: nowrap
				color: inherit
				text-decoration: underline
		&__actions
			.button
				margin-right: $margin-big
				&:last-child
					margin-right: 0

</style>
