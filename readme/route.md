Модуль управления роутами для MeerkatCMF (on Kohana 3.3)
=============

Модуль предназначен для того, чтобы делать правильный урл 
<pre><code>"/news/123/edit/"</code></pre>
<pre><code>"/&lt;controller&gt;/&lt;id&gt;/&lt;action&gt;/"</code></pre>
вместо рекомендуемых дефолтных Kohana

<pre><code>
Route::set('default', '(&lt;controller&gt;(/&lt;action&gt;(/&lt;id&gt;)))')
->defaults(array(
    'controller' => 'welcome',
    'action'     => 'index',
));</code></pre>

Помните, как это было у Артемия Лебедева:
http://www.artlebedev.ru/kovodstvo/sections/48/
>Правило: посетитель всегда должен получить осмысленное и предсказуемое содержимое, если он стер часть адреса до ближайшей косой черты.

А что будет, если последовать этому правилу в урл /admin/news/edit/123 ?

Понятно, что надо редактировать, понятно, что новость, но какую?

С использованием же этого модуля все будет логично:

#### Пример использования

<pre><code>
Meerkat\Base\Route::factory('/admin/news')
->controller('News')
->directory('Admin')
->with_item(true)
->put();
</code></pre>
Создаст роуты
<table width="100%">
	<tr>
		<th>Действие</th>
		<th>URL</th>
		<th>Param</th>
		<th>Controller:action</th>
	</tr>
	<tr>
		<td>Показать все новости</td>
		<td>/admin/news/</td>
		<td></td>
		<td>Controller_Admin_News::action_index</td>
	</tr>
	<tr>
		<td>Показать одну новость</td>
		<td>/admin/news/123</td>
		<td>Request::current()->param('id')</td>
		<td>Controller_Admin_News::action_item</td>
	</tr>
	<tr>
		<td>Добавить новость</td>
		<td>/admin/news/add</td>
		<td></td>
		<td>Controller_Admin_News::action_add</td>
	</tr>
	<tr>
		<td>Редактировать новость</td>
		<td>/admin/news/123/edit</td>
		<td>Request::current()->param('id')</td>
		<td>Controller_Admin_News::action_edit</td>
	</tr>
	<tr>
		<td>Удалить новость</td>
		<td>/admin/news/123/delete</td>
		<td>Request::current()->param('id')</td>
		<td>Controller_Admin_News::action_delete</td>
	</tr>
</table>
Если есть необходимость использовать ЧПУ, то параметром метода ->with_item() надо передать соответствующее регулярное выражение, например
<pre><code>
Meerkat\Base\Route::factory('/admin/news')
->controller('News')
->directory('Admin')
->with_item('([a-z-_0-9]+)')
->put();
</code></pre>
Создаст роуты
<table width="100%">
	<tr>
		<th>Действие</th>
		<th>URL</th>
		<th>Param</th>
		<th>Controller:action</th>
	</tr>
	<tr>
		<td>Показать все новости</td>
		<td>/admin/news/</td>
		<td></td>
		<td>Controller_Admin_News::action_index</td>
	</tr>
	<tr>
		<td>Показать одну новость</td>
		<td>/admin/news/putin-v-kremle</td>
		<td>Request::current()->param('id')</td>
		<td>Controller_Admin_News::action_item</td>
	</tr>
	<tr>
		<td>Добавить новость</td>
		<td>/admin/news/add</td>
		<td></td>
		<td>Controller_Admin_News::action_add</td>
	</tr>
	<tr>
		<td>Редактировать новость</td>
		<td>/admin/news/putin-v-kremle/edit</td>
		<td>Request::current()->param('id')</td>
		<td>Controller_Admin_News::action_edit</td>
	</tr>
	<tr>
		<td>Удалить новость</td>
		<td>/admin/news/putin-v-kremle/delete</td>
		<td>Request::current()->param('id')</td>
		<td>Controller_Admin_News::action_delete</td>
	</tr>
</table>
