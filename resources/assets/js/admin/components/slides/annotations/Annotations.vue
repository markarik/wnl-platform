<template>
	<div>
		<div class="header">
			<div class="tabs">
				<ul>
					<li :class="{[tab.class]: tab.class, 'is-active': tab.view === activeView}"
						@click="changeTab(name, tab)" v-for="(tab, name) in tabs" :key="name"
					>
						<a>{{tab.text}}</a>
					</li>
				</ul>
			</div>
		</div>
		<wnl-paginated-list
			v-show="activeView === 'list'"
			:resource-name="'annotations/.filter'"
			:custom-request-params="requestParams"
			:search-available-fields="searchAvailableFields"
			:dirty="dirty"
			@updated="dirty = false"
		>
			<wnl-annotations-list
				slot="list"
				slot-scope="slotParams"
				:modified-annotation-id="modifiedAnnotationId"
				:list="slotParams.list"
				@annotationSelect="onAnnotationSelect"
			/>
		</wnl-paginated-list>
		<wnl-annotations-editor
			v-show="activeView === 'editor'"
			:annotation="activeAnnotation"
			@addSuccess="dirty = true"
			@editSuccess="dirty = true"
			@deleteSuccess="dirty = true"
			@hasChanges="onEditorChange"
		/>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	/deep/ .annotations__pagination
		margin-top: $margin-base

		.pagination-list
			justify-content: center

	/deep/ .header
		background: white
		position: sticky
		top: -30px
		z-index: 100
		padding-bottom: $margin-small

	/deep/ .tabs
		margin-bottom: 0
		.highlighted
			width: 100%
			text-align: right
			a
				background-color: $color-ocean-blue
				color: white
				display: inline-block
			&.is-active a
				color: #fff

	/deep/ .search
		position: sticky
		top: 13px
		background: white
		padding: $margin-small 0
		z-index: 100
		+small-shadow-bottom()
</style>

<script>
import {mapActions} from 'vuex';
import WnlAnnotationsList from './AnnotationsList';
import WnlAnnotationsEditor from './AnnotationsEditor';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	components: {WnlAnnotationsList, WnlAnnotationsEditor, WnlPaginatedList},
	data() {
		return {
			requestParams: {
				include: 'keywords,tags,taxonomy_terms.tags,taxonomy_terms.taxonomies,taxonomy_terms.ancestors.tags'
			},
			searchAvailableFields: [
				{value: 'id', title: 'ID'},
				{value: 'title', title: 'Tytuł'},
				{value: 'description', title: 'Treść'},
				{value: 'tags.name', title: 'Nazwa Taga'},
			],
			tabs: {
				list: {
					view: 'list',
					text: 'Lista',
				},
				editor: {
					view: 'editor',
					text: 'Edytor'
				},
				new: {
					view: 'editor',
					text: '+Nowy Przypis',
					clickCallback: () => {
						this.activeAnnotation = {
							tags: [],
							keywords: '',
						};
					},
					class: 'highlighted'
				}
			},
			views: ['list', 'editor'],
			activeAnnotation: {},
			modifiedAnnotationId: 0,
			activeView: 'list',
			dirty: false
		};
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		changeTab(name, tab) {
			this.activeView = tab.view;
			if (typeof tab.clickCallback === 'function') tab.clickCallback();
		},
		onEditorChange(changedAnnotation) {
			this.modifiedAnnotationId = changedAnnotation;
		},
		onAnnotationSelect(annotation) {
			if (this.modifiedAnnotationId && annotation.id !== this.modifiedAnnotationId) {
				const result = window.confirm(
					`Masz niezapisane zmiany w przypisie ${this.modifiedAnnotationId}. Czy na pewno chcesz zmienić edytowany przypis?`
				);
				if (result) {
					this.onEditorActivate(annotation);
				}
			} else {
				this.onEditorActivate(annotation);
			}
		},
		onEditorActivate(annotation) {
			this.activeView = 'editor';
			if (annotation.id !== this.modifiedAnnotationId) {
				this.activeAnnotation = annotation;
				this.modifiedAnnotationId = 0;
			}
		},
	},
	beforeRouteLeave(to, from, next) {
		if (this.modifiedAnnotationId) {
			const result = window.confirm(
				`Masz niezapisane zmiany w przypisie ${this.modifiedAnnotationId}. Czy na pewno chcesz wyjść?`
			);
			result && next();
		} else {
			next();
		}
	}
};
</script>
