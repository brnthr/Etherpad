import $ from 'jquery';
import { NAMESPACE } from './consts';
import SimpleMultiSelect from './simple-multi-select';

$.fn.simpleMultiSelect = function(options) {
  return this.each((i, elem) => {
    let $elem = $(elem);
    if ($elem.data(NAMESPACE)) $elem.data(NAMESPACE).destroy();
    $elem.data(NAMESPACE, new SimpleMultiSelect($elem, options));
  });  
};

$.SimpleMultiSelect = SimpleMultiSelect;
