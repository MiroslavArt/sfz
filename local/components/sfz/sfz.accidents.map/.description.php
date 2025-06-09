<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
   "NAME" => GetMessage("COMP_NAME"),
   "DESCRIPTION" => GetMessage("COMP_DESCR"),
   //"ICON" => "",
   "PATH" => array(
      "ID" => "sfz",
      "NAME" => GetMessage("COMP_GROUP_NAME"),
      //"CHILD" => array(
      //   "ID" => "catalog",
      //   "NAME" => "Каталог товаров"
      //)
   ),
   "AREA_BUTTONS" => array(
      /*array(
         'URL' => "javascript:alert('Это кнопка!!!');",
         'SRC' => '/images/button.jpg',
         'TITLE' => "Это кнопка!"
      ),*/
   ),
   "CACHE_PATH" => "Y",
   "COMPLEX" => "N"
);
?>