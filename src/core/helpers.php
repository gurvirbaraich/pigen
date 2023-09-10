<?php

/**
 * The function converts an array to an object in PHP.
 * 
 * @param array array The parameter "array" is an array that you want to convert into an object.
 * 
 * @return stdClass an object of the stdClass class.
 */
function arrayToObject(array $array): stdClass
{
  if (!is_array($array)) {
    return $array;
  }

  return json_decode(
    json_encode(
      $array
    )
  );
}


/**
 * The function "concatenate" takes in multiple strings as arguments and returns a single string by
 * concatenating them together.
 * 
 * @return string a string that is the concatenation of all the input strings.
 */
function concatenate(...$strings): string {
  return implode('', $strings);
}
