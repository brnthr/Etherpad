# jquery-simple-multi-select

A jquery plugin for multiple select.

## Dependencies

* jquery

## Installation

Install from npm:

    $ npm install @kanety/jquery-simple-multi-select --save

## Usage

Build two select elements as follows:

```html
<div id="select">
  <div>
    <select id="src" multiple>
      <option value="option1">option1</option>
      <option value="option2">option2</option>
      <option value="option3">option3</option>
      <option value="option4">option4</option>
      <option value="option5" disabled>option5</option>
    </select>
  </div>
  <div>
    <div><button type="button" id="adder"></button></div>
    <div><button type="button" id="remover"></button></div>
  </div>
  <div>
    <select id="dst" multiple></select>
  </div>
</div>
```

Then run:

```javascript
$('#select').simpleMultiSelect({
  source: '#src',
  destination: '#dst',
  adder: '#adder',
  remover: '#remover'
});
```

### Options

Set max number of addable options:

```javascript
$('#select').simpleMultiSelect({
  ...
  maxOptions: 2
});
```

Enable sorting when options moved:

```javascript
$('#select').simpleMultiSelect({
  ...
  sortOptions: true
});
```

### Callbacks

```javascript
$('#select').simpleMultiSelect({
  ...
}).on('option:added', function(e, $option) {
  ...
}).on('option:removed', function(e, $option) {
  ...
});
```

## License

The library is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).
