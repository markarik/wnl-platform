/* eslint-disable */
import Quill from 'quill';

const EmbedBlot = Quill.import('blots/embed');

class MentionBlot extends EmbedBlot {

	static create(data) {
		const node = super.create(data.name);
		node.innerHTML = `<span contenteditable="false">${data.name}</span>`;
		node.setAttribute('spellcheck', 'false');
		node.setAttribute('autocomplete', 'off');
		node.setAttribute('autocorrect', 'off');
		node.setAttribute('autocapitalize', 'off');

		// we can add more user fields info here
		node.setAttribute('data-name', data.name);
		node.setAttribute('data-id', data.id);

		return node;
	}

	static value(domNode) {
		const { name, id } = domNode.dataset;
		return { name, id };
	}

	constructor(domNode, value) {
		super(domNode);
		this._data = value;
		this._removedBlot = false;
	}
	
	index(node, offset) {
		// https://github.com/quilljs/parchment/blob/master/src/blot/abstract/leaf.ts
		return 1;
	}

	_replaceBlotWithText(text) {
		if (this._removedBlot) return;
		this._removedBlot = true;

		const cursorPosition = quill.getSelection().index;
		const blotCursorPosition = quill.selection.getNativeRange().end.offset;
		const realPosition = cursorPosition + blotCursorPosition;

		quill.insertText(cursorPosition - 1, text, Quill.sources.API);
		quill.setSelection(realPosition - 1, Quill.sources.API);
		quill.deleteText(cursorPosition + text.length - 1, 1, Quill.sources.USER);
	}

	changeText(oldText, newText) {}

	update(mutations) {
		mutations
			.filter(mutation => mutation.type == 'characterData')
			.forEach(m => {
				const oldText = m.oldValue;
				const newText = m.target.data;
				this.changeText(oldText, newText);
			});

		super.update(mutations.filter(mutation => mutation.type != 'characterData'));
	}
}

MentionBlot.blotName = 'mention';
MentionBlot.className = 'quill-mention';
MentionBlot.tagName = 'span';

export default MentionBlot;
