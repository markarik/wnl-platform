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
		<wnl-annotations-list
			v-show="activeView === 'list'"
			:modified-annotation-id="modifiedAnnotationId"
			@annotationSelect="onAnnotationSelect"
		/>
		<wnl-annotations-editor
			v-show="activeView === 'editor'"
			:annotation="activeAnnotation"
			@annotationSelect="onAnnotationSelect"
			@addSuccess="onAddSuccess"
			@editSuccess="onEditSuccess"
			@deleteSuccess="onDeleteSuccess"
			@hasChanges="onEditorChange"
		/>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.annotations__pagination
		margin-top: $margin-base

		.pagination-list
			justify-content: center

	.header
		background: white
		position: sticky
		top: -30px
		z-index: 100
		padding-bottom: $margin-small

	.tabs
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
	.search
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

export default {
	components: {WnlAnnotationsList, WnlAnnotationsEditor},
	data() {
		return {
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
			activeView: 'list'
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
			this.activeAnnotation = annotation;
			this.activeView = 'editor';
			if (annotation.id !== this.modifiedAnnotationId) {
				this.modifiedAnnotationId = 0;
			}
		},
		onAddSuccess(annotation) {
			this.activeAnnotation = {
				...annotation,
				keywords: (annotation.keywords || []).join(',')
			};
			this.annotations.splice(0,0, this.activeAnnotation);
		},
		onEditSuccess(annotation) {
			this.activeAnnotation = {
				...annotation,
				keywords: (annotation.keywords || []).join(',')
			};

			this.annotations = this.annotations.map(item => {
				if (item.id === annotation.id) {
					return {
						...this.activeAnnotation
					};
				}
				return item;
			});
		},
		onDeleteSuccess({id}) {
			this.activeAnnotation = {};

			const annotationIndex = this.annotations.findIndex(annotation => annotation.id === id);
			this.annotations.splice(annotationIndex, 1);
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
