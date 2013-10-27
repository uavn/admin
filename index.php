<?php
date_default_timezone_set('Europe/Kiev');
require_once 'src/Uavn/Dictator.php';

$pdo = new \PDO(
  'mysql:host=localhost;dbname=dictator',
  'root', '', array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
  )
);

$dictator = new \Uavn\Dictator;
$dictator
  ->setConnection($pdo)
  
  ->setOptions(array(
    'Actions' => 'Действия',
    'Edit' => 'Редактировать',
    'Delete' => 'Удалить',
    'New row' => 'Новая запись',
    'Edit row' => 'Редактировать запись',
    'Page' => 'Страница',
    'Sure?' => 'Уверены?',
    'Search' => 'Найти',
    'Save' => 'Сохранить',
    'List rows' => 'Все записи',
    'Saved successful' => 'Успешно сохранено',
    'ipp' => 5
  ))

  ->setTable('book', 'Книги')

  ->addField('id', 'ID')
  ->addField('pic', 'Обложка')
  ->addField('categoryId', 'Категория')
  ->addField('name', 'Название')
  ->addField('desc', 'Описание')
  ->addField('isSold', 'Продана?')
  ->addField('date', 'Дата')

  ->addFilter('isSold', function( $isSold ) {
    return $isSold
      ? 'Да'
      : 'Нет';
  })
  ->addFilter('pic', function( $text ) {
    if ( $text ) {
      return '<img width="150" src="upload/' . $text . '">';
    }

    return '—';
  })
  ->addFilter('desc', function( $text ) {
    if ( $text ) {
      $substr = mb_substr($text, 0, 200, 'UTF-8');
      return $substr . '…';
    }

    return '—';
  })
  ->addFilter('date', function( $text ) {
    if ( $text ) {
      return date( 'd.m.Y (H:i:s)', strtotime($text) );
    }

    return '—';
  })

  ->addFileWidget('pic', __DIR__ . '/upload')

  ->addTextWidget('desc')

  ->addCheckWidget('isSold')

  ->addRelation('categoryId', 'category', 'name')
  
  ->addManyToManyRelation(
    'author', 'name', 'Авторы',
    'author_book', 'bookId', 'authorId'
  )

  ->addManyToManyRelation(
    'publisher', 'name', 'Издатели',
    'publisher_book', 'bookId', 'publisherId'
  )

  ->addSearch('name')
  ->addSearch('desc')

  ->onBeforeSave('date', function( $date ) {
    return $date
      ? $date
      : date('Y-m-d H:i:s');
  })
  ;

  $html = $dictator->generate();
?>


<html>
<head>
  <title>Dictator Demo</title>

  <!--[if lt IE 9]>
  <style>
    table.dictator th {
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d5e3e4', endColorstr='#b3c8cc',GradientType=0 );
      position: relative;
      z-index: -1;
    }
    table.dictator td {
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ebecda', endColorstr='#ceceb7',GradientType=0 );
      position: relative;
      z-index: -1;
    }
  </style>
  <![endif]-->
</head>
<body>
  <style>
    .dictator-sort-asc, .dictator-sort-desc {
      padding-left: 10px;
      background-repeat: no-repeat;
      background-position: -6px -3px;
    }
    .dictator-sort-desc {
      background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAwNC8xNC8wOFZL9aYAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzQGstOgAAAAj0lEQVQ4jWP8//8/AzUBE1VNGzVwhBjIgk9yr4AAzkTq/OEDI8kGMjAwMJhWVGCIne7owKker5ddPn5khGn+sX8/3LBc459YXcfAwMDA8P//f5wYJr+Hn///x/b2/3v4+f/jU////38GRnx5mZER4ZA9/Pz/t0+RZfr+8zMTAwPD36nJD7DrGS0cRg0kHQAAYMtZjBTUt+kAAAAASUVORK5CYII=');
    }
    .dictator-sort-asc {
      background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAwNC8xNC8wOFZL9aYAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzQGstOgAAAApUlEQVQ4je2TMQ7CMAxFv6MKIYbEV2HIASquwcYhmhv0BD0NibpxDkbGsrGkZkB0ajxAByT6JUuWv/6TLNkkIlhSZlHaCvwTYKWZRDT10Tm57XfV5bgBgNydrvMhESnW24/Oyb1tJTJLtM5oGXXls7WUmMWHgEffwzcNYCgn5mJOBRLR6EMAAGzrGgBeUCAXM9ovJ+aieRgGmpurwE/0+3e4Ar/XEybDVp50x3YfAAAAAElFTkSuQmCC');
    }
    table.dictator {
      font-family: verdana,arial,sans-serif;
      font-size:11px;
      color:#333333;
      border-width: 1px;
      border-color: #999999;
      border-collapse: collapse;
      box-shadow: 0 0 10px #aaa;
      width: 100%;
    }
    table.dictator th a {
      color: #000;
    }
    table.dictator th {
      padding: 0px;
      background: #d5e3e4;
      background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2Q1ZTNlNCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjQwJSIgc3RvcC1jb2xvcj0iI2NjZGVlMCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNiM2M4Y2MiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
      background: -moz-linear-gradient(top,  #d5e3e4 0%, #ccdee0 40%, #b3c8cc 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#d5e3e4), color-stop(40%,#ccdee0), color-stop(100%,#b3c8cc));
      background: -webkit-linear-gradient(top,  #d5e3e4 0%,#ccdee0 40%,#b3c8cc 100%);
      background: -o-linear-gradient(top,  #d5e3e4 0%,#ccdee0 40%,#b3c8cc 100%);
      background: -ms-linear-gradient(top,  #d5e3e4 0%,#ccdee0 40%,#b3c8cc 100%);
      background: linear-gradient(to bottom,  #d5e3e4 0%,#ccdee0 40%,#b3c8cc 100%);
      border: 1px solid #999999;
    }
    table.dictator td {
      padding: 0px;
      background: #ebecda;
      background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ViZWNkYSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjQwJSIgc3RvcC1jb2xvcj0iI2UwZTBjNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNjZWNlYjciIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
      background: -moz-linear-gradient(top,  #ebecda 0%, #e0e0c6 40%, #ceceb7 100%);
      background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ebecda), color-stop(40%,#e0e0c6), color-stop(100%,#ceceb7));
      background: -webkit-linear-gradient(top,  #ebecda 0%,#e0e0c6 40%,#ceceb7 100%);
      background: -o-linear-gradient(top,  #ebecda 0%,#e0e0c6 40%,#ceceb7 100%);
      background: -ms-linear-gradient(top,  #ebecda 0%,#e0e0c6 40%,#ceceb7 100%);
      background: linear-gradient(to bottom,  #ebecda 0%,#e0e0c6 40%,#ceceb7 100%);
      border: 1px solid #999999;
    }
    table.dictator th p{
      margin:0px;
      padding:8px;
      border-bottom:0px;
      border-right:0px;
    }
    table.dictator td p {
      margin:0px;
      padding:8px;
      border-bottom:0px;
      border-right:0px;
    }
    .dictator-pager {
      margin-top: 10px;
    }
    .dictator-current {
      background-color: #fff000;
    }
    .dictator-page, .dictator-current {
      padding: 3px;
    }
    .dictator-pager a {
      color: #000;
    }

    .dictator-search {
      background-color: #fafafa;
      padding: 10px;
    }

    .dictator-search input[type=text] {
      border: 1px solid #aaa;
      padding: 5px;
    }
    
    .dictator-search input[type=submit] {
      padding: 5px 15px;
      border: 1px solid #aaa;
      background-color: #fff;
    }

    .dictator-search input[type=submit]:hover {
      opacity: 0.8;
      cursor: pointer;
      background-color: #333;
      color: #fff;
    }

    .dictator-search label {
      margin-right: 20px;
    }

    .dictator-row-m2m {
      border: 1px solid #ddd;
      display: inline-block;
      max-height: 100px;
      overflow-y: scroll;
      padding: 5px;
    }

    .dictator-row {
      margin-bottom: 10px;
    }

    .dictator-row input[type=text] {
      border: 1px solid #aaa;
      padding: 5px;
    }

    input[type=submit],
    .dictator-row select {
      padding: 5px;
    }

    .dictator-list-rows {
      margin-left: 10px;
    }

    .dictator-row textarea {
      border: 1px solid #aaa;
      padding: 5px;
      height: 200px;
      width: 500px;
    }

    .dictator-saved {
      background-color: rgb(208, 238, 118);
      padding: 10px;
      margin-bottom: 20px;
    }
  </style>

  <?php echo $html ?>

  <script src="jquery-1.10.2.min.js"></script>
  <script>
  $(document).on('click', 'th input[type=checkbox]', function() {
    if ( $(this).is(':checked') ) {
      $('td input[type=checkbox]').prop('checked', true);
    } else {
      $('td input[type=checkbox]').prop('checked', false);
    }
  });

  $(document).on('click', 'td input[type=checkbox]', function() {
    if( $('td input[type=checkbox]:checked').length == $('td input[type=checkbox]').length ) {
      $('th input[type=checkbox]').prop('checked', true);
    } else {
      $('th input[type=checkbox]').prop('checked', false);
    }
  });
  </script>
</body>
</html>