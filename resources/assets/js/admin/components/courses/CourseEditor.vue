<template>
    <wnl-form
            :method="method"
            :resource-route="resourceRoute"
            :populate="isEdit"
            name="CourseEditor"
            @change="onFormDataChange"
            :attach="({lessons: lessonIds})"
            :hideDefaultSubmit="true"
            class="editor"
    >
        <div class="header">
            <h2 class="title is-2">Edycja kursu <span v-if="isEdit">(Id: {{id}})</span></h2>
            <wnl-submit class="submit"/>
        </div>
        <wnl-form-text
                name="name"
                class="margin top bottom"
        >Nazwa</wnl-form-text>

        <h3>Kolejność grup lekcji</h3>
        <ol>
            <draggable v-model="groups" @start="drag=true" @end="drag=false">
                <li
                        class="group"
                        v-for="group in groups"
                        :key="group.id"
                >
                    {{group.name}}
                </li>
            </draggable>
        </ol>
    </wnl-form>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
    @import 'resources/assets/sass/variables'
    @import 'resources/assets/sass/mixins'

    .editor
        max-width: 800px

    .header
        +small-shadow-bottom()
        align-items: flex-start
        display: flex
        justify-content: space-between
        background: $color-white
        margin-bottom: $margin-medium
        padding-top: $margin-small
        position: sticky
        top: -$margin-big
        z-index: 101

        .submit
            width: auto

    .group
        border-top: $border-light-gray
        cursor: move
        padding: $margin-base 0
        margin-left: $margin-base
</style>

<script>
	import {Form as WnlForm, Text as WnlFormText, Submit as WnlSubmit} from 'js/components/global/form';

	import draggable from 'vuedraggable';

	export default {
		name: 'CourseEditor',
		props: ['id'],
		data() {
			return {
				groups: [],
			}
		},
		computed: {
			isEdit() {
				return this.id !== 'new';
			},
			method() {
				return this.isEdit ? 'put' : 'post'
			},
			resourceRoute() {
				return this.isEdit ? `courses/${this.id}?include=groups` : 'courses?include=groups';
			},
			groupIds(){
				return this.groups.map(group => group.id);
			}
		},
		components: {
			WnlFormText,
			WnlForm,
			draggable,
			WnlSubmit,
		},
		methods: {
			onSubmitSuccess(data) {
				if (!this.isEdit) {
					this.$router.push({ name: 'course-edit', params: { id: data.id } })
				}
			},
			onFormDataChange({formData}) {
				if (formData.included) {
					this.groups = formData.groups.map(groupId => formData.included.groups[groupId]);
				}
			}
		}
	}
</script>
