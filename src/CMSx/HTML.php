<?php

namespace CMSx;

abstract class HTML
{
  /**
   * Формирование ссылки
   */
  public static function A($href, $text = null, $attr = null, $target = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['href'] = $href;
    if (is_null($text)) {
      $text = $href;
    }
    if (!is_null($target)) {
      $attr['target'] = $target;
    }

    return self::Tag('a', $text, $attr);
  }

  /**
   * Формировалка кнопок
   * $onclick: true = submit, false = reset, string = onclick
   */
  public static function Button($title, $onclick = null, $attr = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['type'] = 'button';
    if ($onclick === true) {
      $attr['type'] = 'submit';
    } elseif ($onclick === false) {
      $attr['type'] = 'reset';
    } elseif (!is_null($onclick)) {
      $attr['onclick'] = $onclick;
    }

    return self::Tag('button', $title, $attr);
  }

  /**
   * Отдельно стоящий чекбокс
   */
  public static function Checkbox($name, $checked = null, $value = null, $attr = null, $label = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['type'] = 'checkbox';
    $attr['name'] = $name;
    if ($checked) {
      $attr['checked'] = 'checked';
    }
    $html = self::Tag('input', (!is_null($value) ? $value : 1), $attr, true);
    if (!empty ($label)) {
      return self::Tag('label', $html . ' ' . $label);
    } else {
      return $html;
    }
  }

  /**
   * Список радиобатонов
   * $separate - как разделять чекбоксы: false - ничем, true - <br>, или своё
   */
  public static function CheckboxListing($arr, $name, $selected = null, $separate = null)
  {
    $tmp = '';
    if (!is_array($selected)) {
      $selected = array($selected);
    }
    foreach ($arr as $val => $title) {
      $tmp .= self::Checkbox($name . '[]', in_array($val, $selected), $val, null, $title);
      if ($separate) {
        $tmp .= $separate === true ? "<br />" : $separate;
      }
      $tmp .= "\n";
    }

    return $tmp;
  }

  /**
   * Формирование формы
   */
  public static function Form($value, $action = null, $enctype = null, $attr = null)
  {
    $attr = self::AttrConvert($attr);
    if (!isset ($attr['action'])) {
      $attr['action'] = !is_null($action) ? $action : '';
    }
    if (!isset ($attr['enctype'])) {
      $attr['enctype'] = !is_null($enctype) ? $enctype : 'multipart/form-data';
    }
    if (!isset ($attr['method'])) {
      $attr['method'] = 'POST';
    }

    return self::Tag('form', $value, $attr, false, true);
  }

  /**
   * Скрытое поле
   */
  public static function Hidden($name, $value, $attr = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['type'] = 'hidden';
    $attr['name'] = $name;

    return self::Tag('input', $value, $attr, true);
  }

  /**
   * Формирование тега IMG
   */
  public static function IMG($src, $width = null, $height = null, $alt = null, $attr = null)
  {
    $attr        = self::AttrConvert($attr);
    $attr['src'] = $src;
    if ($width) {
      $attr['width'] = $width;
    }
    if ($height) {
      $attr['height'] = $height;
    }
    $attr['alt'] = $alt;

    return self::Tag('img', null, $attr, true);
  }

  /**
   * Поле для ввода
   */
  public static function Input($name, $value, $attr = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['type'] = 'text';
    $attr['name'] = $name;

    return self::Tag('input', $value, $attr, true);
  }

  /**
   * Пункт выбиралки Select
   */
  public static function Option($value, $title = null, $selected = null)
  {
    $attr = array('value' => $value);
    if ($selected) {
      $attr['selected'] = 'selected';
    }
    if (is_null($title)) {
      $title = $value;
    }

    return self::Tag('option', $title, $attr);
  }

  /**
   * Список OPTION`ов
   */
  public static function OptionListing($arr, $selected = null, $optgroup = null, $onlyvalues = false)
  {
    $tmp = '';
    if (!is_array($selected)) {
      $selected = array($selected);
    }
    foreach ($arr as $key => $val) {
      if (is_array($val)) {
        $tmp .= self::OptionListing($val, $selected, $key);
      } else {
        if ($onlyvalues) {
          $key = $val;
        }
        $tmp .= self::Option($key, $val, in_array($key, $selected)) . "\n";
      }
    }
    if ($optgroup) {
      $tmp = self::Tag('optgroup', $tmp, array('label' => $optgroup));
    }

    return $tmp;
  }

  /**
   * Скрытое поле
   */
  public static function Password($name, $value, $attr = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['type'] = 'password';
    $attr['name'] = $name;

    return self::Tag('input', $value, $attr, true);
  }

  /**
   * Отдельно стоящий радиобатон
   */
  public static function Radio($name, $checked = null, $value = null, $attr = null, $label = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['type'] = 'radio';
    $attr['name'] = $name;
    if ($checked) {
      $attr['checked'] = 'checked';
    }
    $html = self::Tag('input', (!is_null($value) ? $value : 1), $attr, true);
    if (!empty ($label)) {
      return self::Tag('label', $html . ' ' . $label);
    } else {
      return $html;
    }
  }

  /**
   * Список радиобатонов
   * $separate - как разделять чекбоксы: false - ничем, true - <br>, или своё
   */
  public static function RadioListing($arr, $name, $selected = null, $separate = null)
  {
    $tmp = '';
    if (!is_array($selected)) {
      $selected = array($selected);
    }
    foreach ($arr as $val => $title) {
      $tmp .= self::Radio($name, in_array($val, $selected), $val, null, $title);
      if ($separate) {
        $tmp .= $separate === true ? "<br />" : $separate;
      }
      $tmp .= "\n";
    }

    return $tmp;
  }

  /**
   * Выбиралка SELECT
   */
  public static function Select($mixed, $name, $selected = null, $attr = null, $header = null, $multiple = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['name'] = $name;
    if ($multiple) {
      $attr['multiple'] = 'multiple';
    }
    if (is_array($mixed)) {
      $mixed = self::OptionListing($mixed, $selected);
    }
    if ($header) {
      $mixed = self::Option('', $header) . "\n" . $mixed;
    }

    return self::Tag('select', $mixed, $attr, false, true);
  }

  /**
   * Создание HTML тэга
   *
   * @param      $tag    - имя тега
   * @param null $value  - значение или содержимое атрибута value
   * @param null $attr   - массив или строка (будет задан как класс)
   * @param bool $single - флаг тег одиночный
   *
   * @return string
   */
  public static function Tag($tag, $value = null, $attr = null, $single = false, $nl = false)
  {
    $attr = self::AttrConvert($attr);
    if (!is_null($value) && $single) {
      $attr['value'] = $value;
    }
    $tmp = '';
    foreach ($attr as $key => $val) {
      $tmp .= ' ' . $key . '="' . $val . '"';
    }
    $attr = $tmp;

    return '<' . $tag . $attr . ($single ? ' />' : ">" . ($nl ? "\n" : '') . $value . '</' . $tag . '>');
  }

  /**
   * TEXTAREA
   */
  public static function Textarea($name, $value = null, $attr = null, $rows = null, $cols = null)
  {
    $attr         = self::AttrConvert($attr);
    $attr['name'] = $name;
    if (!is_null($rows)) {
      $attr['rows'] = $rows;
    }
    if (!is_null($cols)) {
      $attr['cols'] = $cols;
    }

    return self::Tag('textarea', $value, $attr);
  }

  /**
   * Приведение атрибутов тега к одному виду
   * Если передана строка - она преобразуется в атрибут class
   * Можно передать несколько атрибутов - они будут сведены в один массив
   *
   * @return array
   */
  public static function AttrConvert($attr, $_ = null)
  {
    $args = func_get_args();
    if (count($args) > 1) {
      $attr = array();
      foreach ($args as $a) {
        $attr = array_merge($attr, self::AttrConvert($a));
      }
    } else {
      if (is_null($attr)) {
        $attr = array();
      } elseif (!is_array($attr)) {
        $attr = array('class' => (string)$attr);
      }
    }

    ksort($attr);
    return $attr;
  }
}