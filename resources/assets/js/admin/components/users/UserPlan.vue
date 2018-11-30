<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Plan Lekcji xD
				</div>
			</div>
		</div>
		<template v-if="userLessons.length">
			<input v-model="filterPhrase" class="input margin bottom" placeholder="filtruj..." ref="filterInput">
			<table class="table is-bordered">
				<thead>
					<tr>
						<th>ID lekcji</th>
						<th>Lekcja</th>
						<th>Data otwarcia</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="userLesson in visibleUserLessons" :key="userLesson.id">
						<td>{{userLesson.lesson_id}}</td>
						<td>{{userLesson.lesson}}</td>
						<td>{{userLesson.start_date}}</td>
					</tr>
				</tbody>
			</table>
		</template>
		<wnl-text-loader v-else-if="loading">Ładuję plan lekcji....</wnl-text-loader>
		<p v-else>
			Ten użytkownik nie ma żadynch lekcji
		</p>
	</div>
</template>

<script>
	import moment from 'moment'
	import {nextTick} from 'vue'
	import {mapActions} from 'vuex'
	import {getApiUrl} from 'js/utils/env'

	export default {
		name: "UserPlan",
		props: {
			user: {
				type: Object,
				required: true
			},
		},
		data() {
			return {
				userLessons: [],
				filterPhrase: '',
				loading: false
			}
		},
		computed: {
			visibleUserLessons() {
				if (!this.filterPhrase) return this.userLessons;

				return this.userLessons.filter(({lesson}) => lesson.toLowerCase().startsWith(this.filterPhrase.toLowerCase()));
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
		},
		async mounted() {
			this.loading = true
			try {
				const response = await axios.get(getApiUrl(`user_lesson/${this.user.id}?include=lessons`))
				const { data: {included, ...userLessons}} = response;
				const userLessonsList = Object.values(userLessons);

				if (!userLessonsList.length) {
					return
				}

				this.userLessons = userLessonsList.map(userLesson => {
					return {
						...userLesson,
						start_date: moment(userLesson.start_date.date).format('ll'),
						lesson: included.lessons[userLesson.lessons[0]].name
					}
				})

				nextTick(() => {
					this.$refs.filterInput.focus()
				})
			} catch (e) {
				this.addAutoDismissableAlert({
					text: "Nie udało się pobrać planu dla tego użytkownika",
					type: 'error'
				})
				$wnl.logger.capture(e)
			} finally {
				this.loading = false
			}
		}
	}
</script>

<style scoped>

</style>
