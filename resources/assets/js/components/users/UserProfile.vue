<template lang="html">
		<div class="wnl-user-profile" :class="{mobile: isMobileProfile}">
			<div class="text-loader" v-if="isLoading">
				<wnl-text-loader>
					{{ $t('user.userProfile.textLoader') }}
				</wnl-text-loader>
			</div>

			<div class="user-profile" :class="isMobile"  v-if="!isLoading && responseCondition">
				<div class="user-content" :class="avatarClass">
					<wnl-avatar class="user-avatar image" size="extralarge"
						:fullName="fullName"
						:url="profile.avatar"
					></wnl-avatar>
					<div class="user-info-header">
						<div class="user-info-header-edit">
							<span v-if="currentUserProfile">
								<router-link :to="{ name: 'my-profile' }">
									<a class="edit-profile button is-primary is-outlined is-small">{{ $t('user.userProfile.editProfileButton') }}</a>
								</router-link>
							</span>
							<span class="user-info-header-names">
								<p class="fullname-title">{{ profile.real_first_name }} {{ profile.real_last_name }}</p>
								<p class="chosen-fullname-title">{{ profileFirstNameToPrint }} {{ profileLastNameToPrint }}</p>
							</span>
						</div>
						<span v-if="cityToDisplay" class="user-info-city">
							<span class="icon is-small">
								<i class="fa fa-map-marker"></i>
							</span>
							<span class="city-title">{{ cityToDisplay }}</span>
						</span>
						<span v-if="helpToDisplay" class="user-info-help">
							<span class="help-title">W czym mogę pomóc?</span>
							<div class="notification">
								<span class="user-help">{{ helpToDisplay }}</span>
							</div>
						</span>
					</div>
				</div>

				<div class="user-activity-content">
					<div class="wnl-activity-meter" v-for="(activity, index) in activityMeterArray" :key="index">
						<div class="activity-stat">
							<span class="icon is-large">
								<i :class="activity.iconClassToUse"></i>
							</span>
							<span class="activity-meter-number">{{ activity.statistic }}</span>
						</div>
						<p class="activity-title">{{ activity.name }}</p>
					</div>
				</div>

				<div class="top-activities" v-if="ifAnyQuestions || ifAnyAnswers">
					<wnl-qna
						:isUserProfileClass="isUserProfileClass"
						:numbersDisabled="true"
						:title="$t('user.userProfile.bestQuestions')"
						:icon="iconForQuestions"
						v-if="ifAnyQuestions"
						:sortingEnabled="false"
						:readOnly="true"
						:reactionsDisabled="true"
						:passedQuestions="sortedQuestions"
						:showContext="true"
					></wnl-qna>
					<wnl-qna
						:isUserProfileClass="isUserProfileClass"
						:numbersDisabled="true"
						:icon="iconForAnswers"
						:title="$t('user.userProfile.bestAnswers')"
						v-if="ifAnyAnswers"
						:sortingEnabled="false"
						:readOnly="true"
						:reactionsDisabled="true"
						:passedQuestions="sortedQuestionsForAnswers"
						:showContext="true"
					></wnl-qna>
				</div>
			</div>
		</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-user-profile
		width: 100%

		.text-loader
			position: absolute
			z-index: $z-index-navbar
			width: 100vw
			height: 100%
			background-color: $color-white
			top: 0
			left: 0

		.collections-controls
			align-items: center
			display: flex
			flex-wrap: wrap
			margin-bottom: $margin-base

		.is-mobile-avatar
			flex-direction: column
			align-items: center

		.is-desktop-avatar
			justify-content: flex-start

		.user-content
			display: flex
			margin-bottom: $margin-base
			border-bottom: $border-light-gray
			padding-bottom: $margin-base
			.user-avatar
				margin-right: 1vw
				margin-top: $margin-tiny
				margin-bottom: $margin-small
			.user-info-header
				display: flex
				flex-direction: column
				width: 100%
				.user-info-header-edit
					display: flex
					flex-direction: row-reverse
					color: $color-ocean-blue
					.edit-profile
						margin-bottom: $margin-small
				.user-info-header-names
					flex-grow: 10
					.fullname-title
						color: $color-ocean-blue
						font-size: $font-size-plus-5
						font-weight: $font-weight-bold
						margin-bottom: $margin-small
						line-height: $line-height-none
					.chosen-fullname-title
						color: $color-ocean-blue-opacity
						font-size: $font-size-plus-2
						font-weight: $font-weight-regular
						margin-bottom: $margin-small
				.user-info-city
					align-items: center
					color: $color-gray-dimmed
					display: flex
					margin-bottom: $margin-base
					.city-title
						font-size: $font-size-plus-1
						font-weight: $font-weight-regular
						margin-left: $margin-small
				.user-info-help
					display: inline-block
					max-width: 100%
					word-wrap: break-all
					.help-title
						font-size: $font-size-minus-1
						text-transform: uppercase
					.notification
						border-radius: $border-radius-small
						margin-top: $margin-tiny
						width: 100%
						.user-help
							font-size: $font-size-plus-1
							font-weight: $font-weight-regular
							display: inline-block
							max-width: 100%
							word-wrap: break-all

		.user-activity-content
			align-items: center
			display: flex
			flex-direction: row
			justify-content: space-around
			margin-bottom: $margin-big
			border-bottom: $border-light-gray
			padding-bottom: $margin-base
			.activity-stat
				align-items: center
				color: $color-dark-blue
				display: flex
				flex-direction: row
				font-size: $font-size-plus-6
				font-weight: $font-weight-black
				margin-bottom: $margin-medium
				.activity-meter-number
					top: -$margin-small
				.icon
					color: $color-dark-blue-opacity
					margin-right: $margin-base
			.activity-title
				color: $color-gray-dimmed
				letter-spacing: 1px
				text-align: center
				text-transform: uppercase

		div.user-profile
			&.is-mobile
				.user-info-header
					align-items: center
					text-align: center
					.user-info-header-edit
						flex-direction: column
						justify-content: center
						align-items: center
				.activity-stat
					font-size: $font-size-plus-4
					justify-content: center
					.icon
						margin-right: $margin-tiny

		.user-section-header
			display: flex
			justify-content: flex-start

		.top-activities
			margin-bottom: $margin-humongous

</style>

<script>
import _ from 'lodash'
import {
	mapActions,
	mapGetters
} from 'vuex'
import {
	getApiUrl
} from 'js/utils/env'
import Avatar from 'js/components/global/Avatar'
import Qna from 'js/components/qna/Qna'

export default {
	name: 'UserProfile',
	components: {
		'wnl-avatar': Avatar,
		'wnl-qna': Qna,
	},
	data() {
		return {
			isLoading: true,
			isUserProfileClass: 'is-user-profile',
			iconForQuestions: 'fa fa-question-circle-o',
			iconForAnswers: 'fa fa-comment-o',
			id: this.$route.params.userId,
			profile: {},
			allAnswers: {},
			allQuestions: {},
			allQuestionsForAnswers: {},
		}
	},
	computed: {
		...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile', 'isTouchScreen']),
		...mapGetters(['currentUserId']),
		...mapGetters('qna', ['getSortedQuestions']),
		responseCondition() {
			return !_.isEmpty(this.profile)
		},
		isMobile() {
			return this.isMobileProfile ? 'is-mobile' : ''
		},
		isSinglePanelView() {
			return this.isTouchScreen
		},
		avatarClass() {
			return this.isMobileProfile ? 'is-mobile-avatar' : 'is-desktop-avatar'
		},
		currentUserProfile() {
			return this.id == this.currentUserId
		},
		fullName() {
			return this.profile.full_name
		},
		profileFirstNameToPrint() {
			return this.profile.real_first_name === this.profile.first_name ? null : this.profile.first_name
		},
		profileLastNameToPrint() {
			return this.profile.real_last_name === this.profile.last_name ? null : this.profile.last_name
		},
		helpToDisplay() {
			return this.currentUserProfile ? this.profile.help || this.$t('user.userProfile.helpDefaultDescription') : this.profile.help || false
		},
		cityToDisplay() {
			return this.currentUserProfile ? this.profile.city || this.$t('user.userProfile.cityDefaultDescription') : this.profile.city || false
		},
		howManyComments() {
			return this.allComments.length
		},
		howManyQuestions() {
			return Object.values(this.allQuestions).length
		},
		howManyAnswers() {
			return Object.values(this.allAnswers).length
		},
		activityMeterArray() {
			return [{
					statistic: this.howManyComments,
					name: 'Komentarze',
					iconClassToUse: 'fa fa-comments-o'
				},
				{
					statistic: this.howManyQuestions,
					name: 'Pytania',
					iconClassToUse: 'fa fa-question-circle-o'
				},
				{
					statistic: this.howManyAnswers,
					name: 'Odpowiedzi',
					iconClassToUse: 'fa fa-comment-o'
				}
			]
		},
		ifAnyQuestions() {
			return this.howManyQuestions !== 0
		},
		ifAnyAnswers() {
			return this.howManyAnswers !== 0
		},
		isQuestionsPanelVisible() {
			return this.isPanelActive('questions')
		},
		isAnswersPanelVisible() {
			return this.isPanelActive('answers')
		},
		sortedQuestionsForAnswers() {
			const questionsIds = this.sortedAnswers.map((answer) => answer.qna_questions)

			const sortedQuestionsForAnswers = []

			questionsIds.forEach((id, index) => {
				const value = Object.values(this.allQuestionsForAnswers).find((question) => {
					return question.id === id
				})
				if (sortedQuestionsForAnswers.indexOf(value) === -1) {
					sortedQuestionsForAnswers.push(value)
				}
			})
			return sortedQuestionsForAnswers
		},
		sortedAnswers() {
			const sortedAnswers =  Object.values(this.allAnswers).sort((a, b) => {
				return b.upvote.count - a.upvote.count
			})
			return sortedAnswers.slice(0,2)
		},
		sortedQuestions() {
			const sortedQuestions = Object.values(this.allQuestions).sort((a, b) => {
				return b.upvote.count - a.upvote.count
			})
			const bestQuestions = sortedQuestions.slice(0,2)
			return this.getSortedQuestions('votes', bestQuestions)
		},
	},
	methods: {
		...mapActions('qna', ['setUserQnaQuestions', 'setConfig']),
		togglePanel(panel) {
			return this.activePanels = [panel]
		},
		isPanelActive(panel) {
			if (this.isSinglePanelView) {
				return this.activePanels[0] === panel
			}
			return this.activePanels.includes(panel)
		},
		loadData() {
			if (!this.$route.params.userId) {
				this.$router.push({
					...this.$route,
					params: {
						...this.$route.params,
						userId: this.currentUserId
					}
				})
			}
			const userId = this.$route.params.userId
			const dataForComments = {
				query: {
					where: [
						['user_id', userId]
					]
				}
			}
			const dataForQnaQuestions = {
				query: {
					where: [
						['user_id', userId]
					]
				},
				include: 'context,profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles'
			}
			const dataForQnaAnswers = {
				query: {
					where: [
						['user_id', userId]
					]
				},
				include: 'reactions'
			}
			const promisedProfile = axios.get(getApiUrl(`users/${userId}/profile`))
			const promisedAllComments = axios.post(getApiUrl(`comments/.count`), dataForComments)
			const promisedQnaQuestionsCompetency = axios.post(getApiUrl(`qna_questions/.search`), dataForQnaQuestions)
			const promisedAllAnswers = axios.post(getApiUrl(`qna_answers/.search`), dataForQnaAnswers)

			this.isLoading = true

			return Promise.all([promisedProfile, promisedAllComments, promisedQnaQuestionsCompetency, promisedAllAnswers]).then(([profile, allComments, questionsWithIncludes, allAnswers]) => {
				this.profile = profile.data
				this.allComments = allComments.data
				this.allAnswers = allAnswers.data

				const {included, ...allQuestions} = questionsWithIncludes.data
				this.allQuestions = allQuestions

				this.setUserQnaQuestions(questionsWithIncludes.data)

				const questionsIds = this.sortedAnswers.map((element) => {return element.qna_questions})

				return this.loadQuestionsForAnswers(questionsIds)
			}).then((questionsForAnswersWithIncludes) => {
				const {included, ...allQuestionsForAnswers} = questionsForAnswersWithIncludes.data
				this.allQuestionsForAnswers = allQuestionsForAnswers

				this.setUserQnaQuestions(questionsForAnswersWithIncludes.data)

				const config = {
					highlighted: {}
				}

				this.sortedAnswers.reverse().forEach((answer) => {
					config.highlighted[answer.qna_questions] = answer.id
				})

				this.setConfig(config)

				this.$emit('userDataLoaded', {
					profile: this.profile
				})
				this.isLoading = false
			}).catch(exception => $wnl.logger.capture(exception))
		},
		loadQuestionsForAnswers(questionsIds) {
			const userId = this.$route.params.userId
			const data = {
				query: {
					whereIn:
						['id', questionsIds]
				},
				include: 'context,profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles'
			}
			return axios.post(getApiUrl(`qna_questions/.search`), data)
		},
	},
	mounted() {
		this.loadData()
	},
	watch: {
		'$route' (newRoute, oldRoute) {
			if ( this.id !== this.$route.params.userId ) {
				 this.loadData()
			}
		}
	}
}
</script>
