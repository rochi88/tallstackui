import {overflow} from '../../helpers';

export default (
    id,
    property,
    multiple,
    error,
    staticMode,
    placeholder,
    placeholders,
    overflowing,
    closeAfterUpload
) => ({
  show: false,
  uploading: false,
  error: false,
  static: staticMode,
  warning: error,
  progress: 0,
  dragging: false,
  property: property,
  multiple: multiple,
  placeholders: placeholders,
  component: null,
  preview: false,
  image: null,
  init() {
    this.component = Livewire.find(id).__instance;

    this.component.$wire.watch(this.property, () => this.text());
    this.$watch('uploading', () => this.text());
    this.$watch('preview', (value) => overflow(value, 'upload', overflowing));
  },
  /**
   * Toggle the file upload modal.
   * @returns {void}
   */
  upload() {
    if (!this.$refs.files.files.length) return;

    this.uploading = true;
    this.error = false;

    let abort = true;

    if (typeof window.TallStackUi?.upload === 'function') {
      abort = window.TallStackUi.upload(this.$refs.files.files);
    }

    if (!abort) {
      this.progress = 0;
      this.uploading = false;

      return;
    }

    this.$el.dispatchEvent(new CustomEvent('upload', {detail: {files: this.$refs.files.files}}));

    if (this.multiple) return this.multiples();

    this.single();
  },
  /**
   * Upload multiple files.
   * @returns {void}
   */
  multiples() {
    this.component.$wire.uploadMultiple(
        this.property,
        this.$refs.files.files,
        () => {
          this.uploading = false;
          this.progress = 0;
        },
        () => {
          this.uploading = false;
          this.error = true;
          this.progress = 0;
        },
        (event) => this.progress = event.detail.progress,
    );

    if (closeAfterUpload) this.show = false;
  },
  /**
   * Upload single file.
   * @returns {void}
   */
  single() {
    this.component.$wire.upload(
        this.property,
        this.$refs.files.files[0],
        () => {
          this.uploading = false;
          this.progress = 0;
        },
        () => {
          this.uploading = false;
          this.error = true;
          this.progress = 0;
        },
        (event) => this.progress = event.detail.progress,
    );

    if (closeAfterUpload) this.show = false;
  },
  /**
   * Remove a file through Livewire component.
   * @param method {String}
   * @param file {Object}
   * @returns {void}
   */
  remove(method, file) {
    this.component.$wire.call(method, file);

    this.text();

    this.$el.dispatchEvent(new CustomEvent('remove', {detail: {file: file}}));
  },
  /**
   * Set the input placeholder.
   * @returns {void}
   */
  text() {
    setTimeout(() => {
      const property = this.component.$wire.get(this.property);

      if (this.multiple) {
        // For an unknown reason, sometimes the property returns as an object.
        const quantity = typeof property === 'object' ?
          Object.keys(property).length :
          property.length;

        this.input = quantity === 0 ? null : quantity;

        const items = this.$refs.items;
        items?.scrollTo(0, items.scrollHeight);

        return;
      }

      this.input = !property || this.static ? null : 1;
    }, 500);
  },
  /**
   * Set the input value.
   * @param value {String|Number|Null}
   * @return {void}
   */
  set input(value) {
    const input = this.$refs.input;

    if (!value) {
      input.value = this.$refs.placeholder?.innerText ?? placeholder;

      return;
    }

    input.value = this.placeholders[this.multiple ? 'multiple' : 'single'].replace(':count', value);
  },
});
