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
								:config="datepickerConfig"
								:value="productLesson.start_date"
								@closed="onDatePickerClosed($event, productLesson)"
							/>
						</td>
						<td>
							<button
								class="button is-danger"
								type="button"
								@click="confirmLessonRemoval(productLesson)"
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
			Ten produkt nie ma żadnych lekcji
		</p>
		<wnl-product-lessons-editor-add-lesson :product-lessons="productLessons" @addLesson="onAddLesson" />
	</div>
</template>

<style lang="sass" scoped>
	.wnl-table__cell--datepicker
		width: 240px
</style>

<script>
import axios from 'axios';
import moment from 'moment';
import { nextTick } from 'vue';
import { mapActions, mapState } from 'vuex';
import { orderBy } from 'lodash';

import { getApiUrl } from 'js/utils/env';
import { swalConfig } from 'js/utils/swal';
import WnlDatepicker from 'js/components/global/Datepicker';
import WnlSortableTable from 'js/admin/components/lists/SortableTable';
import WnlProductLessonsEditorAddLesson from 'js/admin/components/products/ProductLessonsEditorAddLesson';

export default {
	name: 'ProductLessonEditor',
	components: {
		WnlDatepicker, WnlSortableTable, WnlProductLessonsEditorAddLesson
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
		};
	},
	computed: {
		...mapState('lessons', ['lessons']),
		visibleProductLessons() {
			const { sortDirection: sort, activeSortColumnName: key } = this.activeSort;

			const filteredLessons = this.filterPhrase ?
				this.productLessons
					.filter(({ lesson_name }) => lesson_name.toLowerCase().startsWith(this.filterPhrase.toLowerCase()))
				: this.productLessons;

			return orderBy(filteredLessons, key, [sort]);
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('lessons', ['fetchAllLessons']),
		serializeProductLesson(productLesson) {
			const matchingLesson = this.lessons.find(lesson => productLesson.lesson_id === lesson.id) || {};
			return {
				...productLesson,
				start_date: new Date(productLesson.start_date * 1000),
				lesson_name: matchingLesson.name
			};
		},
		async onAddLesson(lesson) {
			if (!this.productLessons.some(({ lesson_id }) => lesson_id === lesson.lessonId)) {
				const { data: productLesson } = await axios.post(getApiUrl('lesson_product'), {
					product_id: this.id,
					lesson_id: lesson.lessonId,
					start_date: moment.utc(lesson.startDate).unix(),
				});
				this.productLessons.push(this.serializeProductLesson(productLesson));
			}
		},
		async performLessonRemoval(productLesson) {
			try {
				await axios.delete(getApiUrl(`lesson_product/${productLesson.id}`));
				const index = this.productLessons.findIndex(({ lesson_id }) => productLesson.lesson_id === lesson_id);
				this.productLessons.splice(index, 1);
				this.addAutoDismissableAlert({
					text: 'Usunięto!',
					type: 'success'
				});
			} catch (e) {
				this.addAutoDismissableAlert({
					text: 'Nie udało się usunąć lekcji',
					type: 'error'
				});
				$wnl.logger.capture(e);
			}
		},
		async confirmLessonRemoval(productLesson) {
			try {
				await this.$swal(swalConfig({
					confirmButtonText: this.$t('ui.confirm.yes'),
					cancelButtonText: this.$t('ui.confirm.no'),
					html: `Na pewno chcesz usunąć lekcję <strong>${productLesson.lesson_name}?</strong> z produktu?`,
					showCancelButton: true,
					type: 'warning',
				}));
				this.performLessonRemoval(productLesson);
			} catch (e) {
				// Do nothing, swal throws an exception on cancel
			}
		},
		getProductLessons(productLessonResponse) {
			const { data: { ...productLessons } } = productLessonResponse;

			const productLessonsList = Object.values(productLessons);

			if (!productLessonsList || !productLessonsList.length) {
				return [];
			}

			return productLessonsList.map(this.serializeProductLesson);
		},
		async onDatePickerClosed(value, productLesson) {
			try {
				await axios.put(getApiUrl(`lesson_product/${productLesson.id}`), {
					start_date: value,
				});

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
			const [productLessonResponse] = await Promise.all([
				axios.post(getApiUrl('lesson_product/query'), {
					product_id: this.id
				}),
				this.fetchAllLessons()
			]);
			this.productLessons = this.getProductLessons(productLessonResponse);

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
