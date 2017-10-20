<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="user-content">
			<wnl-user-background class="user-background"
				:fullName="fullName">
				<wnl-avatar class="user-avatar image is-128x128" size="extralarge"
					:fullName="profile.full_name"
					:url="profile.avatar"
				></wnl-avatar>
				<div class="user-info">
					<h1>{{ profile.first_name }} {{ profile.last_name }}</h1>
					<h3>{{ profile.public_email }}</h3>
					<!-- <h3>{{ address.city }}</h3> -->
				</div>
			</wnl-user-background>
		</div>
		<hr>
		<div class="user-activity-header">
			<p class="title is-4">Aktywność</p>
		</div>
		<br>
		<div class="user-activity-content">
			<wnl-activity-meter class="user-activity" :activity="'Komentarze'" :activityCount="howManyComments"></wnl-activity-meter>
			<wnl-activity-meter class="user-activity" :activity="'Odpowiedzi'" :activityCount="howManyAnswers"></wnl-activity-meter>
			<wnl-activity-meter class="user-activity" :activity="'Pytania'" :activityCount="howManyQuestions"></wnl-activity-meter>
		</div>

		<hr>
		<div class="top-activities">
			<p class="title is-4">Topowe aktywności</p>
			<wnl-qna
				:title="'Nqjlepsze Pytania'"
				v-if="ifAnyQuestions"
				:sordingEnabled="sortingDisabled"
				:readOnly="readOnly"
				:reactionsDisabled="reactionsDisabled"
				:qnaQuestionsCompetency="convertSortedQuestionsToObject"
			></wnl-qna>
			<wnl-qna
				:title="'Najlepsze Odpowiedzi'"
				v-if="ifAnyAnswers"
				:sordingEnabled="sortingDisabled"
				:readOnly="readOnly"
				:reactionsDisabled="reactionsDisabled"
				:qnaAnswersCompetency="convertSortedAnswersToObject"
			></wnl-qna>
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
					<hr>
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
		.user-info
			z-index: 1
			padding-left: 2vw


	.user-info
		display: flex
		justify-content: center
		flex-direction: column
		align-items: center
		position: relative

	.user-activity-content
		display: flex
		flex-direction: row
		justify-content: space-around
		align-items: center

	.user-activity-header
		display: flex
		justify-content: center

</style>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

    import Avatar from 'js/components/global/Avatar'
	import Comment from 'js/components/comments/Comment'
	import Upload from 'js/components/global/Upload'
	import { Form, Text } from 'js/components/global/form'
	import { isProduction } from 'js/utils/env'
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
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-upload': Upload,
			'wnl-qna-question': QnaQuestion,
			'wnl-qna-answer': QnaAnswer,
			'wnl-qna': Qna,
			'wnl-activity-meter': ActivityMeter,
		},
		props: ['address', 'profile', 'readOnly', 'commentsCompetency', 'qnaAnswersCompetency', 'qnaQuestionsCompetency'],
		data() {
			return {
				sortingDisabled: false,
				sortingEnabled: true,
				loading: false,
				hideDefaultSubmit: true,
				id: this.$route.params.userId,
				disableInput: true,
				reactionsDisabled: true,
				activePanels: ['comments'],
				fullName: this.profile.full_name,
			}
		},
		computed: {
			...mapGetters(['isMobileProfile', 'isTouchScreen']),
			howManyComments() {
				return this.commentsCompetency.data.length
			},
			howManyQuestions() {
				return Object.values(this.qnaQuestionsComputed).length
			},
			ifAnyQuestions() {
				if (this.howManyQuestions === 0) {
					return false
				} else {
					return true
				}
			},
			howManyAnswers() {
				return Object.values(this.qnaAnswersComputed).length
			},
			ifAnyAnswers() {
				if (this.howManyAnswers === 0) {
					return false
				} else {
					return true
				}
			},
			isProduction() {
				return isProduction()
			},
			sorted() {
				return this.qna_answers.sort(function(a, b) {
					return b.reactions.length - a.reactions.length
				})
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
				const {included, ...questions} = this.qnaQuestionsCompetency.data;
				return questions;
			},
			qnaAnswersComputed() {
				const {included, ...questions} = this.qnaAnswersCompetency.data;
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
			}
		},
		pointsForQuestions() {
			return Object.values(this.qnaQuestionsComputed.data).reduce((sum, el) => {
				return sum + el.upvote.count
			}, 0)
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
			// this.pointsForQuestions()
			this.setUserQnaQuestions(this.qnaQuestionsCompetency.data)
			this.setUserQnaQuestions(this.qnaAnswersCompetency.data)
			console.log(Object.values(this.qnaQuestionsComputed).length);
		},
	}
</script>
