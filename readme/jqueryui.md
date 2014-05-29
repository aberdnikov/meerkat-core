meerkat-sf-jqueryui
===================

В PHP
~~~
\Meerkat\StaticFiles\File::need_lib('jqueryui');
Meerkat\StaticFiles\Js::instance()->add_onload('$( "#datepicker" ).datepicker();');
~~~

В HTML
~~~
<p>Date: <input type="text" id="datepicker"></p>
~~~

