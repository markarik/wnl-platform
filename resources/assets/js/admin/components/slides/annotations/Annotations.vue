<template>
	<div>
		<button class="button is-primary" @click="addAnnotation">+ Nowy Przypis</button>
		<div class="tabs">
			<ul>
				<li :class="{'is-active': tab.active}" @click="changeTab(name)" v-for="(tab, name) in tabs" :key="name">
					<a>{{tab.text}}</a>
				</li>
			</ul>
		</div>
		<component
			:is="activeComponent"
			:list="annotations"
			:annotation="activeAnnotation"
			:modifiedAnnotationId="modifiedAnnotationId"
			@annotationSelect="onAnnotationSelect"
			@addSuccess="onAddSuccess"
			@editSuccess="onEditSuccess"
			@deleteSuccess="onDeleteSuccess"
			@hasChanges="onEditorChange"
		/>
	</div>
</template>
<script>
	import axios from 'axios';

	import { getApiUrl } from 'js/utils/env'
	import AnnotationsList from "./AnnotationsList";
	import AnnotationsEditor from "./AnnotationsEditor";

	export default {
		components: {AnnotationsList, AnnotationsEditor},
		data() {
			return {
				tabs: {
					list: {
						component: AnnotationsList,
						active: true,
						text: 'Lista'
					},
					editor: {
						component: AnnotationsEditor,
						active: false,
						text: 'Edytor'
					}
				},
				activeAnnotation: {},
				annotations: [],
				modifiedAnnotationId: 0
			}
		},
		computed: {
			activeTab() {
				return Object.values(this.tabs).find(tab => tab.active)
			},
			activeComponent() {
				return this.activeTab.component
			},
		},
		methods: {
			changeTab(name) {
				this.activeTab.active = false;
				this.tabs[name].active = true;
			},
			addAnnotation() {
				this.changeTab('editor');
				this.activeAnnotation = {
					tags: [],
					keywords: '',
				};
			},
			onEditorChange(changedAnnotation) {
				this.modifiedAnnotationId = changedAnnotation
			},
			onAnnotationSelect(annotation) {
				if (this.modifiedAnnotationId && annotation.id !== this.modifiedAnnotationId) {
					const result = window.confirm(
						`Masz niezapisane zmiany w przypisie ${this.modifiedAnnotationId}. Czy na pewno chcesz zmienić edytowany przypis?`
					)
					if (result) {
						this.onEditorActivate(annotation)
					}
				} else {
					this.onEditorActivate(annotation)
				}
			},
			onEditorActivate(annotation) {
				this.activeAnnotation = annotation;
				this.activeTab.active = false;
				this.tabs.editor.active = true;
				if (annotation.id !== this.modifiedAnnotationId) {
					this.modifiedAnnotationId = 0
				}
			},
			onAddSuccess(annotation) {
				this.activeAnnotation = {
					...annotation,
					keywords: (annotation.keywords || []).join(',')
				}
				this.annotations.splice(0,0, this.activeAnnotation);
			},
			onEditSuccess(annotation) {
				this.activeAnnotation = {
					...annotation,
						keywords: (annotation.keywords || []).join(',')
				}

				this.annotations = this.annotations.map(item => {
					if (item.id === annotation.id) {
						return {
							...this.activeAnnotation
						}
					}
					return item;
				})
			},
			onDeleteSuccess({id}) {
				this.activeAnnotation = {}

				const annotationIndex = this.annotations.findIndex(annotation => annotation.id === id)
				this.annotations.splice(annotationIndex, 1)
			}
		},
		async mounted() {
			const {data} = await axios.get(getApiUrl('annotations/all'), {
				params: {
					include: 'tags,keywords'
				}
			});
			const {included, ...annotations} = data;
			const {tags, keywords} = included;

			this.annotations = Object.values(annotations).map(annotation => {
				return {
					...annotation,
					tags: (annotation.tags || []).map(tagId => ({
						id: tags[tagId].id,
						name: tags[tagId].name,
					})),
					keywords: (annotation.keywords || []).map(keywordId => keywords[keywordId].text).join(',')
				}
			})
		},
		beforeRouteLeave(to, from, next) {
			if (this.modifiedAnnotationId) {
				const result = window.confirm(
					`Masz niezapisane zmiany w przypisie ${this.modifiedAnnotationId}. Czy na pewno chcesz wyjść?`
				)
				result && next()
			} else {
				next()
			}
		}
	}
</script>
