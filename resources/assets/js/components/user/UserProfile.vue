<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="user-content">
			<wnl-user-background></wnl-user-background>
			<div class="wnl-user-profile-avatar">
				<wnl-avatar
				:fullName="profile.full_name"
				:url="profile.avatar"
				class="image is-128x128" size="huge"></wnl-avatar>
			</div>

			<div class="wnl-user-info">
				<h1>{{ profile.first_name }} {{ profile.last_name }}</h1>
			</div>
		</div>
		<div class="comments-content">
			<h1>KOMENTARZE</h1>
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

		<div class="qna-answers">
			<h1>ODPOWIEDZI</h1>
			<hr>
			<wnl-qna
				:readOnly="readOnly"
				:reactionsDisabled="reactionsDisabled"
				:qnaAnswersCompetency="qnaAnswersComputed"
			></wnl-qna>
		</div>

		<div class="qna-questions">
			<h1>PYTANIA</h1>
			<hr>
			<wnl-qna
				:readOnly="readOnly"
				:reactionsDisabled="reactionsDisabled"
				:qnaQuestionsCompetency="qnaQuestionsComputed"
			></wnl-qna>
		</div>

	</div>
</template>

<style lang="sass">

	.image
		align-self: center
		margin: auto

</style>

<script>
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
		},
		props: ['profile', 'readOnly', 'commentsCompetency', 'qnaAnswersCompetency', 'qnaQuestionsCompetency'],
		data() {
			return {
				loading: false,
				hideDefaultSubmit: true,
				id: this.$route.params.userId,
				disableInput: true,
				reactionsDisabled: true,
			}
		},
		computed: {
			...mapGetters(['isMobileProfile']),
			isProduction() {
				return isProduction()
			},
			sorted() {
				return this.qna_answers.sort(function(a, b) {
					return b.reactions.length - a.reactions.length
				})
			},
			qnaQuestionsComputed() {
				const {included, ...questions} = this.qnaQuestionsCompetency.data;
				return questions;
			},
			qnaAnswersComputed() {
				const {included, ...questions} = this.qnaAnswersCompetency.data;
				return questions;
			}
		},
		methods: {
			...mapActions('qna', ['setUserQnaQuestions'])
		},
		mounted() {
			this.setUserQnaQuestions(this.qnaQuestionsCompetency.data)
			this.setUserQnaQuestions(this.qnaAnswersCompetency.data)
		},
	}
</script>
