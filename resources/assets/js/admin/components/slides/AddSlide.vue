<template>
	<wnl-slides-editor
		title="Dodaj slajd"
		method="post"
		:resource-url="resourceUrl"
		:request-payload="requestPayload"
		:screen-id="Number(screenId)"
	>
		<div class="level margin vertical">
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
	</wnl-slides-editor>
</template>

<script>
import SlidesEditor from 'js/admin/components/slides/SlideEditor';
import { getApiUrl } from 'js/utils/env';

export default {
	name: 'AddSlide',
	components: {
		'wnl-slides-editor': SlidesEditor,
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
