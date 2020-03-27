import $ from 'jquery';
import { NAMESPACE } from './consts';
import './simple-multi-select.scss';

const DEFAULTS = {
  source: '',
  destination: '',
  adder: '',
  remover: '',
  maxOptions: null,
  sortOptions: false,
  selectOnSubmit: true
};

export default class SimpleMultiSelect {
  constructor(container, options = {}) {
    this.options = $.extend({}, DEFAULTS, options);

    this.$container = $(container);
    this.$src = this.$container.find(this.options.source);
    this.$dst = this.$container.find(this.options.destination);
    this.$adder = this.$container.find(this.options.adder);
    this.$remover = this.$container.find(this.options.remover);

    this.init();
  }

  init() {
    this.$container.addClass(NAMESPACE);
    this.$adder.addClass(`${NAMESPACE}-handler`);
    this.$remover.addClass(`${NAMESPACE}-handler`);
    this.bind();
  }

  destroy() {
    this.$container.removeClass(NAMESPACE);
    this.$adder.removeClass(`${NAMESPACE}-handler`);
    this.$remover.removeClass(`${NAMESPACE}-handler`);
    this.unbind();
  }

  bind() {
    this.$container.on(`click.${NAMESPACE}`, this.options.adder, (e) => {
      this.addOptions();
    }).on(`click.${NAMESPACE}`, this.options.remover, (e) => {
      this.removeOptions();
    }).on(`dblclick.${NAMESPACE}`, this.options.source, (e) => {
      this.addOptions();
    }).on(`dblclick.${NAMESPACE}`, this.options.destination, (e) => {
      this.removeOptions();
    }).on(`keydown.${NAMESPACE}`, this.options.source, (e) => {
      if (e.keyCode == 13) {
        e.preventDefault();
        this.addOptions();
      }
    }).on(`keydown.${NAMESPACE}`, this.options.destination, (e) => {
      if (e.keyCode == 13) {
        e.preventDefault();
        this.removeOptions();
      }
    });

    if (this.options.selectOnSubmit) {
      this.$container.closest('form').on(`submit.${NAMESPACE}`, () => {
        this.$src.find('option').prop('selected', false);
        this.$dst.find('option').prop('selected', true);
      });
    }
  }

  unbind() {
    this.$container.closest('form').off(`.${NAMESPACE}`)
    this.$container.off(`.${NAMESPACE} option:added option:removed`);
  }

  addOptions() {
    this.$src.find('option:selected').each((i, option) => {
      let $option = $(option);
      if (this.options.maxOptions && this.$dst.find('option').length >= this.options.maxOptions) {
        return false;
      }
      this.add($option)
    });

    if (this.options.sortOptions) this.sortOptions(this.$dst);
  }

  removeOptions() {
    this.$dst.find('option:selected').each((i, option) => {
      let $option = $(option);
      this.remove($option);
    });

    if (this.options.sortOptions) this.sortOptions(this.$src);
  }

  add($option) {
    this.$dst.append($option.prop('selected', false));
    this.$container.trigger('option:added', [$option]);
  }

  remove($option) {
    this.$src.append($option.prop('selected', false));
    this.$container.trigger('option:removed', [$option]);
  }

  sortOptions($target) {
    let $sorted = $target.find('option').sort((a, b) => {
      return $(a).val() > $(b).val() ? 1 : -1;
    });
    $target.empty().html($sorted);
  }

  static getDefaults() {
    return DEFAULTS;
  }

  static setDefaults(options) {
    return $.extend(DEFAULTS, options);
  }
}
