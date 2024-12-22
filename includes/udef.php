<?php
function udef($var, $mode = 'GET', $exit = true)
{
  echo "Undefined $mode [$var], $_SERVER[REQUEST_URI]";
  if ($exit) exit;
}
