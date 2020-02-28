<?php

namespace Alograg;


/**
 * Class StrTools
 * @package Alograg
 */
class StrTools
{
  /**
   * Expresión regular para correos
   */
  const EMAIL_REGEX = '/^[\w\.=-]+@[\w\.-]+\.[\w]{2,3}$/i';
  /**
   * Expresión regular para nombres de host
   */
  const HOST_REGEX = '/^[\w\.-]+\.[\w]{2,3}$/i';

  /**
   * @param $string
   *
   * @return string
   */
  public static function abreviad($string)
  {
    $omitir = self::$vocablos;
    $string = str_replace(
      explode(',', 'á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ'),
      explode(',', 'a,e,i,o,u,n,a,e,i,o,u,n'),
      $string
    );
    $string = str_replace($omitir, ' ', $string);
    $words = str_word_count($string, 1);
    $romanos = array_filter($words, 'Modules\Thinking\Tools\RomanNumber::isRoman');
    if (count($romanos)) {
      $string = str_replace($romanos, '', $string);
      $words = str_word_count($string, 1);
    }
    $expr = '/(?<=\s|^)[a-z]/i';
    if (count($words) > 1) {
      preg_match_all($expr, strtolower($string), $matches);
      $abbreviations = implode('', $matches[0]);
    } else {
      $abbreviations = substr($string, 0, 3);
    }
    if (count($romanos)) {
      array_walk(
        $romanos,
        function (&$item) {
          $item = RomanNumber::int($item);
        }
      );
      $abbreviations .= implode('', $romanos);
    }

    return strtoupper($abbreviations);
  }

  /**
   * @param $string
   *
   * @return string
   */
  public static function decodeId($string)
  {
    return base_convert($string, 36, 10);
  }

  /**
   **
   * Evalúa un string para convertirlo en eun dato
   *
   * @param  string  $value  El texto a evaluar
   *
   * @return mixed|string
   */
  public static function evalString($value)
  {
    if (is_string($value)) {
      $value = urldecode($value);
      if ($value != "") {
        $allow = [
          "false",
          "FALSE",
          "true",
          "TRUE",
          "null",
          "NULL",
        ];
        $isNumber = preg_match(
          '/^[0-9\\.]+$/i',
          $value
        );
        if ($value[0] == '[' || $value[0] == '{' || in_array($value, $allow) || $isNumber) {
          $value = json_decode($value);
        }
      }
    }

    return $value;
  }

  /**
   * @param $string
   *
   * @return array|bool
   */
  public static function extractEmails($string)
  {
    if (preg_match(self::EMAIL_REGEX, $string, $emails)) {
      return $emails;
    }

    return false;
  }

  /**
   * @return string
   */
  public static function routeNameToCssClass()
  {
    $name = \Route::currentRouteName();
    $class = explode('.', $name);
    $class[] = self::dotToSnake($name);

    return implode(' ', array_unique($class));
  }

  /**
   * @param $str
   *
   * @return mixed
   */
  public static function dotToSnake($str)
  {
    return str_replace('.', '-', $str);
  }

  /**
   * @param $str
   *
   * @return mixed
   */
  public static function slashToDot($str)
  {
    return str_replace(['/', '\\'], '.', $str);
  }

  /**
   * @param $string
   *
   * @return string
   */
  public static function stripAccents($string)
  {
    return strtr(
      utf8_decode($string),
      utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
      utf8_decode('aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY')
    );
  }

  /**
   * @param $email
   *
   * @return bool
   */
  public static function validEmail($email)
  {
    return (boolean) preg_match(self::EMAIL_REGEX, $email);
  }

  /**
   * Busca los campos en un texto y los remplaza por datos de un array
   *
   * @param  array  $data  El elemento que tiene los valores.
   * @param  string  $text  El texto a ser remplazado.
   * @param  boolean  $double  El texto a ser remplazado.
   *
   * @return string El texto con los datos colocados
   */
  public static function substitute(array $data, $text, $double = false)
  {
    $search = [];
    $replace = [];
    $corchetes = [
      $double ? '{{' : '{',
      $double ? '}}' : '}',
    ];
    $data = array_dot($data);
    foreach ($data as $field => $value) {
      if (is_array($value)) {
        continue;
      }
      $search[] = $corchetes[0].$field.$corchetes[1];
      $replace[] = trim((string) $value);
    }

    return str_replace($search, $replace, $text);
  }

  /**
   * @param $full_name
   * @return array
   */
  public static function complexNames($full_name)
  {
    /* separar el nombre completo en espacios */
    $tokens = explode(' ', trim(preg_replace('/[^bcdfghjklmnopqrstuvwxyz ]/i', '_', utf8_decode($full_name))));
    /* arreglo donde se guardan las "palabras" del nombre */
    $names = [];
    /* palabras de apellidos (y nombres) compuetos */
    $special_tokens = [
      'da',
      'de',
      'del',
      'la',
      'las',
      'los',
      'mac',
      'mc',
      'van',
      'von',
      'y',
      'i',
      'san',
      'santa',
      'sa',
      'cv',
    ];
    $prev = "";
    foreach ($tokens as $token) {
      $_token = strtolower($token);
      if (in_array($_token, $special_tokens)) {
        $prev .= "$token ";
      } else {
        $names[] = $prev.$token.(strlen($_token) < 3 ? '%' : '');
        $prev = "";
      }
    }

    return $names;
  }

}
