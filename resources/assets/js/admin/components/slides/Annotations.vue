<template>
  <div>
    <p class="title is-3">Dodaj Adnotację</p>
    <form class="" action="" method="POST" @submit.prevent="onSubmit"
      @keydown="form.errors.clear($event.target.name)"
    >
      <div class="field is-horizontal annotation-input-text">
        <div class="field-label">
          <label class="label">Hasło</label>
        </div>
        <div class="field-body">
          <div class="field">
            <div class="control">
              <input class="input" type="text" v-model="annotationKey">
            </div>
          </div>
        </div>
      </div>
      <div class="field is-horizontal annotation-input-text">
        <div class="field-label">
          <label class="label">ID</label>
        </div>
        <div class="field-body">
          <div class="field">
            <div class="control">
              <input class="input" type="text" v-model="annotationId" disabled>
            </div>
          </div>
        </div>
      </div>
      <div class="annotation-input-description">
        <wnl-form-code type="text" name="content" :form="form"/>
      </div>
      <div class="level-item">
          <a class="button is-primary"
             :disabled="form.errors.any() || !form.content"
             @click="onSubmit">Dodaj Adnotacje
          </a>
      </div>
    </form>
  </div>
</template>

<style lang="sass" scoped>
  @import 'resources/assets/sass/variables'

  .field-label
    flex-basis: 80px
    flex-grow: 0

  .annotation-input-text
    display: inline-flex
    width: 500px
    align-items: center

  .annotation-input-description
    border: $border-light-gray
    height: 500px
    margin: $margin-big 0
</style>

<script>
	import { getApiUrl } from 'js/utils/env'
	import Code from 'js/admin/components/forms/Code'
	import Form from 'js/classes/forms/Form'

	export default {
		name: 'Annotations',
		components: {
      'wnl-form-code': Code
		},
		data() {
			return {
				form: new Form({
					content: null,
					is_functional: null,
				}),
        annotationKey: '',
        annotationId: 0
			}
		},
		computed: {
			requestPayload() {
			}
		},
	}
</script>
