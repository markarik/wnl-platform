<template>
	<div class="wnl-app-layout">
		<div class="wnl-middle wnl-app-layout-main" :class="{'full-width': isMobileProfile, 'mobile-main': isMobileProfile}">
			<wnl-user-profile v-if="responseCondition" :response="response" :commentsCompetency="commentsCompetency"></wnl-user-profile>
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
				response: {},
				commentsCompetency: {},
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
				return !_.isEmpty(this.response)
			}
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
			const data = {
				query: {
					where: [[ 'user_id', this.param ]]
				},
				include: 'context'
			}
			const promisedProfile = axios.get(getApiUrl(`users/${this.param}/profile`))
			const promisedCommentsCompetency = axios.post(getApiUrl(`comments/.search`), data)
			// const promisedQnaAnswersCompetency = axios.post(getApiUrl(`qna-questions/.search`))
			
			Promise.all([promisedProfile, promisedCommentsCompetency])
			.then(([profile, competency]) => {
				this.response = profile
				this.commentsCompetency = competency
			})
			.catch(exception => $wnl.logger.capture(exception))
		},
	}
</script>
