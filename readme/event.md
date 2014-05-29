Модуль управления событиями
=============

###### Оповестить приложения о событии
<pre><code> 
//создали событие   
$event = new \sfEvent(null, 'APP_MODULES_INIT');
//оповестили приложение
\Meerkat\Event\Event::dispatcher()->notify($event);

//...............................
//где-то в приложении делаем множество обработчиков события, реагирующих на него
\Meerkat\Event\Event::dispatcher()->connect('APP_MODULES_INIT', function (sfEvent $event) {
	print "Наступило событие APP_MODULES_INIT";
});

</code></pre>

###### Событие-фильтр
<pre><code> 
//создали событие   
$event = new \sfEvent(null, 'INIT_MENU_ITEMS');
//оповестили приложение
\Meerkat\Event\Event::dispatcher()->filter($event, array());
$ret = $event->getReturnValue();
var_dump($ret);
/*
array(2) {
  ["news"]=>
  string(7) "Новости"
  ["blog"]=>
  string(4) "Блог"
}*/

//...............................
//где-то в приложении делаем множество обработчиков события, изменяющих начальный параметр
\Meerkat\Event\Event::dispatcher()->connect('INIT_MENU_ITEMS', function (sfEvent $event, $param) {
	$param['news'] = 'Новости';
	return $param;
});
//где-то в приложении делаем множество обработчиков события, изменяющих начальный параметр
\Meerkat\Event\Event::dispatcher()->connect('INIT_MENU_ITEMS', function (sfEvent $event, $param) {
	$param['blog'] = 'Блог';
	return $param;
});

</code></pre>