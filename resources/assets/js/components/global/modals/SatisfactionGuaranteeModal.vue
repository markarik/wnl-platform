<template>
	<wnl-modal @closeModal="$emit('closeModal')" v-if="visible">
		<div class="satisfaction-guarantee-modal normal">
			<h4 class="margin bottom">
				<slot name="title"></slot>
			</h4>
			<p v-if="displayHeadline" class="strong margin bottom">{{$t('ui.satisfactionGuarantee.headline')}}</p>
			<p class="margin bottom">
				<slot name="body">{{$t('ui.satisfactionGuarantee.body')}}</slot>
			</p>
			<p class="margin bottom text-dimmed satisfaction-guarantee-modal__extra-info">
				<i class="fa fa-info-circle"/>
				<slot name="footer">
					<span v-html="$t('ui.satisfactionGuarantee.note', {url: $router.resolve({name: 'satisfaction-guarantee'}).href})"></span>
				</slot>
			</p>
			<div class="satisfaction-guarantee-modal__actions">
				<button class="button" @click="$emit('closeModal')">
					<slot name="close">{{$t('ui.satisfactionGuarantee.close')}}</slot>
				</button>
				<button class="button is-primary" @click="$emit('submit')">
					<slot name="submit">{{$t('ui.satisfactionGuarantee.accept')}}</slot>
				</button>
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
			display: flex
			flex-wrap: wrap
			justify-content: center

			.button
				flex: 100%
				margin-bottom: $margin-big
				&:last-child
					margin-bottom: 0

			@media #{$media-query-tablet}
				flex-wrap: nowrap
				.button
					flex: 50%
					margin: 0 $margin-base 0 0

					&:last-child
						margin: 0
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
		displayHeadline: {
			type: Boolean,
			default: true
		}
	}
};
</script>
