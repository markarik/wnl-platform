<template>
	<div class="wnl-app-layout layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div class="single-question" v-if="!isOverlayVisible">
			<div class="question-container" v-if="isLoaded">
				<div class="question-header">
					<span class="question-title">{{title}}</span>
					<a class="question-back" @click="goBack">
						<span class="icon is-small">
							<i class="fa fa-angle-left"></i>
						</span>
						{{$t('quiz.single.back')}}
					</a>
				</div>
				<div v-if="hasError" class="notification">
					{{$t('quiz.single.error', {id: this.id})}} <wnl-emoji name="disappointed"/>
				</div>
				<wnl-quiz-widget v-else :isSingle="true"/>
			</div>
			<wnl-text-loader v-else/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.layout
		justify-content: space-between

	.question-header
		align-items: center
		display: flex
		justify-content: space-between
		margin-bottom: $margin-base

		.question-title
			font-size: $font-size-plus-1
			font-weight: $font-weight-bold

		.question-back

			.is-active
				font-weight: $font-weight-regular

	.single-question
		display: flex
		justify-content: center
		padding: $margin-base 0
		width: 100%

	.question-container
		max-width: 100vw
		padding: 0 $margin-base
		width: 600px

	.wnl-quiz-widget
		width: 100%
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	import MainNav from 'js/components/MainNav'
	import QuizWidget from 'js/components/quiz/QuizWidget'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	export default {
		name: 'SingleQuestion',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-quiz-widget': QuizWidget,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: {
			id: {
				required: true,
				type: String|Number,
			}
		},
		data() {
			return {
				hasError: false,
			}
		},
		computed: {
			...mapGetters(['isOverlayVisible', 'isSidenavVisible', 'isSidenavMounted']),
			...mapGetters('quiz', ['isLoaded']),
			title() {
				return this.hasError ? this.$t('quiz.single.errorTitle') : this.$t('quiz.single.title', {id: this.id})
			},
		},
		methods: {
			...mapActions('quiz', ['fetchSingleQuestion']),
			goBack() {
				this.$router.go(-1)
			},
		},
		mounted() {
			if (!this.id) {
				this.hasError = true
				return
			}
			this.fetchSingleQuestion(this.id)
				.then(response => {
					if (!response.data) this.hasError = true
				})
				.catch(error => {
					this.hasError = true
				})
		}
	}
</script>
