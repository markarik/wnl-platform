<template>
	<wnl-slide-editor
		title="Dodaj slajd"
		method="post"
		:resource-url="resourceUrl"
		:request-payload="requestPayload"
		:screen-id="Number(screenId)"
	>
		<div class="level margin vertical" slot="above-content">
			<div class="level-left">
				<div class="level-item">
					<div class="field is-grouped">
						<div class="control">
							<label class="label">Numer screena</label>
							<input type="text" class="input" v-model="screenId">
						</div>
						<div class="control">
							<label class="label">Numer slajdu</label>
							<input type="text" class="input" v-model="slideOrderNo">
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="notification is-info" slot="below-content">
			<span class="icon">
				<i class="fa fa-info-circle"></i>
			</span>
			Przypisanie pojęć do slajdu jest możliwe tylko w trybie edycji
		</div>
	</wnl-slide-editor>
</template>

<script>
import WnlSlideEditor from 'js/admin/components/slides/SlideEditor';
import { getApiUrl } from 'js/utils/env';

export default {
	name: 'AddSlide',
	components: {
		WnlSlideEditor,
	},
	data() {
		return {
			screenId: null,
			slideOrderNo: null,
			resourceUrl: getApiUrl('slides'),
		};
	},
	computed: {
		requestPayload() {
			return {
				screen: this.screenId,
				order_number: this.slideOrderNo
			};
		}
	},
};
</script>
