<?php

require_once '../init.php';

use \CMSx\HTML;

class HTMLTest extends \PHPUnit_Framework_TestCase
{
  function testAttrConvert()
  {
    $exp = array('class' => 'test');
    $this->assertEquals($exp, HTML::AttrConvert('test'), 'Если передан не массив - он становится классом');

    $arr1 = array('two' => 2, 'one' => 1);
    $exp  = array('one' => 1, 'two' => 2);
    $this->assertEquals($exp, HTML::AttrConvert($arr1), 'Атрибуты сортируются по алфавиту');

    $arr2 = array('three' => 3);
    $exp  = array('class' => 'hello', 'one' => 1, 'two' => 2, 'three' => 3);
    $this->assertEquals($exp, HTML::AttrConvert($arr1, 'hello', $arr2), 'Объединение нескольких групп атрибутов');
  }

  /**
   * @dataProvider tags
   */
  function testTag($exp, $args, $info)
  {
    $act = call_user_func_array('CMSx\\HTML::Tag', $args);
    $this->assertEquals($exp, $act, $info);
  }

  function testA()
  {
    $h = 'http://www.ya.ru';
    $a = HTML::A($h);
    $this->assertEquals('<a href="http://www.ya.ru">http://www.ya.ru</a>', $a, 'Ссылка без заданного текста');

    $a = HTML::A($h, 'hello');
    $this->assertEquals('<a href="http://www.ya.ru">hello</a>', $a, 'Ссылка с текстом');

    $a = HTML::A($h, 'hello', 'myclass', '_blank');
    $this->assertSelectCount('a[class=myclass]', true, $a, 'Атрибуты');
    $this->assertSelectCount('a[target=_blank]', true, $a, 'Target');
  }

  function testButton()
  {
    $b = HTML::Button('Hello');
    $this->assertEquals('<button type="button">Hello</button>', $b, 'Просто кнопка');

    $b = HTML::Button('Hello', true);
    $this->assertSelectCount('button[type=submit]', true, $b, 'Кнопка submit');

    $b = HTML::Button('Hello', false);
    $this->assertSelectCount('button[type=reset]', true, $b, 'Кнопка reset');

    $b = HTML::Button('Hello', null, 'myclass');
    $this->assertSelectCount('button[class=myclass]', true, $b, 'Кнопка с атрибутами');
  }

  function testCheckbox()
  {
    $c = HTML::Checkbox('hello');
    $this->assertSelectCount('input[type=checkbox]', true, $c, 'Тип');
    $this->assertSelectCount('input[name=hello]', true, $c, 'Name');
    $this->assertSelectCount('input[value=1]', true, $c, 'Значение по-умолчанию');
    $this->assertSelectCount('input[checked=checked]', false, $c, 'Не отмечен');

    $c = HTML::Checkbox('hello', true, 123, 'myclass');
    $this->assertSelectCount('input[checked=checked]', true, $c, 'Отмечен');
    $this->assertSelectCount('input[value=123]', true, $c, 'Значение');
    $this->assertSelectCount('input[class=myclass]', true, $c, 'Класс');

    $c = HTML::Checkbox('hello', null, null, null, 'Привет');
    $this->assertSelectCount('label input', true, $c, 'Чекбокс завернутый в лейбл');
    $this->assertTrue(strpos($c, 'Привет') > 0, 'Текст лейбла');
  }

  function testCheckboxListing()
  {
    $arr = array(1 => 'one', 2 => 'two');
    $l   = HTML::CheckboxListing($arr, 'myname', 2);
    $this->assertSelectCount('label input[type=checkbox]', 2, $l, '2 чекбокса');
    $this->assertSelectCount('label input[name~=myname]', 2, $l, '2 чексбокса с именем');
    $this->assertSelectCount('label input[checked=checked]', true, $l, '1 чексбокс отмечен');

    $arr = array(0 => '<b>Нет</b>', 1 => 'Да');
    $l   = HTML::CheckboxListing($arr, 'test', null);
    $this->assertSelectCount('input[checked=checked]', false, $l, 'Ни один чекбокс не выбран');

    $s = '<input checked="checked" name="test[]" type="checkbox" value="0" />';
    $l = HTML::CheckboxListing($arr, 'test', 0);
    $this->assertGreaterThan(0, strpos($l, $s), 'Выбран чекбокс Нет');

    $l = HTML::CheckboxListing($arr, 'test', array(1, 0));
    $this->assertSelectCount('input[checked=checked]', 2, $l, 'Выбраны оба чекбокса');

    $arr = array('one', 'two');
    echo $l = HTML::CheckboxListing($arr, 'test', 'two', null, true);
    $this->assertSelectCount('input[value=one]', true, $l, 'Чекбокс с значением one');
    $this->assertSelectCount('input[value=two]', true, $l, 'Чекбокс с значением two');
    $this->assertSelectCount('input[checked=checked]', true, $l, 'Отмечен один чекбокс');
  }

  function testForm()
  {
    $f = HTML::Form('<div></div>');
    $this->assertSelectCount('form[action=] div', true, $f);
    $this->assertSelectCount('form[enctype=multipart/form-data]', true, $f);

    $f = HTML::Form('<div></div>', '/hello/', null, 'myclass');
    $this->assertSelectCount('form[action=/hello/] div', true, $f);
    $this->assertSelectCount('form[class=myclass]', true, $f);

    $f = HTML::Form('<div></div>', '/hello/', 'wtf', array('id' => 'myform'));
    $this->assertSelectCount('form[action=/hello/] div', true, $f);
    $this->assertSelectCount('form[enctype=wtf] div', true, $f);
    $this->assertSelectCount('form[id=myform] div', true, $f);
  }

  function testHidden()
  {
    $h = HTML::Hidden('hello', 123);
    $this->assertSelectCount('input[type=hidden]', true, $h, 'Тип');
    $this->assertSelectCount('input[name=hello]', true, $h, 'Имя');
    $this->assertSelectCount('input[value=123]', true, $h, 'Значение');

    $h = HTML::Hidden('hello', 123, 'myclass');
    $this->assertSelectCount('input[class=myclass]', true, $h, 'Атрибуты');
  }

  function testIMG()
  {
    $i = HTML::IMG('img.img', 10, 20);
    $this->assertSelectCount('img[src=img.img]', true, $i, 'Путь к файлу');
    $this->assertSelectCount('img[width=10]', true, $i, 'Ширина');
    $this->assertSelectCount('img[height=20]', true, $i, 'Высота');
    $this->assertSelectCount('img[alt=]', true, $i, 'Пустой альт');

    $i = HTML::IMG('img.img', null, null, 'hello', 'myclass');
    $this->assertSelectCount('img[alt=hello]', true, $i, 'Заданный альт');
    $this->assertSelectCount('img[class=myclass]', true, $i, 'Атрибуты');
  }

  function testInput()
  {
    $i = HTML::Input('hello', 123);
    $this->assertSelectCount('input[type=text]', true, $i, 'Тип');
    $this->assertSelectCount('input[name=hello]', true, $i, 'Имя');
    $this->assertSelectCount('input[value=123]', true, $i, 'Значение');

    $i = HTML::Hidden('hello', 123, 'myclass');
    $this->assertSelectCount('input[class=myclass]', true, $i, 'Атрибуты');
  }

  function testOption()
  {
    $o = HTML::Option('');
    $this->assertEquals('<option value=""></option>', $o, 'Пустой option');

    $o = HTML::Option('Hello');
    $this->assertEquals('<option value="Hello">Hello</option>', $o, 'Только значение');

    $o = HTML::Option('hi', 'Hello');
    $this->assertEquals('<option value="hi">Hello</option>', $o, 'Значение и название');

    $o = HTML::Option('hi', 'Hello', true);
    $this->assertEquals('<option selected="selected" value="hi">Hello</option>', $o, 'Выбранный пункт');
  }

  function testOptionListing()
  {
    $arr = array('one', 'two');
    $ol  = HTML::OptionListing($arr, 'two', null, true);
    $exp = '<option value="one">one</option>' . "\n" . '<option selected="selected" value="two">two</option>' . "\n";
    $this->assertEquals($exp, $ol, 'Список по значениям');

    $arr = array(1 => 'one', 2 => 'two');
    $ol  = HTML::OptionListing($arr, 2, 'choose');
    $this->assertSelectCount('optgroup[label=choose] option', 2, $ol, 'OPTGROUP с 2 опциями');
    $this->assertSelectCount('option[value=1]', true, $ol, 'Опции по ключ-значению 1');
    $this->assertSelectCount('option[value=2]', true, $ol, 'Опции по ключ-значению 2');
    $this->assertGreaterThan(0, strpos($ol, '<option selected="selected" value="2">two</option>'), 'Выделен пункт №2');

    $arr = array(0 => 'Нет', 1 => 'Да');

    $ol = HTML::OptionListing($arr, null);
    $this->assertSelectCount('option[selected=selected]', false, $ol, 'Никакой пункт не выбран');

    $ol = HTML::OptionListing($arr, 0);
    $this->assertSelectCount('option[selected=selected]', true, $ol, 'Выбран пункт Нет');
  }

  function testPassword()
  {
    $i = HTML::Password('hello', 123);
    $this->assertSelectCount('input[type=password]', true, $i, 'Тип');
    $this->assertSelectCount('input[name=hello]', true, $i, 'Имя');
    $this->assertSelectCount('input[value=123]', true, $i, 'Значение');

    $i = HTML::Password('hello', 123, 'myclass');
    $this->assertSelectCount('input[class=myclass]', true, $i, 'Атрибуты');
  }

  function testRadio()
  {
    $c = HTML::Radio('hello');
    $this->assertSelectCount('input[type=radio]', true, $c, 'Тип');
    $this->assertSelectCount('input[name=hello]', true, $c, 'Name');
    $this->assertSelectCount('input[value=1]', true, $c, 'Значение по-умолчанию');
    $this->assertSelectCount('input[checked=checked]', false, $c, 'Не отмечен');

    $c = HTML::Radio('hello', true, 123, 'myclass');
    $this->assertSelectCount('input[checked=checked]', true, $c, 'Отмечен');
    $this->assertSelectCount('input[value=123]', true, $c, 'Значение');
    $this->assertSelectCount('input[class=myclass]', true, $c, 'Класс');

    $c = HTML::Radio('hello', null, null, null, 'Привет');
    $this->assertSelectCount('label input', true, $c, 'Радиобатон завернутый в лейбл');
    $this->assertTrue(strpos($c, 'Привет') > 0, 'Текст лейбла');
  }

  function testRadioListing()
  {
    $arr = array(1 => 'one', 2 => 'two');
    $l   = HTML::RadioListing($arr, 'myname', 2);
    $this->assertSelectCount('label input[type=radio]', 2, $l, '2 радиобатона');
    $this->assertSelectCount('label input[name=myname]', 2, $l, '2 радиобатона с именем');
    $this->assertSelectCount('label input[checked=checked]', true, $l, '1 радиобатон отмечен');

    $arr = array(0 => 'Нет', 1 => 'Да');
    $l   = HTML::RadioListing($arr, 'test');
    $this->assertSelectCount('input[checked=checked]', false, $l, 'Ни один радиобатон не выбран');

    $arr = array('one', 'two');
    echo $l = HTML::RadioListing($arr, 'test', 'two', null, true);
    $this->assertSelectCount('input[value=one]', true, $l, 'Радиобатон с значением one');
    $this->assertSelectCount('input[value=two]', true, $l, 'Радиобатон с значением two');
    $this->assertSelectCount('input[checked=checked]', true, $l, 'Отмечен один чекбокс');
  }

  function testSelect()
  {
    $s = HTML::Select(HTML::Option('hello'), 'myname');
    $this->assertSelectCount('select[name=myname] option[value=hello]', true, $s, 'Простой селект');

    $s = HTML::Select(array(1 => 'Привет', 2 => 'Пока'), 'hello', 2, 'myclass', 'Выбор');
    $this->assertSelectCount('select[class=myclass]', true, $s, 'Атрибуты');
    $this->assertGreaterThan(0, strpos($s, '<option value="">Выбор</option>'), 'Заголовок');
    $this->assertGreaterThan(0, strpos($s, '<option value="1">Привет</option>'), 'Первый пункт не выбран');
    $this->assertGreaterThan(
      0, strpos($s, '<option selected="selected" value="2">Пока</option>'), 'Второй пункт выбран'
    );
  }

  function testTextarea()
  {
    $t = HTML::Textarea('hello', 'Hi');
    $this->assertEquals('<textarea name="hello">Hi</textarea>', $t, 'Пустое поле');

    $t = HTML::Textarea('hello', 'bla bla', 'myclass', 10, 20);
    $this->assertSelectCount('textarea[class=myclass]', true, $t, 'Атрибуты');
    $this->assertSelectCount('textarea[rows=10]', true, $t, 'Ряды');
    $this->assertSelectCount('textarea[cols=20]', true, $t, 'Столбцы');
  }

  function tags()
  {
    // $tag, $value = null, $attr = null, $single = false
    return array(
      array('<b></b>', array('b'), 'Пустой двойной тег'),
      array('<b>Hello</b>', array('b', 'Hello'), 'Двойной тег с текстом'),
      array('<b class="myclass">Hello</b>', array('b', 'Hello', 'myclass'), 'Двойной тег с текстом и классом'),
      array('<b id="my-id">Hello</b>', array('b', 'Hello', array('id' => 'my-id')), 'Двойной тег с текстом и атрибутами'),
      array('<br />', array('br', null, null, true), 'Пустой одинарный тег'),
      array('<input value="myval" />', array('input', 'myval', null, true), 'Одинарный тег с значением'),
    );
  }
}