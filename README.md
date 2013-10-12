Dictator — open source admin generator for any PHP 5.3 project.
========

* Work with PDO, you can set your current project connection or create new one;
* Set items per page count;
* Multilanguage;
* Connect to any MySQL-table;
* Control fields and their view on list page;
* Edit any row, create new rows, delete, bulk delete;
* All types of relations (one-to-one, one-to-many, many-to-many);
* Upload files and images;
* Search by fields;
* Sort by fields;
* Theming.

List page:
![List page](/screen.png "List page")

New (edit) row page:
![List page](/screen 2.png "Edit page")

Code example:

    // Require lib
    require_once 'src/Dictator.php';
    
    // Create PDO connection or use your project connection
    $pdo = new \PDO(
      'mysql:host=localhost;dbname=dictator',
      'root', '', array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
      )
    );
    
    // Create instance of Dictator
    $dictator = new \Uavn\Dictator;
    $dictator
      ->setConnection($pdo)
      // Set options: ipp — items per page, others — translations.
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
      // Set table and label
      ->setTable('book', 'Книги')
      // Add fields to list and its label
      ->addField('id', 'ID')
      ->addField('pic', 'Обложка')
      ->addField('categoryId', 'Категория')
      ->addField('name', 'Название')
      ->addField('desc', 'Описание')
      ->addField('date', 'Дата')
      // Add list filters: field name and callback function
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
      // This field will generate input type="file" on edit page, second param — upload dir
      ->addFileWidget('pic', __DIR__ . '/upload')
      // This field will geretate textarea on edit page
      ->addTextWidget('desc')
      // Field categoryId maps to table category will show category.name field in list and dropdown
      ->addRelation('categoryId', 'category', 'name')
      // This adds many-to-many widget on edit page:
      // Current table maps to table author,
      //  with field author.name in list and checbox set
      //  with label — Авторы
      //  connected through third table autor_book by bookId and authorId
      ->addManyToManyRelation(
        'author', 'name', 'Авторы',
        'author_book', 'bookId', 'authorId'
      )
      ->addManyToManyRelation(
        'publisher', 'name', 'Издатели',
        'publisher_book', 'bookId', 'publisherId'
      )
      // This creates search by this fields
      ->addSearch('name')
      ->addSearch('desc')
      ;
      // This will generate table or form
      $html = $dictator->generate();
      
      echo $hmtl;
