meerkat-ajax
============

Модуль AJAX для MeerkatCMF (on Kohana 3.3)

##### рекомендуется делать отдельный контроллер на каждый AJAX запрос
поместите это в init.php своего модуля
~~~
\Meerkat\Route\Route::factory(Kohana::$config->load('meerkat/ajax.url') . 'users/info')
	->controller('User')
	->directory('Ajax')
	->put();
~~~
а затем создайте контроллер
~~~
class Controller_Ajax_User extends \Meerkat\Base\Controller{
	function action_index(){
		//получение html представления пользователя, например "<a href="/users/aberdnikov">@aberdnikov</a>"
		$ret= get_user();
		\Meerkat\Ajax\Ajax::response_html($ret);
	}
}
~~~

Затем можно будет обращаться к контроллеру по ссылке: http://site.com/!/ajax/users/info

### Все возможности AJAX-менеджера 

###### HTML
**Content-Type: text/html; charset=UTF-8**
~~~
\Meerkat\Ajax\Ajax::response_html('<a href="/users/aberdnikov">@aberdnikov</a>');
~~~
- - -
###### TEXT
**Content-Type: text/plain; charset=UTF-8**
~~~
\Meerkat\Ajax\Ajax::response_text("text");
~~~
- - -
###### JSON
**Content-Type: application/json; charset=UTF-8**

{"name":"@username"}
~~~
\Meerkat\Ajax\Ajax::response_json(array("name"=>"@username"));
~~~
- - -
####### XML
**Content-Type: application/xml**
~~~
$xml='<?xml version="1.0"?>
<recipe name="хлеб" preptime="5" cooktime="180">
  <title>Простой хлеб</title>
  <ingredient amount="3" unit="стакан">Мука</ingredient>
  <ingredient amount="0.25" unit="грамм">Дрожжи</ingredient>
  <ingredient amount="1.5" unit="стакан">Тёплая вода</ingredient>
  <ingredient amount="1" unit="чайная ложка">Соль</ingredient>
  <instructions>
   <step>Смешать все ингредиенты и тщательно замесить.</step>
   <step>Закрыть тканью и оставить на один час в тёплом помещении.</step>
   <step>Замесить ещё раз, положить на противень и поставить в духовку.</step>
   <step>Посетить сайт webi.ru</step>
  </instructions>
</recipe>';
\Meerkat\Ajax\Ajax::response_xml($xml);
~~~
- - -
####### JAVASCRIPT
**Content-Type: application/x-javascript; charset=UTF-8**
~~~
\Meerkat\Ajax\Ajax::response_javascript("alert('123');");
~~~
