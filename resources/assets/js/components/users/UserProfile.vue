<template lang="html">
		<div v-if="responseCondition" class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
			<div class="user-content">
				<wnl-user-background class="user-background"
					:fullName="fullName">
					<wnl-avatar class="user-avatar image is-128x128" size="extralarge"
						:fullName="fullName"
						:url="profile.avatar"
					></wnl-avatar>
					<div class="user-info-header">
						<h1>{{ profile.first_name }} {{ profile.last_name }}</h1>
						<h2>{{ profile.city }}</h2>
						<h3>{{ profile.help }}</h3>
						<!-- <h3>{{ address.city }}</h3> -->
					</div>
				</wnl-user-background>
			</div>
			<hr>
			<div class="user-section-header">
				<p class="title is-4">AKTYWNOŚĆ</p>
			</div>
			<br>
			<div class="user-activity-content">
				<wnl-activity-meter class="user-activity" :activity="'Komentarze'" :activityCount="howManyComments"></wnl-activity-meter>
				<wnl-activity-meter class="user-activity" :activity="'Odpowiedzi'" :activityCount="howManyAnswers"></wnl-activity-meter>
				<wnl-activity-meter class="user-activity" :activity="'Pytania'" :activityCount="howManyQuestions"></wnl-activity-meter>
			</div>

			<hr>
			<div class="top-activities" v-if="ifAnyQuestions || ifAnyAnswers">
				<div class="user-section-header">
					<p class="title is-4">TOPOWA AKTYWNOŚĆ</p>
				</div>
				<br>
				<wnl-qna
					:title="'Nqjlepsze Pytania'"
					v-if="ifAnyQuestions"
					:sortingEnabled="sortingDisabled"
					:readOnly="readOnly"
					:reactionsDisabled="reactionsDisabled"
					:qnaQuestionsCompetency="convertSortedQuestionsToObject"
				></wnl-qna>
				<wnl-qna
					:title="'Najlepsze Odpowiedzi'"
					v-if="ifAnyAnswers"
					:sortingEnabled="sortingDisabled"
					:readOnly="readOnly"
					:reactionsDisabled="reactionsDisabled"
					:qnaAnswersCompetency="convertSortedAnswersToObject"
				></wnl-qna>
				<hr>
			</div>
			<div class="user-section-header">
				<p class="title is-4">WSZYSTKIE WPISY</p>
			</div>
			<div class="collections-controls">
				<a v-for="name, panel in panels" class="panel-toggle" :class="{'is-active': isPanelActive(panel), 'is-single': isSinglePanelView}"  :key="panel" @click="togglePanel(panel)">
					{{name}}
					<span class="icon is-small">
						<i class="fa" :class="[isPanelActive(panel) ? 'fa-check-circle' : 'fa-circle-o']"></i>
					</span>
				</a>
			</div>
			<div class="columns">
				<div class="column" v-show="isCommentsPanelVisible">
					<div class="comments-content">
						<hr>
						<p class="title is-4">Komentarze ({{howManyComments}})</p>
						<br>
						<wnl-comment
							v-for="comment in commentsCompetency.data"
							:comment="comment"
							:key="comment.id"
							:profile="profile"
						>
							<router-link :to="{ name: comment.context.name, params: comment.context.params }">Pokaż kontekst</router-link>
						</wnl-comment>
					</div>
				</div>
				<div class="column" v-show="isAnswersPanelVisible">
					<div class="qna-answers">
						<hr>
						<wnl-qna
							:title="'Wszystkie Odpowiedzi'"
							:sortingEnabled="sortingEnabled"
							:readOnly="readOnly"
							:reactionsDisabled="reactionsDisabled"
							:qnaAnswersCompetency="qnaAnswersComputed"
						></wnl-qna>
					</div>
				</div>
				<div class="column" v-show="isQuestionsPanelVisible">
					<div class="qna-questions">
						<hr>
						<wnl-qna
							:title="'Wszystkie Pytania'"
							:sortingEnabled="sortingEnabled"
							:readOnly="readOnly"
							:reactionsDisabled="reactionsDisabled"
							:qnaQuestionsCompetency="qnaQuestionsComputed"
						></wnl-qna>
					</div>
				</div>
			</div>
		</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.collections-controls
		align-items: center
		display: flex
		flex-wrap: wrap
		margin-bottom: $margin-base

	.user-background
		display: flex
		flex-direction: row
		justify-content: flex-start
		align-items: center
		padding-left: 2vw
		.user-avatar
			z-index: 1
			margin-right: 1vw

	.user-activity-content
		display: flex
		flex-direction: row
		justify-content: space-around
		align-items: center

	.user-section-header
		display: flex
		justify-content: flex-start

</style>

<script>
import _ from 'lodash'
import { mapActions, mapGetters } from 'vuex'
import { isProduction, getApiUrl } from 'js/utils/env'
import Avatar from 'js/components/global/Avatar'
import Comment from 'js/components/comments/Comment'
import UserBackground from 'js/components/users/UserBackground'
import QnaQuestion from 'js/components/qna/QnaQuestion'
import QnaAnswer from 'js/components/qna/QnaAnswer'
import Qna from 'js/components/qna/Qna'
import ActivityMeter from 'js/components/users/ActivityMeter'

export default {
    name: 'UserProfile',
    components: {
        'wnl-user-background': UserBackground,
        'wnl-avatar': Avatar,
		'wnl-comment': Comment,
        'wnl-qna-question': QnaQuestion,
        'wnl-qna-answer': QnaAnswer,
        'wnl-qna': Qna,
        'wnl-activity-meter': ActivityMeter,
    },
    props: ['readOnly'],
    data() {
        return {
            sortingDisabled: false,
            sortingEnabled: true,
            loading: false,
            id: this.$route.params.userId,
            reactionsDisabled: true,
            activePanels: ['comments'],
			profile: {},
			qnaAnswersCompetency: {},
			qnaQuestionsCompetency: {},
			commentsCompetency: {}
        }
    },
    computed: {
        ...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile', 'isTouchScreen']),
        howManyComments() {
            return this.commentsCompetency.data.length
        },
        howManyQuestions() {
            return Object.values(this.qnaQuestionsComputed).length
        },
        ifAnyQuestions() {
			return this.howManyQuestions === 0 ? false : true
        },
		fullName() {
			return this.profile.full_name
		},
        howManyAnswers() {
            return Object.values(this.qnaAnswersComputed).length
        },
        ifAnyAnswers() {
            return this.howManyAnswers === 0 ? false : true
        },
        isProduction() {
            return isProduction()
        },
        isCommentsPanelVisible() {
            return this.isPanelActive('comments')
        },
        isQuestionsPanelVisible() {
            return this.isPanelActive('questions')
        },
        isAnswersPanelVisible() {
            return this.isPanelActive('answers')
        },
        qnaQuestionsComputed() {
			if (!this.responseCondition) return {}
			const {
                included,
                ...questions
            } = this.qnaQuestionsCompetency.data;
            return questions;
        },
        qnaAnswersComputed() {
			if (!this.responseCondition) return {}
            const {
                included,
                ...questions
            } = this.qnaAnswersCompetency.data;
            return questions;
        },
        sortQuestionsCompetency() {
            return Object.values(this.qnaQuestionsComputed).sort((a, b) => {
                return b.upvote.count - a.upvote.count
            })
        },
        sortAnswersCompetency() {
            return Object.values(this.qnaAnswersComputed).sort((a, b) => {
                return b.upvote.count - a.upvote.count
            })
        },
        convertSortedQuestionsToObject() {
            if (this.sortQuestionsCompetency.length > 1) {
                return {
                    0: this.sortQuestionsCompetency[0],
                    1: this.sortQuestionsCompetency[1]
                }
            } else if (this.sortQuestionsCompetency.length = 1) {
                return {
                    0: this.sortQuestionsCompetency[0]
                }
            } else {
                return ''
            }
        },
        convertSortedAnswersToObject() {
            if (this.sortAnswersCompetency.length > 1) {
                return {
                    0: this.sortAnswersCompetency[0],
                    1: this.sortAnswersCompetency[1]
                }
            } else if (this.sortAnswersCompetency.length = 1) {
                return {
                    0: this.sortAnswersCompetency[0]
                }
            } else {
                return ''
            }
        },
        panels() {
            return {
                comments: 'Komentarze',
                questions: 'Pytania',
                answers: 'Odpowiedzi'
            }
        },
        isSinglePanelView() {
            return this.isTouchScreen
        },
		responseCondition() {
			return !_.isEmpty(this.profile)
		},
    },
    methods: {
        ...mapActions('qna', ['setUserQnaQuestions']),
        togglePanel(panel) {
            return this.activePanels = [panel]
        },
        isPanelActive(panel) {
            if (this.isSinglePanelView) {
                return this.activePanels[0] === panel
            }

            return this.activePanels.includes(panel)
        },
    },
    mounted() {
		const userId = this.$route.params.userId;
		const dataForComments = {
			query: {
				where: [[ 'user_id', userId ]]
			},
			include: 'context'
		}
		const dataForQnaQuestions = {
			query: {
				where: [[ 'user_id', userId ]]
			},
			include: 'context,profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles'
		}
		const dataForQnaAnswers = {
			query: {
				whereHas: {
					answers: {
						where: [[ 'user_id', userId ]]
					}
				}
			},
			include: 'context,profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles'
		}
		// const promisedAddress = axios.get(getApiUrl(`users/${userId}/address`))
		const promisedProfile = axios.get(getApiUrl(`users/${userId}/profile`))
		const promisedCommentsCompetency = axios.post(getApiUrl(`comments/.search`), dataForComments)
		const promisedQnaQuestionsCompetency = axios.post(getApiUrl(`qna_questions/.search`), dataForQnaQuestions)
		const promisedQnaAnswersCompetency = axios.post(getApiUrl(`qna_questions/.search`), dataForQnaAnswers)

		Promise.all([promisedProfile, promisedCommentsCompetency, promisedQnaQuestionsCompetency, promisedQnaAnswersCompetency])
		.then(([profile, commentsCompetency, qnaQuestionsCompetency, qnaAnswersCompetency]) => {
			this.profile = profile.data
			this.commentsCompetency = commentsCompetency
			this.qnaQuestionsCompetency = qnaQuestionsCompetency
	        this.qnaAnswersCompetency = qnaAnswersCompetency

			this.setUserQnaQuestions(qnaAnswersCompetency.data)
			this.setUserQnaQuestions(qnaQuestionsCompetency.data)
			this.$emit('userDataLoaded', {
				profile: this.profile
			})
		})
		.catch(exception => $wnl.logger.capture(exception))
    },
}
</script>
