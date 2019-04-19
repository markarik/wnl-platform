<template>
	<div class="field">
		<div id="wnl-code-editor" />
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	#wnl-code-editor
		min-height: 100%
		min-width: 100%
</style>

<script>
import * as brace from 'brace';
import 'brace/ext/modelist';
import 'brace/ext/themelist';

let editor;
const modelist  = brace.acequire('ace/ext/modelist');
const themelist = brace.acequire('ace/ext/themelist');

export default {
	name: 'WnlFormCode',
	props: {
		name: {
			type: String,
		},
		value: {
			type: String,
		},
	},
	data() {
		return {
			code: '',
			mode: 'html',
			theme: 'chrome',
		};
	},
	watch: {
		value (newValue) {
			if (!newValue) editor.setValue('', -1);

			if (newValue && newValue !== editor.getValue()) {
				editor.setValue(newValue, -1);
			}
		}
	},
	mounted () {
		editor = brace.edit('wnl-code-editor');
		this.setMode();
		this.setTheme();
		editor.$blockScrolling = Infinity;
		editor.setShowPrintMargin(false);
		editor.getSession().on('change', this.emitCode);
		editor.getSession().setUseWrapMode(true);
	},
	methods: {
		setMode () {
			let modeObj = modelist.modesByName[this.mode];

			if (modeObj) {
				require('brace/mode/html');
				editor.getSession().setMode(modeObj.mode);
			}
		},
		setTheme () {
			let themeObj = themelist.themesByName[this.theme];

			if (themeObj) {
				require('brace/theme/' + themeObj.name);
				editor.setTheme(themeObj.theme);
			}
		},
		emitCode () {
			this.$emit('input', editor.getValue());
		}
	}
};
</script>
