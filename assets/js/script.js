const { __ } = wp.i18n;

window.addEventListener('load', () => A3WCF7FieldsAttributes.init());

class A3WCF7FieldsAttributes {
  static wrapperId = 'a3w-attribute-generator';

  static init() {
    this.addButtonToTagPanel();
  }

  static addButtonToTagPanel() {
    const tagPanel = document.querySelector('.contact-form-editor-panel #tag-generator-list');

    if (tagPanel) {
      const textCustomAttributeGenerator = __('Custom attribute generator', 'a3w-cf7-fields-attributes');

      const btn = document.createElement('button');
      btn.id = 'a3w-attribute-generator-toggle';
      btn.classList.add('thickbox', 'button');
      btn.title = textCustomAttributeGenerator;
      btn.textContent = textCustomAttributeGenerator;
      btn.role = 'button';
      btn.addEventListener('click', (e) => this.openCustomGenerator(e));
      tagPanel.appendChild(btn);

      const editorPanel = document.querySelector('.contact-form-editor-panel');
      if (editorPanel) {
        editorPanel.insertAdjacentHTML('beforeend', a3wVar.panelHTML.trim());
        this.addListenersToPanel();
      }
    }
  }

  static openCustomGenerator(e) {
    e.preventDefault();
    this.toggleDisplayPanel();
  }

  static addListenersToPanel() {
    const panel = document.getElementById('a3w-attribute-generator');

    if (panel) {
      const closeElements = panel.querySelectorAll('.close-a3w-generator');
      closeElements.forEach((element) => element.addEventListener('click', (e) => {
        e.preventDefault();
        this.toggleDisplayPanel();
      }));

      const inputAttrName = document.getElementById('a3w-attr-name');
      inputAttrName?.addEventListener('input', (e) => this.setAttrName(e.currentTarget.value));

      const inputAttrContent = document.getElementById('a3w-attr-content');
      inputAttrContent?.addEventListener('input', (e) => this.setAttrContent(e.currentTarget.value));

      const attrElement = document.getElementById('a3w-attr-cf7');
      attrElement?.addEventListener('input', () => {
        this.setAttrName();
        this.setAttrContent();
      });

      const copyclickElements = panel.querySelectorAll('.a3w-attr-copy');
      copyclickElements.forEach((element) => element.addEventListener('click', (e) => this.copyGeneratedAttribute(e)));
    }
  }

  static toggleDisplayPanel() {
    const panel = document.getElementById('a3w-attribute-generator');
    if (panel) {
      if (panel.style.display === 'none') {
        panel.style.display = 'flex';
        panel.classList.add('opened');
        const closeBtn = panel.querySelector('.panel-header .close');
        if (closeBtn) {
          closeBtn.focus({ focusVisible: true });
        }
      } else {
        panel.style.display = 'none';
        panel.classList.remove('opened');
      }
    }
  }

  static copyGeneratedAttribute(e) {
    const attrElement = document.getElementById('a3w-attr-cf7');
    if (attrElement) {
      attrElement.select();
      attrElement.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(attrElement.value);

      const spanElement = document.createElement('span');
      spanElement.classList.add('copy-confirmed');
      spanElement.textContent = __('Copied!', 'a3w-cf7-fields-attributes');

      attrElement.parentNode.appendChild(spanElement);
      setTimeout(() => {
        spanElement.remove();
      }, 1500);
    }
  }

  static setAttrName(text = null) {
    if (text === null) {
      const inputAttrName = document.getElementById('a3w-attr-name');
      text = inputAttrName ? inputAttrName.value : '';
    }

    const attrElement = document.getElementById('a3w-attr-cf7');
    if (attrElement) {
      const currentValue = attrElement.value;
      const parts = currentValue.split(':');
      text = text.normalize('NFD').replace(/[\u0300-\u036f]/gi, '');
      text = text.replace(/\s/gi, '-');
      const name = 'attr_' + text.toLowerCase();
      if (parts.length > 1) {
        attrElement.value = name + ':' + parts[1];
      } else {
        attrElement.value = name + ':';
      }
    }
  }

  static setAttrContent(text = null) {
    if (text === null) {
      const inputAttrContent = document.getElementById('a3w-attr-content');
      text = inputAttrContent ? inputAttrContent.value : '';
    }
    const attrElement = document.getElementById('a3w-attr-cf7');
    if (attrElement) {
      const currentValue = attrElement.value;
      const parts = currentValue.split(':');
      text = text.replace(/'/gi, '&apos;');
      const content = encodeURIComponent(text.trim());
      if (parts.length > 0) {
        attrElement.value = parts[0] + ':' + content;
      } else {
        attrElement.value = ':' + content;
      }
    }
  }
}
