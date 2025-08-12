<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME"),
	"DESCRIPTION" =>  GetMessage("DESCRIPTION"),
	"ICON" => "/images/news_line.gif",
	"PATH" => array(
		"ID" => "otus",
	),

	"AREA_BUTTONS" => array(
		array(
			'URL' => "javascript:alert('Это действие приведет к очистке кэш, Вы точно хотите это сделать?');",
			'SRC' => '/images/button.jpg',
			'TITLE' => "Очистить кэш!"
		),
	),
	"CACHE_PATH" => "Y",
);
