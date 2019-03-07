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
			<wnl-sortable-table
				:columns="tableColumns"
				:active-sort-column-name="activeSort.activeSortColumnName"
				:sort-direction="activeSort.sortDirection"
				:list="visibleProductLessons"
				@changeOrder="onSort"
			>
				<tbody slot-scope="table" slot="tbody">
					<tr v-for="productLesson in table.list" :key="productLesson.id">
						<td>{{productLesson.lesson_id}}</td>
						<td>{{productLesson.lesson_name}}</td>
						<td class="wnl-table__cell--datepicker">
							<wnl-datepicker
									name="start_date"
									:key="productLesson.lesson_id"
									:config="datepickerConfig"
									:value="productLesson.start_date"
									@onChange="onDateChange($event, productLesson)"
							/>
						</td>
						<td>
							<button
									class="button is-danger"
									type="button"
									@click="removeLesson(productLesson)"
							>
								<span class="icon"><i class="fa fa-trash"></i></span>
							</button>
						</td>
					</tr>
				</tbody>
			</wnl-sortable-table>
		</template>
		<wnl-text-loader v-else-if="loading">Ładuję plan...</wnl-text-loader>
		<p v-else>
			Ten produkt nie ma żadynch lekcji
		</p>
		<div>
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item big strong">
						Dodaj Lekcję
					</div>
				</div>
			</div>
			<label class="label">Nazwa lekcji</label>
			<wnl-autocomplete
				v-if="!newLesson.lessonId"
				class="margin bottom big"
				v-model="lessonInput"
				placeholder="wpisz nazwę aby wyszukać..."
				:items="autocompleteLessonsItems"
				:is-down="false"
				@change="onNewLessonSelect"
			>
				<span class="lesson-autocomplete-item" slot-scope="row">{{row.item.id}}. {{row.item.name}}</span>
			</wnl-autocomplete>
			<div v-else>
				{{newLesson.name}}
				<span
					class="icon clickable is-small margin left"
					@click="newLesson.lessonId = null"
				><i class="fa fa-close"></i>
				</span>
			</div>
			<label class="label margin top">Data otwarcia</label>
			<wnl-datepicker
				class="new-lesson-datepicker"
				name="start_date"
				:config="datepickerConfig"
				:value="newLesson.startDate"
				@onChange="value => newLesson.startDate = value[0]"
			/>
		</div>
		<button class="button is-primary is-outlined is-big margin top" @click="addLesson" :disabled="!newLesson.lessonId">Dodaj lekcję</button>
	</div>
</template>

<style lang="sass" scoped>
	.wnl-table__cell--datepicker
		width: 240px

	/deep/ .new-lesson-datepicker
		width: auto
</style>

<script>
import moment from 'moment';
import {nextTick} from 'vue';
import {mapActions, mapState} from 'vuex';
import {orderBy} from 'lodash';

import {getApiUrl} from 'js/utils/env';
import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlDatepicker from 'js/components/global/Datepicker';
import WnlSortableTable from 'js/admin/components/lists/SortableTable';

export default {
	name: 'ProductLessonEditor',
	components: {
		WnlAutocomplete, WnlDatepicker, WnlSortableTable
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
			lessonInput: '',
			datepickerConfig: {
				altInput: true,
				enableTime: true,
				dateFormat: 'U',
				altFormat: 'Y-m-d H:i',
				time_24hr: true,
			},
			tableColumns: [
				{
					name: 'lesson_id',
					label: 'ID',
				}, {
					name: 'lesson_name',
					label: 'Nazwa',
				}, {
					name: 'start_date',
					label: 'Data otwarcia'
				}, {
					name: 'actions',
					label: 'Akcje',
					sortable: false
				}
			],
			activeSort: {
				activeSortColumnName: 'start_date',
				sortDirection: 'asc'
			},
			newLesson: {
				startDate: new Date(),
				lessonId: null,
				name: null,
			},
		};
	},
	computed: {
		...mapState('lessons', ['lessons']),
		visibleProductLessons() {
			const {sortDirection: sort, activeSortColumnName: key} = this.activeSort;

			const filteredLessons = this.filterPhrase ?
				this.productLessons
					.filter(({lesson_name}) => lesson_name.toLowerCase().startsWith(this.filterPhrase.toLowerCase()))
				: this.productLessons;

			return orderBy(filteredLessons, key, [sort]);
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
		...mapActions('lessons', ['fetchAllLessons']),
		onNewLessonSelect(lesson) {
			this.newLesson.lessonId = lesson.id;
			this.newLesson.name = lesson.name;

			this.lessonInput = '';
		},
		async addLesson() {
			if (!this.productLessons.some(({lesson_id}) => lesson_id === this.newLesson.lessonId)) {
				await axios.post(getApiUrl(`lesson_product/${this.id}`), {
					lesson_id: this.newLesson.lessonId,
					start_date: moment.utc(this.newLesson.startDate).unix(),
				});
				this.productLessons.push({
					lesson_name: this.newLesson.name,
					lesson_id: this.newLesson.lessonId,
					start_date: this.newLesson.startDate,
				});

				this.newLesson = {
					startDate: new Date(),
					lessonId: null,
					name: null,
				};
			}
		},
		async removeLesson(productLesson) {
			try {
				await axios.delete(getApiUrl(`lesson_product/${this.id}/${productLesson.lesson_id}`));
				const index = this.productLessons.findIndex(({lesson_id}) => productLesson.lesson_id === lesson_id);
				this.productLessons.splice(index, 1);
			} catch (e) {
				this.addAutoDismissableAlert({
					text: 'Nie udało się usunąć lekcji',
					type: 'error'
				});
				$wnl.logger.capture(e);
			}
		},
		async getProductLessons() {
			const productLessonResponse = await axios.get(getApiUrl(`lesson_product/${this.id}`));
			const { data: {...productLessons}} = productLessonResponse;

			const productLessonsList = Object.values(productLessons);

			if (!productLessonsList || !productLessonsList.length) {
				return [];
			}

			return productLessonsList.map(productLesson => {
				const matchingLesson = this.lessons.find(lesson => productLesson.lesson_id === lesson.id) || {};
				return {
					...productLesson,
					start_date: new Date(productLesson.start_date * 1000),
					lesson_name: matchingLesson.name
				};
			}).sort((productLessonA, productLessonB) => {
				return productLessonA.start_date - productLessonB.start_date;
			});
		},
		async onDateChange(value, productLesson) {
			try {
				await axios.put(getApiUrl(`lesson_product/${this.id}/${productLesson.lesson_id}`), {
					start_date: moment.utc(value[0]).unix(),
				});

				productLesson.start_date = value[0];

				this.addAutoDismissableAlert({
					text: 'Zapisano!',
					type: 'success'
				});
			} catch (e) {

				this.addAutoDismissableAlert({
					text: 'Nie udało się zapisać zmian.',
					type: 'error'
				});
				$wnl.logger.capture(e);
			}
		},
		onSort(sort) {
			this.activeSort = sort;
		}
	},
	async mounted() {
		this.loading = true;
		try {
			await this.fetchAllLessons();
			this.productLessons = await this.getProductLessons();

			nextTick(() => {
				this.$refs.filterInput && this.$refs.filterInput.focus();
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
