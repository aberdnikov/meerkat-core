meerkat/html
============
����������� ������ HTML-��������� �� �������� - ����� ������ ��� ������������ AJAX �������.

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
� ������� ��������
~~~
echo Meerkat\Html\Abbr::factory()
    ->set_title('HyperText Markup Language')
    ->as_initialism()
    ->set_content('Html')
~~~
~~~
<abbr class="initialism" title="HyperText Markup Language">Html</abbr>
~~~
������� ��� ����
~~~
echo Meerkat\Html\Abbr::factory()                  
    ->set_title('HyperText Markup Language')  
    ->set_content('Html');                     
~~~
~~~
<abbr title="HyperText Markup Language">Html</abbr>
~~~

### BLOCKQUOTE
������ �������� �����
~~~
echo Meerkat\Html\Blockquote::factory()
    ->set_author('����� ����')
    ->set_content('����� �� ��������, ��� �� ����-�� �� ������ � �� �����. 
    ���� �� ��������, ��� �� ���-�� ������ � �� ���� ������');

~~~
~~~
<blockquote>
  <p>
    ����� �� ��������, ��� �� ����-�� �� ������ &mdash; �� �����. 
    ���� �� ��������, ��� �� ���-�� ������ &mdash; �� ���� ������
  </p>
  <small>����� ����</small>
</blockquote>
~~~
������ �������� ������
~~~
            $el = Meerkat\Html\Blockquote::factory()
                ->add_class('pull-right')
                ->set_author('����� ����')
                ->set_content('����� �� ��������, ��� �� ����-�� �� ������ � �� �����. 
                ���� �� ��������, ��� �� ���-�� ������ � �� ���� ������');
~~~
~~~
<blockquote class="pull-right">
  <p>
    ����� �� ��������, ��� �� ����-�� �� ������ &mdash; �� �����. 
    ���� �� ��������, ��� �� ���-�� ������ &mdash; �� ���� ������
  </p>
  <small>����� ����</small>
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
