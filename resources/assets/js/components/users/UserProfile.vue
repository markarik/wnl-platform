<template>
		<div class="wnl-user-profile" :class="{mobile: isMobileProfile}">
			<div class="text-loader" v-if="isLoading">
				<wnl-text-loader>
					{{ $t('user.userProfile.textLoader') }}
				</wnl-text-loader>
			</div>

			<div class="profile-deleted notification" v-if="profile.deleted_at">
				<div class="profile-deleted__annotation">
					{{ $t('ui.accountDeleted') }}
				</div>
			</div>

			<div v-else-if="isLoadingError" class="notification is-danger">
				Nie udało się załadować profilu dla tego użytkownika.
				Odśwież stronę, żeby spróbować ponownie lub <router-link :to="{name: 'dashboard'}">przejdź na dashboard</router-link>.
			</div>

			<div v-else>
				<div class="user-profile" :class="isMobile" v-if="!isLoading && responseCondition">
					<div class="user-content" :class="avatarClass">
						<wnl-avatar class="user-avatar image" size="extraextralarge"
						:full-name="fullName"
						:url="profile.avatar"
						></wnl-avatar>
						<div class="user-info-header">
							<div class="user-info-header-edit">
								<span v-if="currentUserProfile">
									<router-link :to="{ name: 'my-profile' }">
										<a class="edit-profile button is-primary is-outlined is-small">{{ $t('user.userProfile.editProfileButton') }}</a>
									</router-link>
								</span>
								<wnl-message-link :user-id="profile.user_id">
									<a class="button is-primary is-outlined is-small">Wyślij wiadomość</a>
								</wnl-message-link>
								<span class="user-info-header-names">
									<p class="fullname-title">{{ profile.full_name }}</p>
								</span>
							</div>
							<span v-if="cityToDisplay" class="user-info-city">
								<span class="icon is-small">
									<i class="fa fa-map-marker"></i>
								</span>
								<span class="city-title">{{ cityToDisplay }}</span>
							</span>
							<span v-if="helpToDisplay" class="user-info-help">
								<span class="help-title">{{ $t('user.userProfile.helpTitle') }}</span>
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
						:is-user-profile-class="isUserProfileClass"
						:numbers-disabled="true"
						:title="$t('user.userProfile.bestQuestions')"
						:icon="iconForQuestions"
						v-if="!isLoading && ifAnyQuestions"
						:sorting-enabled="false"
						:read-only="true"
						:reactions-disabled="true"
						:passed-questions="bestQuestions"
						:show-context="true"
						></wnl-qna>
						<wnl-qna
						:is-user-profile-class="isUserProfileClass"
						:numbers-disabled="true"
						:icon="iconForAnswers"
						:title="$t('user.userProfile.bestAnswers')"
						v-if="!isLoading && ifAnyAnswers"
						:sorting-enabled="false"
						:read-only="true"
						:reactions-disabled="true"
						:passed-questions="sortedQuestionsForBestAnswers"
						:show-context="true"
						:config="qnaConfig"
						></wnl-qna>
					</div>
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

		.profile-deleted
			text-align: center
			.profile-deleted__annotation
				text-transform: uppercase
				font-weight: 900

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
				.user-info-city
					align-items: center
					color: $color-gray
					display: flex
					margin-bottom: $margin-base
					overflow-wrap: break-word
					word-break: break-word
					.city-title
						font-size: $font-size-plus-1
						font-weight: $font-weight-regular
						margin-left: $margin-small
				.user-info-help
					display: inline-block
					overflow-wrap: break-word
					word-break: break-all
					word-break: break-word
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
				color: $color-gray
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
import axios from 'axios';
import _ from 'lodash';
import {
	mapActions,
	mapGetters
} from 'vuex';
import {
	getApiUrl
} from 'js/utils/env';
import Avatar from 'js/components/global/Avatar';
import Qna from 'js/components/qna/Qna';
import MessageLink from 'js/components/global/MessageLink';

export default {
	name: 'UserProfile',
	components: {
		'wnl-avatar': Avatar,
		'wnl-qna': Qna,
		'wnl-message-link': MessageLink
	},
	data() {
		return {
			isLoading: true,
			isLoadingError: false,
			isUserProfileClass: 'is-user-profile',
			iconForQuestions: 'fa fa-question-circle-o',
			iconForAnswers: 'fa fa-comment-o',
			id: this.$route.params.userId,
			profile: {},
			allAnswers: {},
			allQuestions: {},
			questionsForBestAnswers: {},
			qnaConfig: {}
		};
	},
	computed: {
		...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile', 'isTouchScreen']),
		...mapGetters(['currentUserId']),
		...mapGetters('qna', ['getSortedQuestions']),
		responseCondition() {
			return !_.isEmpty(this.profile);
		},
		isMobile() {
			return this.isMobileProfile ? 'is-mobile' : '';
		},
		isSinglePanelView() {
			return this.isTouchScreen;
		},
		avatarClass() {
			return this.isMobileProfile ? 'is-mobile-avatar' : 'is-desktop-avatar';
		},
		currentUserProfile() {
			return this.id == this.currentUserId;
		},
		fullName() {
			return this.profile.full_name;
		},
		helpToDisplay() {
			return this.currentUserProfile ? this.profile.help || this.$t('user.userProfile.helpDefaultDescription') : this.profile.help || false;
		},
		cityToDisplay() {
			return this.currentUserProfile ? this.profile.city || this.$t('user.userProfile.cityDefaultDescription') : this.profile.city || false;
		},
		howManyComments() {
			return this.allComments.length;
		},
		howManyQuestions() {
			return Object.values(this.allQuestions).length;
		},
		howManyAnswers() {
			return Object.values(this.allAnswers).length;
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
			];
		},
		ifAnyQuestions() {
			return this.howManyQuestions !== 0;
		},
		ifAnyAnswers() {
			return this.howManyAnswers !== 0;
		},
		sortedQuestionsForBestAnswers() {
			const questionsIds = this.bestAnswers.map((answer) => answer.qna_questions);

			const sortedQuestionsForBestAnswers = [];

			questionsIds.forEach((id) => {
				const value = Object.values(this.questionsForBestAnswers).find((question) => {
					return question.id === id;
				});
				if (sortedQuestionsForBestAnswers.indexOf(value) === -1) {
					sortedQuestionsForBestAnswers.push(value);
				}
			});
			return sortedQuestionsForBestAnswers;
		},
		bestAnswers() {
			return Object.values(this.allAnswers)
				.sort((a, b) => b.upvote.count - a.upvote.count)
				.slice(0, 2);
		},
		bestQuestions() {
			return Object.values(this.allQuestions)
				.sort((a, b) => b.upvote.count - a.upvote.count)
				.slice(0, 2);
		},
	},
	methods: {
		...mapActions('qna', ['setUserQnaQuestions', 'setConfig']),
		togglePanel(panel) {
			return this.activePanels = [panel];
		},
		isPanelActive(panel) {
			if (this.isSinglePanelView) {
				return this.activePanels[0] === panel;
			}
			return this.activePanels.includes(panel);
		},
		async loadData() {
			this.isLoadingError = false;

			const userId = this.$route.params.userId;

			if (!userId) {
				this.$router.push({
					...this.$route,
					params: {
						...this.$route.params,
						userId: this.currentUserId
					}
				});
				return;
			}

			const dataForQnaQuestions = {
				include: 'context,profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles',
				user_id: userId
			};
			const dataForQnaAnswers = {
				include: 'reactions',
				user_id: userId
			};

			this.isLoading = true;

			try {
				const [
					profile,
					allComments,
					questionsWithIncludes,
					allAnswers
				] = await Promise.all([
					axios.get(getApiUrl(`users/${userId}/profile`)),
					axios.get(getApiUrl('comments/query'), {params: {user_id: userId}}),
					axios.get(getApiUrl('qna_questions/query'), {params: dataForQnaQuestions}),
					axios.get(getApiUrl('qna_answers/query'), {params: dataForQnaAnswers})
				]);

				this.profile = profile.data;
				this.allComments = allComments.data;
				this.allAnswers = allAnswers.data;
				this.allQuestions = this.loadQuestions(questionsWithIncludes);
				this.questionsForBestAnswers = await this.loadQuestionsForBestAnswers();
				this.qnaConfig = this.loadConfig();

				this.$emit('userDataLoaded', {
					profile: this.profile
				});
			} catch (exception) {
				$wnl.logger.capture(exception);
				this.isLoadingError = true;
			} finally {
				this.isLoading = false;
			}
		},
		loadQuestions({data}) {
			this.setUserQnaQuestions(data);
			const {included: _, ...allQuestions} = data;
			return allQuestions;
		},
		async loadQuestionsForBestAnswers() {
			const questionsIds = this.bestAnswers.map((element) => {return element.qna_questions;});

			const {data} = await axios.post(getApiUrl('qna_questions/byIds'), {
				ids: questionsIds,
				include: 'context,profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles'
			});
			const {included, ...questionsForBestAnswers} = data;

			this.setUserQnaQuestions(data);

			return questionsForBestAnswers;
		},
		loadConfig() {
			const config = {
				highlighted: {}
			};

			[...this.bestAnswers].reverse().forEach((answer) => {
				config.highlighted[answer.qna_questions] = answer.id;
			});

			return config;
		},
	},
	mounted() {
		this.loadData();
	},
	watch: {
		'$route' () {
			if ( this.id !== this.$route.params.userId ) {
				 this.loadData();
			}
		}
	}
};
</script>
