Das Cryptor Addon stellt Methoden zur Zweiweg-Verschlüsselung von Daten zur Verfügung. 

- [Beispiel: Strings verschlüsseln](#beispiel1)
- [Beispiel: Arrays verschlüsseln](#beispiel2)
- [Beispiel: Arrays mit Key verschlüsseln](#beispiel3)
- [Beispiel: Mehrdimensionale Arrays verschlüsseln](#beispiel4)
- [Beispiel: Objekte verschlüsseln](#beispiel5)
- [Beispiel: Encryption key als zweiten Parameter übergeben](#beispiel6)
- [Helper Methoden](#helper)

### 
<a name="ueber"></a>

<a name="beispiel1"></a>
## Code-Beispiele

### Beispiel: Strings verschlüsseln

```php 
<?php 
  // String verschlüsseln 
  $string = cryptor::encrypt('Mein zu verschlüsselender String');
  echo $string;

  // Ausgabe:
  // sRnGJBj28LvDS0CT1dLkw9z5JmiTLcR9BOWC7B1M2LrSCvXzknTgJ9auo2Q/5ekVbq83qKlgmhHTsPy03HZj57jrxPLo846Ccr2z0qZBb7Fb

  // String entschlüsseln 
  $cryptedString = cryptor::decrypt('sRnGJBj28LvDS0CT1dLkw9z5JmiTLcR9BOWC7B1M2LrSCvXzknTgJ9auo2Q/5ekVbq83qKlgmhHTsPy03HZj57jrxPLo846Ccr2z0qZBb7Fb');
  echo $string;

  // Ausgabe:
  // Mein zu verschlüsselender String
```
<a name="beispiel2"></a>
### Beispiel: Arrays verschlüsseln

```php 
<?php 
  // Eindimensionale Arrays verschlüsseln 
  $array = cryptor::encrypt(['Mein Array Wert 1', 'Mein Array Wert 2']);
  print_r($array);

  // Ausgabe:
  Array
  (
    [0] => 76L0+YhFGWRY3fv2Mwn/iPEL1tacHPKi5OCE5blVJnDEmeLqD13T5QE42Jwd6BSsiu3rhMpAIY7Uuw4+9Q4aXkc=
    [1] => aVVw2+Vg9mw5hb9E0d9n20FrsHc+0xh0DrsuWreCFuIbTnN5iMn/Pa5k2CCyM/pByswGJBXf4ZRyoCtCeIa4z+w=
  )

  // Array entschlüsseln 
  $array = cryptor::decrypt([
    '74NfV19pQSbqFlxgtEmWnRBYDwzDX3IQYDBjQoRlr6fcwk29qOmJQG1hx3gkDCoTVxzN+ITpy11eljevrE/d7NU=', 
    '2XQpzsunYxcxQavuuK8kxyvWokLFnF4s+mXvGc9zzuy86v5raDpCt3WZW4ARNT29a15Tyff0jVwxYen7kKc3EEs='
  ]);
  print_r($array);

  // Ausgabe:
  Array
  (
    [0] => Mein Array Wert 1
    [1] => Mein Array Wert 2
  )

```
<a name="beispiel3"></a>
### Beispiel: Arrays verschlüsseln (mit Array Keys)

```php 
<?php 
  
  // Eindimensionale Arrays verschlüsseln (mit array key)
  $array = cryptor::encrypt([
    'mein Key 1' => 'Mein Array Wert 1', 
    'mein Key 2' => 'Mein Array Wert 2'
  ]);
  print_r($array);

  // Ausgabe:
  // Array
  // (
  //   [mein Key 1] => 76L0+YhFGWRY3fv2Mwn/iPEL1tacHPKi5OCE5blVJnDEmeLqD13T5QE42Jwd6BSsiu3rhMpAIY7Uuw4+9Q4aXkc=
  //   [mein Key 2] => aVVw2+Vg9mw5hb9E0d9n20FrsHc+0xh0DrsuWreCFuIbTnN5iMn/Pa5k2CCyM/pByswGJBXf4ZRyoCtCeIa4z+w=
  // )

  // Array entschlüsseln (mit array key)
  $array = cryptor::decrypt([
    'mein Key 1' => '74NfV19pQSbqFlxgtEmWnRBYDwzDX3IQYDBjQoRlr6fcwk29qOmJQG1hx3gkDCoTVxzN+ITpy11eljevrE/d7NU=', 
    'mein Key 2' => '2XQpzsunYxcxQavuuK8kxyvWokLFnF4s+mXvGc9zzuy86v5raDpCt3WZW4ARNT29a15Tyff0jVwxYen7kKc3EEs='
  ]);
  print_r($array);

  // Ausgabe:
  // Array
  // (
  //   [mein Key 1] => Mein Array Wert 1
  //   [mein Key 2] => Mein Array Wert 2
  // )
```

<a name="beispiel4"></a>
### Beispiel: Mehrdimensionale Arrays verschlüsseln
```php 
<?php 
  
  // Mehrdimensionales Array verschlüsseln 
  $array = cryptor::encrypt([
    'mein Key 1' => 'Mein Array Wert 1', 
    'mein Key 2' => 'Mein Array Wert 2',
    'mein child array' => [
        'a' => 'Mein Child Wert a',
        'b' => 'Mein Child Wert b'
    ]
  ]);
  print_r($array);

  // Ausgabe:
  // Array
  // (
  //   [mein Key 1] => tRlwGlAek1EPeZqfjRdOt+3XKe29Iq1PHb79U46yjCvI+oENr6hAxk6dewmgVIU7bCHHvIGmWBtIDLgS5THpORs=
  //   [mein Key 2] => ZM3IkaFqNkHI3nvCzLNjiVxqO8ahHMgHaS20XoM0QBzetEVpUO8d8qySjRx37Wb1u0vvdEB0x0Ml+5BbMQwp3/E=
  //   [mein child array] => Array
  //       (
  //           [a] => GQYVfBJ2XfLociY+EItaD6zGU+KSHyir34BNJM2N2c9SN5UxvKkXAeB3JoKrrK1YrdnUzv93TaqOnmDWOmS6Sro=
  //           [b] => q4mMweqOGfqR7waAvt5QwYmGnYvZ0JEXQMsbjIv9HF/vhknfCeoKT5ftzV6l4wLHFefPPKSnr+EU7BlbdFLRfs8=
  //       )
  // )

  // Mehrdimensionales Array entschlüsseln
  $array = cryptor::decrypt([
    'mein Key 1' => '74NfV19pQSbqFlxgtEmWnRBYDwzDX3IQYDBjQoRlr6fcwk29qOmJQG1hx3gkDCoTVxzN+ITpy11eljevrE/d7NU=', 
    'mein Key 2' => '2XQpzsunYxcxQavuuK8kxyvWokLFnF4s+mXvGc9zzuy86v5raDpCt3WZW4ARNT29a15Tyff0jVwxYen7kKc3EEs=',
    'mein child array' => [
        'a' => 'GQYVfBJ2XfLociY+EItaD6zGU+KSHyir34BNJM2N2c9SN5UxvKkXAeB3JoKrrK1YrdnUzv93TaqOnmDWOmS6Sro=',
        'b' => 'q4mMweqOGfqR7waAvt5QwYmGnYvZ0JEXQMsbjIv9HF/vhknfCeoKT5ftzV6l4wLHFefPPKSnr+EU7BlbdFLRfs8='
    ]
  ]);
  print_r($array);

  // Ausgabe:
  // Array
  // (
  //   [mein Key 1] => Mein Array Wert 1
  //   [mein Key 2] => Mein Array Wert 2
  //   [mein child array] => Array
  //       (
  //           [a] => Mein Child Wert a
  //           [b] => Mein Child Wert b
  //       )
  // )
```

<a name="beispiel5"></a>
### Beispiel: Objekte verschlüsseln

```php 
<?php 
  
  // Object verschlüsseln 
  $object = new stdClass();
  $object->a = 'Mein Object Wert 1';
  $object->b = 'Mein Object Wert 2';
  $result = cryptor::encrypt($object);
  print_r($result);

  // Ausgabe:
  // stdClass Object
  // (
  //   [a] => IAH+veaCR/bEfG5UV0+zPrjAkk47sLb183A0OpLmNCyMO+7gnHBrywNaz6OrMndQeQZBGrssBAdzOl7c0WuU5TOj
  //   [b] => kM7HLU4ekTU49S9dUr22RPAjMOHPOCObpcEyIf52FeeX+5Jm554mwzQHRxLtC7r+krto/3wwMJZ0DHAXrq79XvLU
  // )

  // Mehrdimensionales Object entschlüsseln
  $object = new stdClass();
  $object->a = 'IAH+veaCR/bEfG5UV0+zPrjAkk47sLb183A0OpLmNCyMO+7gnHBrywNaz6OrMndQeQZBGrssBAdzOl7c0WuU5TOj';
  $object->b = 'kM7HLU4ekTU49S9dUr22RPAjMOHPOCObpcEyIf52FeeX+5Jm554mwzQHRxLtC7r+krto/3wwMJZ0DHAXrq79XvLU';
  $object->c = new stdClass();
  $object->c->a1 = 'GQYVfBJ2XfLociY+EItaD6zGU+KSHyir34BNJM2N2c9SN5UxvKkXAeB3JoKrrK1YrdnUzv93TaqOnmDWOmS6Sro=';
  $object->c->b2 = 'q4mMweqOGfqR7waAvt5QwYmGnYvZ0JEXQMsbjIv9HF/vhknfCeoKT5ftzV6l4wLHFefPPKSnr+EU7BlbdFLRfs8=';
  $result = cryptor::decrypt($object);
  print_r($result);

  // Ausgabe:
  // stdClass Object
  // (
  //   [a] => Mein Object Wert 1
  //   [b] => Mein Object Wert 2
  //   [c] => stdClass Object
  //       (
  //           [a1] => Mein Child Wert a
  //           [b2] => Mein Child Wert b
  //       )
  // )
```

<a name="beispiel6"></a>
### Beispiel: Encryption key als zweiten Parameter übergeben

```php 
<?php 
  // String verschlüsseln 
  $string = cryptor::encrypt('Mein zu verschlüsselender String', 'mitEinemAnderenEncryptionKey');
  echo $string;

  // Ausgabe:
  // UOLwNuVLTISED0FyA/RRDslECjGh8kxaAJtrFgbo19GTfA3HoK5asCfG7VUEm5cb/qqerRQNQ+naK43ihGNOLHv4IfEvQYOZ+SfrbsyXjW7/

  // String entschlüsseln 
  $string = cryptor::decrypt('UOLwNuVLTISED0FyA/RRDslECjGh8kxaAJtrFgbo19GTfA3HoK5asCfG7VUEm5cb/qqerRQNQ+naK43ihGNOLHv4IfEvQYOZ+SfrbsyXjW7/', 'mitEinemAnderenEncryptionKey');
  echo $string;

  // Ausgabe:
  // Mein zu verschlüsselender String
```
<a name="helper"></a>
### Helper methoden

```php 
<?php 
  // Abfrage ob Cipher Methode zur Verfügung steht
  if (cryptor::hasCipher('AES-256-CTR')) {
    // do something
  }

```