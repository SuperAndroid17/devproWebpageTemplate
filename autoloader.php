<?php
/*
 * Autoloader
 * Lädt die benötigten Klassen für Objekte um nicht immer manuell alle Klassen einbinden zu müssen.
 */
function __autoload ($klasse) {
  // die bösesten zeichen in klassennamen mal sicherheitshalber verbieten
  if (strpos ($klasse, '.') !== false || strpos ($klasse, '/') !== false
      || strpos ($klasse, '\\') !== false || strpos ($klasse, ':') !== false) {
    return;
  }
  // Main INDEX
  if (file_exists ('Engine/Class/'.$klasse.'.php')) {
    include_once 'Engine/Class/'.$klasse.'.php';
  }
  // Engine/Api
  if (file_exists ('../Class/'.$klasse.'.php')) {
    include_once '../Class/'.$klasse.'.php';
  }
}
 
