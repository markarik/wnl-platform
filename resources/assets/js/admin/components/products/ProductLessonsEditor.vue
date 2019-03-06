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
					<td>
						<wnl-datepicker
							name="start_date"
							:config="datepickerConfig" :value="productLesson.start_date"
							@onChange="onDateChange($event, productLesson)"
						/>
					</td>
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
		</div>
		<a class="button button is-primary is-outlined is-big" @click="submitPlan">Zapisz</a>
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
			lessonInput: '',
			datepickerConfig: {
				altInput: true,
				enableTime: true,
				dateFormat: 'U',
				altFormat: 'Y-m-d H:i',
				time_24hr: true,
			},
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
				.filter(lesson => !this.productLessons.find(({lesson_id}) => lesson_id === lesson.id)
					&& lesson.name.toLowerCase().includes(this.lessonInput.toLowerCase())
				)
				.slice(0, 10);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		addLesson(lesson) {
			if (!this.productLessons.some(({lesson_id}) => lesson_id === lesson.id)) {
				this.productLessons.push({
					lesson: lesson.name,
					lesson_id: lesson.id,
					start_date: new Date()
				});
			}

			this.lessonInput = '';
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
					start_date: moment.utc(productLesson.start_date.date).toDate(),
					lesson: included.lessons[productLesson.lesson_id].name
				};
			});
		},
		onDateChange(value, productLesson) {
			productLesson.start_date = value[0];
		},
		async submitPlan() {
			await axios.put(getApiUrl(`lesson_product/${this.id}`), {
				lessons: this.productLessons.map(productLesson => {
					return {
						lesson_id: productLesson.lesson_id,
						start_date: moment.utc(productLesson.start_date).unix()
					};
				})
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
