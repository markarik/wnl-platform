<template>
	<div class="slide-edit-link">
		<a
			class="small"
			target="_blank"
			:href="`/admin/app/slides/edit?screenId=${screenId}&slideId=${currentSlideId}`"
		>
			{{$t('admin.editSlide')}}
			<span class="icon is-small">
				<i class="fa fa-pencil" />
			</span>
		</a>
	</div>
</template>

<style lang="sass">

</style>

<script>
export default {
	name: 'EditSlideButton',
	props: {
		currentSlideId: Number,
		screenId: Number,
	}
};
</script>
