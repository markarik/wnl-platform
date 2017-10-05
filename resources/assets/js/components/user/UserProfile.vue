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

		<ul>
			<li v-for="comment in comments">
				<div v-html="comment.text"/>
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
				// qna_answers: this.competency.data.competency.qna_answers,
				// qna_questions: this.competency.data.competency.qna_questions,
			}
		},
		computed: {
			...mapGetters(['isMobileProfile']),
			isProduction() {
				return isProduction()
			},
			computedResourceRoute() {
				return `users/${this.id}/profile`
			}
		},
	}
</script>
