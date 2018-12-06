<template>
    <wnl-form
            :method="method"
            :resource-route="resourceRoute"
            :populate="isEdit"
            name="GroupEditor"
            @submitSuccess="onSubmitSuccess"
    >
        <h3 class="title is-3">Edycja grupy <span v-if="isEdit">(Id: {{id}})</span></h3>
        <wnl-form-text
                name="name"
                class="margin top bottom"
        >Nazwa</wnl-form-text>

    </wnl-form>
</template>

<script>
	import {Form as WnlForm, Text as WnlFormText} from 'js/components/global/form'

	export default {
		name: 'GroupEditor',
		props: ['id'],
        computed: {
			isEdit() {
				return this.id !== 'new';
            },
            method() {
				return this.isEdit ? 'put' : 'post'
            },
			resourceRoute() {
				return this.isEdit ? `groups/${this.id}` : 'groups';
			}
        },
        components: {
			WnlFormText,
			WnlForm
        },
        methods: {
			onSubmitSuccess(data) {
				if (!this.isEdit) {
					this.$router.push({ name: 'group-edit', params: { id: data.id } })
				}
            }
        }
	}
</script>
