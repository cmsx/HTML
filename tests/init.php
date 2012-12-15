<?php

/**
 * Файл инициализации для запуска тестов
 */

spl_autoload_register(
  function ($class) {
    require_once __DIR__.'/../src/' . str_replace('\\', DIRECTORY_SEPARATOR, trim($class, '\\')) . '.php';
  }
);