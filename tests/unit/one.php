<?php

require_once '../init.php';

use \CMSx\HTML;

class HTMLTest extends \PHPUnit_Framework_TestCase
{
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

    echo $c = HTML::Checkbox('hello', null, null, null, 'Привет');
    $this->assertSelectCount('label input', true, $c, 'Чекбокс завернутый в лейбл');
    $this->assertTrue(strpos($c, 'Привет') > 0, 'Текст лейбла');
  }

  function testCheckboxListing()
  {
    $arr = array(1 => 'one', 2 => 'two');
    $l = HTML::CheckboxListing($arr, 'myname', 2);
    $this->assertSelectCount('label input[type=checkbox]', 2, $l, '2 чекбокса');
    $this->assertSelectCount('label input[name~=myname]', 2, $l, '2 чексбокса с именем');
    $this->assertSelectCount('label input[checked=checked]', true, $l, '1 чексбокс отмечен');
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
    $this->assertEquals('<option value="hi" selected="selected">Hello</option>', $o, 'Выбранный пункт');
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

    echo $c = HTML::Radio('hello', null, null, null, 'Привет');
    $this->assertSelectCount('label input', true, $c, 'Радиобатон завернутый в лейбл');
    $this->assertTrue(strpos($c, 'Привет') > 0, 'Текст лейбла');
  }

  function testRadioListing()
  {
    $arr = array(1 => 'one', 2 => 'two');
    $l = HTML::RadioListing($arr, 'myname', 2);
    $this->assertSelectCount('label input[type=radio]', 2, $l, '2 радиобатона');
    $this->assertSelectCount('label input[name=myname]', 2, $l, '2 радиобатона с именем');
    $this->assertSelectCount('label input[checked=checked]', true, $l, '1 радиобатон отмечен');
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
      0, strpos($s, '<option value="2" selected="selected">Пока</option>'), 'Второй пункт выбран'
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