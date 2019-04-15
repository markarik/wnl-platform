<template>
	<div>
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
		<button
			class="button is-primary is-outlined is-big margin top"
			@click="addLesson"
			:disabled="!newLesson.lessonId"
		>Dodaj lekcję</button>
	</div>
</template>

<style lang="sass" scoped>
	/deep/ .new-lesson-datepicker
		width: auto
</style>

<script>
import { mapActions, mapState } from 'vuex';

import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlDatepicker from 'js/components/global/Datepicker';

export default {
	components: {
		WnlAutocomplete, WnlDatepicker
	},
	props: {
		productLessons: {
			type: Array,
			required: true,
		},
	},
	data() {
		return {
			lessonInput: '',
			datepickerConfig: {
				altInput: true,
				enableTime: true,
				dateFormat: 'U',
				altFormat: 'Y-m-d H:i',
				time_24hr: true,
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
		autocompleteLessonsItems() {
			if (this.lessonInput === '') {
				return [];
			}

			return this.lessons
				.filter(lesson => !this.productLessons.find(({ lesson_id }) => lesson_id === lesson.id)
					&& lesson.name.toLowerCase().startsWith(this.lessonInput.toLowerCase())
				)
				.slice(0, 10);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onNewLessonSelect(lesson) {
			this.newLesson.lessonId = lesson.id;
			this.newLesson.name = lesson.name;

			this.lessonInput = '';
		},
		async addLesson() {
			this.$emit('addLesson', this.newLesson);

			this.newLesson = {
				startDate: new Date(),
				lessonId: null,
				name: null,
			};
		},
	},
};
</script>
