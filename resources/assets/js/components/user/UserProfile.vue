<template lang="html">
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
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

	export default {
		name: 'UserProfile',
		components: {
			'wnl-user-background': UserBackground,
            'wnl-avatar': Avatar,
			'wnl-comment': Comment,
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-upload': Upload,
		},
		props: ['profile', 'commentsCompetency'],
		data() {
			return {
				loading: false,
				hideDefaultSubmit: true,
				id: this.$route.params.userId,
				disableInput: true,
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
		},
		mounted() {

			// console.log(this.sorted);
		}
	}
</script>
