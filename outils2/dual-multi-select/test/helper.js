export function keydown(elem, code, ctrl = false, shift = false) {
  $(elem).trigger($.Event('keydown', { keyCode: code, ctrlKey: ctrl, shiftKey: shift }));
}
