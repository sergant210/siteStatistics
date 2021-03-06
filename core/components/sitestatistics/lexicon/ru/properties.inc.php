<?php

$_lang['sitestatistics_prop_countby'] = 'Режим подсчета статистики: "" - показывает общие данные, "day" - показывает данные за день, "month" - данные за месяц, "year" - данные за год. Может работать в паре с параметром "date". Если "date" не указан, то берется текущее значение даты/месяца/года соответственно.';
$_lang['sitestatistics_prop_date'] = 'Дата для вывода статистики. Если не указана, то берется текущая. Формат зависит от параметра "countby": для "day" - "YYYY-mm-dd", для "month" - "YYYY-mm", для "year" - "YYYY".';
$_lang['sitestatistics_prop_mode'] = 'Режим вывода статистики: "page" - статистика ресурса, "site" - статистика сайта.';
$_lang['sitestatistics_prop_resource'] = 'ID ресурса для вывода статистики. Если не указан, берется ID текущего.';
$_lang['sitestatistics_prop_show'] = 'Указывает какой элемент статистики выводить - просмотры (views) или посещения (users).';
$_lang['sitestatistics_prop_toPlaceholders'] = 'Если "да", то результат будет сохранен в плейсхолдеры [[+stat.views]] и [[+stat.users]], вместо прямого вывода на странице.';
$_lang['sitestatistics_prop_tpl'] = 'Чанк для вывода статистики.';

$_lang['siteonlineusers_prop_ctx'] = 'Контекст, в котором нужно считать пользователей. Если не указан, то считается во всех контекстах.';
$_lang['siteonlineusers_prop_toPlaceholder'] = 'Если указан этот параметр, то результат будет сохранен в указанный плейсхолдер.';
$_lang['siteonlineusers_prop_tpl'] = 'Чанк для вывода информации о количестве текущих пользователей (короткий режим).';
$_lang['siteonlineusers_prop_tplItem'] = 'Чанк для вывода списка текущих пользователей (для полного режима).';
$_lang['siteonlineusers_prop_fullMode'] = 'Режим вывода информации о текущих пользователях - короткий (false) или полный (true).';
