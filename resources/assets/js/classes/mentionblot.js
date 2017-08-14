import Quill from 'quill'

const EmbedBlot = Quill.import('blots/embed')

class MentionBlot extends EmbedBlot {

  static create(data) {
    const node = super.create(data.name);
    node.innerHTML = data.name;
    node.setAttribute('spellcheck', "false");
    node.setAttribute('autocomplete', "off");
    node.setAttribute('autocorrect', "off");
    node.setAttribute('autocapitalize', "off");

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

  // eslint-disable-next-line no-unused-vars
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

  changeText(oldText, newText) {
    const name = this._data.name;

    const valid = (oldText == name) && (newText != oldText);
    if (!valid) return;

    let cursorPosition = quill.getSelection().index;
    if (cursorPosition == -1) {
      cursorPosition = 1;
      console.warning("[changeText] cursorPosition was -1 ... changed to 1");
    }

    const blotCursorPosition = quill.selection.getNativeRange().end.offset;
    let realPosition = cursorPosition;

    if (!this._removedBlot) {
      realPosition += blotCursorPosition;
    } else {
      console.warning("[changeText] removedBlot is set!");
    }

    if (newText.startsWith(name) && oldText == name) { // append
      const extra = newText.substr(name.length);

      this.domNode.innerHTML = name;

      quill.insertText(cursorPosition, extra, Quill.sources.USER);
      quill.setSelection(cursorPosition + extra.length, Quill.sources.API);
      return;
    } else if (newText.endsWith(name) && oldText == name) { // prepend
      const end = newText.length - name.length;
      const extra = newText.substr(0, end);
      const pos = cursorPosition > 0 ? cursorPosition - 1 : cursorPosition;

      this.domNode.innerHTML = name;

      quill.insertText(pos, extra, Quill.sources.USER);
      quill.setSelection(pos + extra.length, Quill.sources.API);

      return;
    }
    setTimeout(() => this._replaceBlotWithText(newText), 0)
  }

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
