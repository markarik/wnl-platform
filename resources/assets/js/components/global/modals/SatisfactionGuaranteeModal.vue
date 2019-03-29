<template>
	<wnl-modal @closeModal="$emit('closeModal')" v-if="visible">
		<div class="satisfaction-guarantee-modal normal">
			<h4 class="margin bottom">{{title}}</h4>
			<p class="strong margin bottom">{{$t('ui.satisfactionGuarantee.headline')}}</p>
			<p class="margin bottom">{{$t('ui.satisfactionGuarantee.body')}}</p>
			<p class="margin bottom text-dimmed satisfaction-guarantee-modal__extra-info">
				<i class="fa fa-info-circle"/>
				<span v-html="$t('ui.satisfactionGuarantee.note', {url: $router.resolve({name: 'satisfaction-guarantee'}).href})"></span>
			</p>
			<div class="satisfaction-guarantee-modal__actions">
				<button class="button" @click="$emit('closeModal')">{{$t('ui.satisfactionGuarantee.close')}}</button>
				<button class="button is-primary" @click="$emit('submit')">{{$t('ui.satisfactionGuarantee.accept')}}</button>
			</div>
		</div>
	</wnl-modal>
</template>

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
			/deep/ a
				white-space: nowrap
				color: inherit
				text-decoration: underline

		&__actions
			.button
				margin-right: $margin-big
				&:last-child
					margin-right: 0
</style>

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
