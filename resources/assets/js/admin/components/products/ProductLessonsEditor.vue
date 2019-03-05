<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Lekcje
				</div>
			</div>
		</div>
		<template v-if="productLessons.length">
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
				<tr v-for="productLesson in visibleProductLessons" :key="productLesson.id">
					<td>{{productLesson.lesson_id}}</td>
					<td>{{productLesson.lesson}}</td>
					<td>{{productLesson.start_date}}</td>
				</tr>
				</tbody>
			</table>
		</template>
		<wnl-text-loader v-else-if="loading">Ładuję plan...</wnl-text-loader>
		<p v-else>
			Ten produkt nie ma żadynch lekcji
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
				class="margin bottom big"
				v-model="lessonInput"
				placeholder="wpisz nazwę aby wyszukać..."
				label="Wyszukaj Lekcję"
				:items="autocompleteLessonsItems"
				@change="addLesson"
				:is-down="false"
			>
				<span class="lesson-autocomplete-item" slot-scope="row">{{row.item.id}}. {{row.item.name}}</span>
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
import moment from 'moment';
import {nextTick} from 'vue';
import {mapActions} from 'vuex';
import momentTimezone from 'moment-timezone';

import {getApiUrl} from 'js/utils/env';
import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlDatepicker from 'js/components/global/Datepicker';

export default {
	name: 'ProductLessonEditor',
	components: {
		WnlAutocomplete, WnlDatepicker
	},
	props: {
		id: {
			type: [String, Number],
			required: true
		},
	},
	data() {
		return {
			productLessons: [],
			filterPhrase: '',
			loading: false,
			lessons: [],
			selectedLessons: [],
			lessonInput: ''
		};
	},
	computed: {
		visibleProductLessons() {
			if (!this.filterPhrase) return this.productLessons;

			return this.productLessons.filter(({lesson}) => lesson.toLowerCase().startsWith(this.filterPhrase.toLowerCase()));
		},
		autocompleteLessonsItems() {
			if (this.lessonInput === '') {
				return [];
			}

			return this.lessons
				.filter(lesson => !this.productLessons.find(({lesson_id}) => lesson_id === lesson.id) &&
						lesson.name.toLowerCase().includes(this.lessonInput.toLowerCase()) &&
						!this.selectedLessons.find(({id}) => id === lesson.id)
				)
				.slice(0, 10);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		addLesson(lesson) {
			if (!this.selectedLessons.find(({id}) => id === lesson.id)) {
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
				this.productLessons = await this.getProductLessons();
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
		async getProductLessons() {
			const productLessonResponse = await axios.get(getApiUrl(`lesson_product/${this.id}?include=lessons`));
			const { data: {included, ...productLessons}} = productLessonResponse;

			const productLessonsList = Object.values(productLessons);

			if (!productLessonsList.length) {
				return;
			}

			return productLessonsList.map(productLesson => {
				return {
					...productLesson,
					start_date: moment(productLesson.start_date.date).format('ll'),
					lesson: included.lessons[productLesson.lesson_id].name
				};
			});
		}
	},
	async mounted() {
		this.loading = true;
		try {
			const [productLessons, lessonsResponse] = await Promise.all([
				this.getProductLessons(),
				axios.get(getApiUrl(('lessons/all')))
			]);
			this.lessons = lessonsResponse.data;
			this.productLessons = productLessons;

			nextTick(() => {
				this.$refs.filterInput.focus();
			});
		} catch (e) {
			this.addAutoDismissableAlert({
				text: 'Nie udało się pobrać planu dla tego produktu',
				type: 'error'
			});
			$wnl.logger.capture(e);
		} finally {
			this.loading = false;
		}
	}
};
</script>

<style scoped>

</style>
