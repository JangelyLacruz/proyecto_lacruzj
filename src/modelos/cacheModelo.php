<?php

namespace src\modelos;

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use src\config\connect\conexion;

class cacheModelo extends conexion {
  private $objCache;
  private string $archivoBloqueo;
  private $mutex;

  public function __construct() {
    CacheManager::setDefaultConfig(new ConfigurationOption([
      'path' => $_SERVER['DOCUMENT_ROOT'] . '/proyecto-lacruz-j/src/config/connect/cache',
      'defaultTtl' => 60 * 60 * 24 * 365 * 1,
    ]));
    $this->objCache = CacheManager::getInstance('files');
  }
  public function getItem(array|string $clave) {
    $hash = $this->hashearClave($clave);
    try {
      $this->archivoBloqueo = $hash['archivoMutex'];
      $this->mutexLectura();
      $cacheString = $this->objCache->getItem($hash['clave']);
      if (!$cacheString->isHit()) {
        return false;
      }
      return $cacheString->get();
    } finally {
      $this->liberarMutex();
    }
  }
  public function setItem(array|string $clave, array|string $valor) {
    $hash = $this->hashearClave($clave);
    try {
      $this->archivoBloqueo = $hash['archivoMutex'];
      $this->mutexEscritura();
      $cacheString = $this->objCache->getItem($hash['clave']);
      $cacheString->set($valor);
      $this->objCache->save($cacheString);
    } finally {
      $this->liberarMutex();
    }
    return true;
  }
  public function removeItem(array|string $clave) {

    $hash = $this->hashearClave($clave);
    try {
      $this->archivoBloqueo = $hash['archivoMutex'];
      $this->mutexEscritura();
      $this->objCache->deleteItem($hash['clave']);
    } finally {
      $this->liberarMutex();
    }
  }
  public function hashearClave(array|string $clave) {
    if (!is_string($clave)) $clave = json_encode($clave);
    $clave = md5($clave);
    return [
      'clave' => $clave,
      'archivoMutex' => sys_get_temp_dir() . '/mutex_' . $clave . '.lock'
    ];
  }
  public function mutexLectura() {
    $this->mutex = fopen($this->archivoBloqueo, 'c');
    flock($this->mutex, LOCK_SH);
  }
  public function mutexEscritura() {
    $this->mutex = fopen($this->archivoBloqueo, 'c');
    flock($this->mutex, LOCK_EX);
  }
  public function liberarMutex() {
    if ($this->mutex) {
      flock($this->mutex, LOCK_UN); // Quita el bloqueo
      fclose($this->mutex);
      $this->mutex = null;
    }
  }
}
