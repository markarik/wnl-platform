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
		<div>
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item big strong">
						Dodaj Lekcje
					</div>
				</div>
			</div>
			<div class="control">
				<label class="label">Wyszukaj Lekcję</label>
				<input class="input" placeholder="nazwa aby wyszukać" v-model="lessonInput"/>
			</div>
			<div class="control margin bottom big">
				<wnl-autocomplete
					:items="autocompleteLessonsItems"
					:onItemChosen="addLesson"
				>
					<template slot-scope="row">
						<span class="lesson-autocomplete-item">{{row.item.id}}. {{row.item.name}}</span>
					</template>
				</wnl-autocomplete>
			</div>
			<div  v-if="selectedLessons.length">
				<table class="table user-plan__add-lesson">
					<thead>
						<tr>
							<th>Lekcja</th>
							<th>Data Startu</th>
							<th>Akcje</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="lesson in selectedLessons" :key="lesson.id">
							<td>{{lesson.name}}</td>
							<td>
								<wnl-datepicker
									:value="lesson.startDate"
									@onChange="(payload) => onStartDateChange(payload, lesson)"
								/>
							</td>
							<td>
								<button
										class="button is-danger"
										type="button"
										@click="unselectLesson(lesson)"
								>
									<span class="icon"><i class="fa fa-trash"></i></span>
								</button>
							</td>
						</tr>
					</tbody>
				</table>
				<a class="button button is-primary is-outlined is-big" @click="addLessons">Zapisz</a>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	.user-plan__add-lesson .datepicker
		text-align: left
</style>

<script>
	import moment from 'moment'
	import {nextTick} from 'vue'
	import {mapActions} from 'vuex'
	import momentTimezone from 'moment-timezone'

	import {getApiUrl} from 'js/utils/env'
	import WnlAutocomplete from 'js/components/global/Autocomplete';
	import WnlDatepicker from 'js/components/global/Datepicker'

	export default {
		name: "UserPlan",
		components: {
			WnlAutocomplete, WnlDatepicker
		},
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
				loading: false,
				lessons: [],
				selectedLessons: [],
				lessonInput: ''
			}
		},
		computed: {
			visibleUserLessons() {
				if (!this.filterPhrase) return this.userLessons;

				return this.userLessons.filter(({lesson}) => lesson.toLowerCase().startsWith(this.filterPhrase.toLowerCase()));
			},
			autocompleteLessonsItems() {
				if (this.lessonInput === '') {
					return [];
				}

				return this.lessons
					.filter(lesson => !this.userLessons.find(({lesson_id}) => lesson_id === lesson.id) &&
						(
							lesson.name.toLowerCase().includes(this.lessonInput.toLowerCase())
						)
					)
					.slice(0, 10)
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			addLesson(lesson) {
				if (!this.selectedLessons.includes(lesson)) {
					this.selectedLessons.push({
						...lesson,
						startDate: new Date()
					})
				}

				this.lessonInput = '';
			},
			onStartDateChange(payload, lesson) {
				this.selectedLessons = this.selectedLessons.map(selectedLesson => {
					if (lesson.id === selectedLesson.id) {
						return selectedLesson
					}
					return {
						...selectedLesson,
						startDate: payload[0]
					}
				})
			},
			unselectLesson(lesson) {
				const index = this.selectedLessons.findIndex(selectedLesson => selectedLesson.id === lesson.id)
				this.selectedLessons.splice(index, 1)
			},
			async addLessons() {
				await axios.put(getApiUrl(`user_lesson/${this.user.id}/batch`), {
					manual_start_dates: this.selectedLessons.map(lesson => {
						return {
							lessonId: lesson.id,
							startDate: lesson.startDate
						}
					}),
					timezone: momentTimezone.tz.guess(),
				})
			}
		},
		async mounted() {
			this.loading = true
			try {
				const [userPlanResponse, lessonsResponse] = await Promise.all([
					axios.get(getApiUrl(`user_lesson/${this.user.id}?include=lessons`)),
					axios.get(getApiUrl(('lessons/all')))
				])
				const { data: {included, ...userLessons}} = userPlanResponse
				this.lessons = lessonsResponse.data

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
