<template>
	<wnl-slide-editor
		title="Dodaj slajd"
		method="post"
		:resource-url="resourceUrl"
		:request-payload="requestPayload"
		:screen-id="Number(screenId)"
	>
		<div slot="above-content" class="level margin vertical">
			<div class="level-left">
				<div class="level-item">
					<div class="field is-grouped">
						<div class="control">
							<label class="label">Numer screena</label>
							<input
								v-model="screenId"
								type="text"
								class="input"
							>
						</div>
						<div class="control">
							<label class="label">Numer slajdu</label>
							<input
								v-model="slideOrderNo"
								type="text"
								class="input"
							>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div slot="below-content" class="notification is-info">
			<span class="icon">
				<i class="fa fa-info-circle" />
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
