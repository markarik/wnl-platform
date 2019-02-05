<template>
	<div>
		<div v-if="selected" class="autocomplete-selected">
			{{selected.name}}
			<span class="icon is-small clickable" @click="onSelect(null)"><i class="fa fa-close" aria-hidden="true"></i></span>
		</div>
		<div class="control" v-else>
			<input class="input" v-model="search" placeholder="Wpisz nazwę lekcji/grupy, którą chcesz dołączyć lub utworzyć" />
			<wnl-autocomplete
				:items="autocompleteItems"
				:onItemChosen="onSelect"
				:isDown="true"
			>
				<template slot-scope="slotProps">
					<div>
						<span class="icon is-small">
							<i :class="['fa', getStructurableIcon(slotProps.item)]" aria-hidden="true"></i>
						</span>

						{{slotProps.item.name}}
					</div>
				</template>
				<template slot="footer" v-if="search !== ''">
					<div>
						<div class="margin">
							Nie mamy lekcji ani grupy o nazwie <strong>{{search}}</strong>
						</div>
						<div class="autocomplete-footer-button-container">
							<button class="button" @click="onLessonAdd">
								<span class="icon is-small">
									<i class="fa fa-plus" aria-hidden="true"></i>
								</span>
								<span>Nowa lekcja</span>
							</button>
							<button class="button" @click="onGroupAdd">
								<span class="icon is-small">
									<i class="fa fa-plus" aria-hidden="true"></i>
								</span>
								<span>Nowa grupa</span>
							</button>
						</div>
					</div>
				</template>
			</wnl-autocomplete>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.autocomplete-selected
		display: flex
		justify-content: space-between
		padding: $margin-small-minus

	.autocomplete-footer-button-container
		border-top: $border-light-gray
		padding: $margin-base
		text-align: right
		display: flex

	.icon
		margin: 0 $margin-tiny
		padding: $margin-small-minus

</style>

<script>
import {mapState, mapActions, mapGetters} from 'vuex';
import {uniqBy} from 'lodash';

import WnlAutocomplete from 'js/components/global/Autocomplete';
import {ALERT_TYPES} from 'js/consts/alert';
import {COURSE_STRUCTURE_TYPES} from 'js/consts/courseStructure';

export default {
	props: {
		selected: {
			type: Object,
			default: null,
		}
	},
	data() {
		return {
			search: '',
		};
	},
	computed: {
		...mapState('lessons', ['lessons']),
		...mapState('groups', ['groups']),
		...mapGetters('courseStructure', ['getStructurableIcon']),
		autocompleteItems() {
			if (!this.search) {
				return [];
			}
			const lowerSearch = this.search.toLocaleLowerCase();

			const items = [
				...this.lessons.map(lesson => {
					lesson.type = COURSE_STRUCTURE_TYPES.LESSON;
					return lesson;
				}),
				...this.groups.map(group => {
					group.type = COURSE_STRUCTURE_TYPES.GROUP;
					return group;
				})];

			const filteredItems = items.filter(item => item.name.toLocaleLowerCase().startsWith(lowerSearch));
			filteredItems.push(...items.filter(item => item.name.toLocaleLowerCase().includes(lowerSearch)));

			return uniqBy(filteredItems, (item => [item.id, item.type].join())).slice(0, 25);
		},
	},
	components: {
		WnlAutocomplete
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('lessons', {
			setupLessons: 'setup',
			createLesson: 'create',
		}),
		...mapActions('groups', {
			fetchAllGroups: 'fetchAll',
			createGroup: 'create',
		}),
		onSelect(item) {
			this.search = '';
			this.$emit('change', item);
		},
		async onLessonAdd() {
			try {
				const lesson = await this.createLesson(this.search);
				this.search = '';
				this.$emit('change', lesson);
			} catch (error) {
				$wnl.logger.capture(error);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error',
				});
			}
		},
		async onGroupAdd() {
			try {
				const group = await this.createGroup(this.search);
				this.search = '';
				this.$emit('change', group);
			} catch (error) {
				$wnl.logger.capture(error);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
					type: 'error',
				});
			}
		},
	},
	async mounted() {
		try {
			await Promise.all([this.setupLessons(), this.fetchAllGroups()]);
		} catch (error) {
			$wnl.logger.capture(error);

			this.addAutoDismissableAlert({
				text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
				type: ALERT_TYPES.ERROR,
			});
		}

	}
};
</script>