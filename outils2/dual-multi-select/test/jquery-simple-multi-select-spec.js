import { keydown } from './helper'

describe('jquery-simple-multi-select', () => {
  beforeEach(() => {
    document.body.innerHTML = __html__['index.html'];
    eval($('script').text());
  });

  describe('basic', () => {
    let $src, $dst;
    let $adder, $remover;

    beforeEach(() => {
      $src = $('#basic_src');
      $dst = $('#basic_dst');
      $adder = $('#basic_adder');
      $remover = $('#basic_remover');
    });

    it('moves options by button', () => {
      $src.find('option').first().prop('selected', true);
      $adder.click();
      expect($src.find('option').length).toEqual(4);
      expect($dst.find('option').length).toEqual(1);

      $dst.find('option').first().prop('selected', true);
      $remover.click();
      expect($src.find('option').length).toEqual(5);
      expect($dst.find('option').length).toEqual(0);
    });

    it('moves options by dblclick', () => {
      $src.find('option').first().prop('selected', true);
      $src.dblclick();
      expect($src.find('option').length).toEqual(4);
      expect($dst.find('option').length).toEqual(1);

      $dst.find('option').first().prop('selected', true);
      $dst.dblclick();
      expect($src.find('option').length).toEqual(5);
      expect($dst.find('option').length).toEqual(0);
    });

    it('moves options by enter key', () => {
      $src.find('option').first().prop('selected', true);
      keydown($src, 13);
      expect($src.find('option').length).toEqual(4);
      expect($dst.find('option').length).toEqual(1);

      $dst.find('option').first().prop('selected', true);
      keydown($dst, 13);
      expect($src.find('option').length).toEqual(5);
      expect($dst.find('option').length).toEqual(0);
    });

    it('selects options before submit', () => {
      $src.find('option').prop('selected', true).dblclick();
      $dst.closest('form').on('submit', () => { return false; }).submit();
      expect($dst.find('option:selected').length).toEqual(5);
    });
  });

  describe('max', () => {
    let $src, $dst;

    beforeEach(() => {
      $src = $('#max_src');
      $dst = $('#max_dst');
    });

    it('has max option', () => {
      $src.find('option').prop('selected', true).dblclick();
      expect($dst.find('option').length).toEqual(2);
    });
  });

  describe('callback', () => {
    let $src, $dst;
    let $adder, $remover;
    let $message;

    beforeEach(() => {
      $src = $('#callback_src');
      $dst = $('#callback_dst');
      $message = $('#callback_message');
    });

    it('has callbacks', () => {
      $src.find('option').first().prop('selected', true);
      $src.dblclick();
      expect($message.text()).toContain('added: option1');

      $dst.find('option').first().prop('selected', true);
      $dst.dblclick();
      expect($message.text()).toContain('removed: option1');
    });
  });

  describe('destroy', () => {
    let $container;
 
    beforeEach(() => {
      eval($('script').text());
      $container = $('#basic');
      $container.data('simple-multi-select').destroy();
    });

    it('destroys existing object', () => {
      expect($container.hasClass('simple-multi-select')).toEqual(false);
    });
  });
});
