<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<wnl-user-background></wnl-user-background>
		<div class="wnl-user-profile-avatar">
				<wnl-avatar
				:fullName="this.response.data.full_name"
                :url="this.response.data.avatar"
                class="image is-128x128" size="huge"></wnl-avatar>
		</div>

		<div class="wnl-user-info">
			<h1>{{ this.response.data.first_name }} {{ this.response.data.last_name }}</h1>
		</div>
		<h1>KOMENTARZE</h1>
		<hr>

		<ul>
			<li v-for="comment in comments">
				<div v-html="comment.text"/>
				<hr>
			</li>
		</ul>

		<h1>QNA_ANSWERS</h1>
		<hr>

		<ul>
			<li v-for="qna_answer in qna_answers">
				<div v-html="qna_answer.text"/>
				<div>{{ this.answerReactionsLength }}</div>
				<hr>
			</li>
		</ul>

		<h1>QNA_QUESTIONS</h1>
		<hr>

		<ul>
			<li v-for="qna_question in qna_questions">
				<div v-html="qna_question.text"/>

				<hr>
			</li>
		</ul>
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
	import Upload from 'js/components/global/Upload'
	import { Form, Text } from 'js/components/global/form'
	import { isProduction } from 'js/utils/env'
	import UserBackground from 'js/components/users/UserBackground'

	export default {
		name: 'UserProfile',
		components: {
			'wnl-user-background': UserBackground,
            'wnl-avatar': Avatar,
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-upload': Upload,
		},
		props: ['response', 'competency'],
		data() {
			return {
				loading: false,
				hideDefaultSubmit: true,
				id: this.$route.params.userId,
				disableInput: true,
				comments: this.competency.data.competency.comments,
				qna_answers: this.competency.data.competency.qna_answers,
				answerReactionsLength: this.competency.data.competency.qna_answers[0].reactions.length,
				qna_questions: this.competency.data.competency.qna_questions,
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
 			computedResourceRoute() {
				return `users/${this.id}/profile`
			}
		},
		mounted() {
			console.log(this.sorted);
		}
	}
</script>
