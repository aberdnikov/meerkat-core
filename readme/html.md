meerkat/html
============
Возможность сборки HTML-элементов по кусочкам - очень удобно для формирования AJAX ответов.

### A
~~~
echo Meerkat\Html\A::factory()
    ->set_href('#')
    ->set_attr('data-action', 'load')
    ->set_attr('data-id', 123)
    ->add_class('btn btn-success')
    ->set_content('Load');
~~~
~~~
<a class="btn btn-success" data-id="123" data-action="load" href="#">Load content</a>
~~~

### ABBR
в верхнем регистре
~~~
echo Meerkat\Html\Abbr::factory()
    ->set_title('HyperText Markup Language')
    ->as_initialism()
    ->set_content('Html')
~~~
~~~
<abbr class="initialism" title="HyperText Markup Language">Html</abbr>
~~~
вывести как есть
~~~
echo Meerkat\Html\Abbr::factory()                  
    ->set_title('HyperText Markup Language')  
    ->set_content('Html');                     
~~~
~~~
<abbr title="HyperText Markup Language">Html</abbr>
~~~

### BLOCKQUOTE
цитата прижатая влево
~~~
echo Meerkat\Html\Blockquote::factory()
    ->set_author('Генри Форд')
    ->set_content('«Если вы говорите, что вы чего-то не можете — вы правы. 
    Если вы говорите, что вы что-то можете — вы тоже правы»');

~~~
~~~
<blockquote>
  <p>
    «Если вы говорите, что вы чего-то не можете &mdash; вы правы. 
    Если вы говорите, что вы что-то можете &mdash; вы тоже правы»
  </p>
  <small>Генри Форд</small>
</blockquote>
~~~
цитата прижатая вправо
~~~
            $el = Meerkat\Html\Blockquote::factory()
                ->add_class('pull-right')
                ->set_author('Генри Форд')
                ->set_content('«Если вы говорите, что вы чего-то не можете — вы правы. 
                Если вы говорите, что вы что-то можете — вы тоже правы»');
~~~
~~~
<blockquote class="pull-right">
  <p>
    «Если вы говорите, что вы чего-то не можете &mdash; вы правы. 
    Если вы говорите, что вы что-то можете &mdash; вы тоже правы»
  </p>
  <small>Генри Форд</small>
</blockquote>
~~~

### BUTTON
~~~
echo Meerkat\Html\Button::factory()
    ->set_attr('data-action', 'load')
    ->set_attr('data-id', 123)
    ->add_class('btn btn-success')
    ->set_content('Load');
~~~
