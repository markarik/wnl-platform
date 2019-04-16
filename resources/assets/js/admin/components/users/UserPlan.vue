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
			<input
				ref="filterInput"
				v-model="filterPhrase"
				class="input margin bottom"
				placeholder="filtruj..."
			>
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
			Ten użytkownik nie ma żadnych lekcji
		</p>
		<div>
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item big strong">
						Dodaj Lekcje
					</div>
				</div>
			</div>
			<wnl-autocomplete
				v-model="lessonInput"
				class="margin bottom big"
				placeholder="wpisz nazwę aby wyszukać..."
				label="Wyszukaj Lekcję"
				:items="autocompleteLessonsItems"
				:is-down="false"
				@change="addLesson"
			>
				<span slot-scope="row" class="lesson-autocomplete-item">{{row.item.id}}. {{row.item.name}}</span>
			</wnl-autocomplete>
			<div v-if="selectedLessons.length">
				<table class="table user-plan__add-lesson">
					<thead>
						<tr>
							<th>ID Lekcji</th>
							<th>Lekcja</th>
							<th>Data Startu</th>
							<th>Akcje</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="lesson in selectedLessons" :key="lesson.id">
							<td>{{lesson.id}}</td>
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
									<span class="icon"><i class="fa fa-trash" /></span>
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
import axios from 'axios';
import moment from 'moment';
import { nextTick } from 'vue';
import { mapActions, mapState } from 'vuex';
import momentTimezone from 'moment-timezone';

import { getApiUrl } from 'js/utils/env';
import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlDatepicker from 'js/components/global/Datepicker';

export default {
	name: 'UserPlan',
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
			selectedLessons: [],
			lessonInput: ''
		};
	},
	computed: {
		...mapState('lessons', ['lessons']),
		visibleUserLessons() {
			if (!this.filterPhrase) return this.userLessons;

			return this.userLessons.filter(({ lesson }) => lesson.toLowerCase().startsWith(this.filterPhrase.toLowerCase()));
		},
		autocompleteLessonsItems() {
			if (this.lessonInput === '') {
				return [];
			}

			return this.lessons
				.filter(lesson => !this.userLessons.find(({ lesson_id }) => lesson_id === lesson.id) &&
						lesson.name.toLowerCase().includes(this.lessonInput.toLowerCase()) &&
						!this.selectedLessons.find(({ id }) => id === lesson.id)
				)
				.slice(0, 10);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('lessons', ['fetchAllLessons']),
		addLesson(lesson) {
			if (!this.selectedLessons.find(({ id }) => id === lesson.id)) {
				this.selectedLessons.push({
					...lesson,
					startDate: new Date()
				});
			}

			this.lessonInput = '';
		},
		onStartDateChange(payload, lesson) {
			this.selectedLessons = this.selectedLessons.map(selectedLesson => {
				if (lesson.id === selectedLesson.id) {
					return selectedLesson;
				}
				return {
					...selectedLesson,
					startDate: payload[0]
				};
			});
		},
		unselectLesson(lesson) {
			const index = this.selectedLessons.findIndex(selectedLesson => selectedLesson.id === lesson.id);
			this.selectedLessons.splice(index, 1);
		},
		async addLessons() {
			try {
				await axios.put(getApiUrl(`user_lesson/${this.user.id}/batch`), {
					manual_start_dates: this.selectedLessons.map(lesson => {
						return {
							lessonId: lesson.id,
							startDate: lesson.startDate
						};
					}),
					timezone: momentTimezone.tz.guess(),
				});
				this.addAutoDismissableAlert({
					type: 'success',
					text: 'Dodano'
				});
				this.loading = true;
				this.userLessons = await this.getUserLessons();
				this.selectedLessons = [];
				this.loading = false;
			} catch (e) {
				this.addAutoDismissableAlert({
					type: 'error',
					text: 'Oho, coś poszłó nie tak. Lekcje nie zostały dodane'
				});
				$wnl.logger.capture(e);
			}
		},
		getUserLessons(userPlanResponse) {
			const { data: { ...userLessons } } = userPlanResponse;

			const userLessonsList = Object.values(userLessons);

			if (!userLessonsList || !userLessonsList.length) {
				return [];
			}

			return userLessonsList.map(userLesson => {
				const matchingLesson = this.lessons.find(lesson => userLesson.lesson_id === lesson.id) || {};
				const startDate = userLesson.start_date && userLesson.start_date.date || null;
				return {
					...userLesson,
					start_date: moment(startDate).format('ll'),
					lesson: matchingLesson.name
				};
			});
		}
	},
	async mounted() {
		this.loading = true;
		try {
			const [userPlanResponse] = await Promise.all([
				axios.get(getApiUrl(`user_lesson/${this.user.id}`)),
				this.fetchAllLessons()
			]);

			this.userLessons = this.getUserLessons(userPlanResponse);
			nextTick(() => {
				this.$refs.filterInput && this.$refs.filterInput.focus();
			});
		} catch (e) {
			this.addAutoDismissableAlert({
				text: 'Nie udało się pobrać planu dla tego użytkownika',
				type: 'error'
			});
			$wnl.logger.capture(e);
		} finally {
			this.loading = false;
		}
	}
};
</script>
