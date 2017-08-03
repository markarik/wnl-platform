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

    // store data
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
    // See leaf definition as reference:
    // https://github.com/quilljs/parchment/blob/master/src/blot/abstract/leaf.ts
    // NOTE: sometimes `node` contains the actual dom node and sometimes just
    // the text
    return 1;
  }

  /**
   * Replace the current Mention Blot with the given text.
   *
   * @param { String } text the text to replace the mention with.
   */
  _replaceBlotWithText(text) {
    // The steps to replace the Blot with its text must be in this order:
    // 1. insert text - source:API
    //    using API we won't react to changes
    // 2. set selection - source:API
    //    set the cursor position in place
    // 3. remove blot - source:USER
    //    using USER we react to the text-change event and it "looks" like we
    //    did a blot->text replacement in one step.
    //
    // If we don't do these actions in the specified order, the text update and
    // the cursor position won't be as it should for the autocompletion list.

    if (this._removedBlot) return;
    this._removedBlot = true;

    const cursorPosition = quill.getSelection().index;
    const blotCursorPosition = quill.selection.getNativeRange().end.offset;
    const realPosition = cursorPosition + blotCursorPosition;

    quill.insertText(cursorPosition - 1, text, Quill.sources.API);
    quill.setSelection(realPosition - 1, Quill.sources.API);
    quill.deleteText(cursorPosition + text.length - 1, 1, Quill.sources.USER);

    // We use API+USER to be able to hook just USER from the outside and the
    // content edit will look like is done in "one action".
  }

  changeText(oldText, newText) {
    const name = this._data.name;

    const valid = (oldText == name) && (newText != oldText);
    if (!valid) return;

    let cursorPosition = quill.getSelection().index;
    if (cursorPosition == -1) {
      // This case was found just a couple of times and it may not appear again
      // due to improvements made on the MentionBlot. I'm leaving the fix here
      // in case that happens again to debug it.
      cursorPosition = 1;
      console.warning("[changeText] cursorPosition was -1 ... changed to 1");
    }

    const blotCursorPosition = quill.selection.getNativeRange().end.offset;
    let realPosition = cursorPosition;

    if (!this._removedBlot) {
      realPosition += blotCursorPosition;
    } else {
      // Right after the blot is removed we may need to handle a Mutation.
      // If that's the case, considering that the length of the text is 1 would
      // be wrong since it no longer is an Embed but a text.
      console.warning("[changeText] removedBlot is set!");
    }

    if (newText.startsWith(name) && oldText == name) { // append
      // An append happens as follows:
      // Text: <@Name|> -> <@NameX|>
      // We need to move the inserted letter X outside the blot.
      const extra = newText.substr(name.length);

      this.domNode.innerHTML = name;

      // append the text outside the blot
      quill.insertText(cursorPosition, extra, Quill.sources.USER);
      quill.setSelection(cursorPosition + extra.length, Quill.sources.API);
      // quill.insertText(cursorPosition + 2, extra, Quill.sources.USER);
      // quill.setSelection(cursorPosition + extra.length + 3, Quill.sources.API);

      return;
    } else if (newText.endsWith(name) && oldText == name) { // prepend
      // A prepend may be handled in two different ways depending on the
      // browser and the text/cursor state.
      //
      // Case A: (not a problem)
      // Text: |<@Name> -> X|<@Name>
      // Case B: (problem)
      // Text: <|@Name> -> <X|@Name>
      //
      // If we reach this point, it means that we need to tackle Case B.
      // We need to move the inserted letter X outside the blot.
      const end = newText.length - name.length;
      const extra = newText.substr(0, end);

      // The cursor position is set right after the inserted character.
      // In some cases the cursor position gets updated before the text-edit
      // event is emited and in some cases afterwards.
      // This difference manifests itself when the Blot is at the beginning and
      // this conditional assignment handles the issue.
      const pos = cursorPosition > 0 ? cursorPosition - 1 : cursorPosition;

      this.domNode.innerHTML = name;

      // append the text outside the blot
      quill.insertText(pos, extra, Quill.sources.USER);
      quill.setSelection(pos + extra.length, Quill.sources.API);

      return;
    }
    // no append, no prepend, text has changed in a different way.

    // We need to trigger these changes right after the update callback
    // finishes, otherwise errors may appear due to ranges not updating
    // correctly.
    // See: https://github.com/quilljs/quill/issues/1134
    setTimeout(() => this._replaceBlotWithText(newText), 0)
  }

  update(mutations) {
    // See as reference:
    // https://github.com/quilljs/quill/blob/develop/blots/cursor.js

    mutations
      .filter(mutation => mutation.type == 'characterData')
      .forEach(m => {
        const oldText = m.oldValue;
        const newText = m.target.data;
        this.changeText(oldText, newText);
      });

    // I'm not sure whether this is needed or not, keeping it just in case.
    super.update(mutations.filter(mutation => mutation.type != 'characterData'));
  }

}

MentionBlot.blotName = 'mention';
MentionBlot.className = 'quill-mention';
MentionBlot.tagName = 'span';

export default MentionBlot;
