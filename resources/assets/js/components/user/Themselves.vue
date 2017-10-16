<template>
	<div class="wnl-app-layout">
		<div class="wnl-middle wnl-app-layout-main" :class="{'full-width': isMobileProfile, 'mobile-main': isMobileProfile}">
			<wnl-user-profile v-if="responseCondition" :profile="profile.data" :commentsCompetency="commentsCompetency" :qnaQuestionsCompetency="qnaQuestionsCompetency" :qnaAnswersCompetency="qnaAnswersCompetency" :readOnly="readOnly"></wnl-user-profile>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.myself-sidenav
		flex: 1

	.wnl-sidenav
		flex: 1
		padding: 7px 0

		&.mobile
			padding: 0

	.mobile-main
		overflow-y: auto
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	import MainNav from 'js/components/MainNav'
	import UserProfile from 'js/components/user/UserProfile'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import { isProduction, getApiUrl } from 'js/utils/env'

	export default {
		name: 'Themselves',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-user-profile': UserProfile,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: ['view'],
		data() {
			return {
				param: this.$route.params.userId,
				profile: {},
				commentsCompetency: {},
				qnaQuestionsCompetency: {},
				qnaAnswersCompetency: {},
				readOnly: true
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile']),
			isProduction() {
				return isProduction()
			},
			items() {
				let items = [

				]

				return items
			},
			responseCondition() {
				return !_.isEmpty(this.profile)
			},

		},
		methods: {
			...mapActions(['killChat']),
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
		mounted() {
			const dataForComments = {
				query: {
					where: [[ 'user_id', this.param ]]
				},
				include: 'context'
			}
			const dataForQnaQuestions = {
				query: {
					where: [[ 'user_id', this.param ]]
				},
				include: 'profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles'
			}
			const dataForQnaAnswers = {
				query: {
					whereHas: {
						answers: {
							where: [[ 'user_id', this.param ]]
						}
					}
				},
				include: 'profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles'
			}
			const promisedProfile = axios.get(getApiUrl(`users/${this.param}/profile`))
			const promisedCommentsCompetency = axios.post(getApiUrl(`comments/.search`), dataForComments)
			const promisedQnaQuestionsCompetency = axios.post(getApiUrl(`qna_questions/.search`), dataForQnaQuestions)
			const promisedQnaAnswersCompetency = axios.post(getApiUrl(`qna_questions/.search`), dataForQnaAnswers)

			Promise.all([promisedProfile, promisedCommentsCompetency, promisedQnaQuestionsCompetency, promisedQnaAnswersCompetency])
			.then(([profile, commentsCompetency, qnaQuestionsCompetency, qnaAnswersCompetency]) => {
				this.profile = profile
				this.commentsCompetency = commentsCompetency
				this.qnaQuestionsCompetency = qnaQuestionsCompetency
				this.qnaAnswersCompetency = qnaAnswersCompetency
			})
			.catch(exception => $wnl.logger.capture(exception))
		},
	}
</script>
