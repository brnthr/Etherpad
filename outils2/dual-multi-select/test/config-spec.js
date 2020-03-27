describe('jquery-simple-multi-select', () => {
  it('config', () => {
    let defaults = $.SimpleMultiSelect.getDefaults();
    expect(defaults.test).toEqual(undefined);

    defaults = $.SimpleMultiSelect.setDefaults({test: 'test'});
    expect(defaults.test).toEqual('test');
  });
});
