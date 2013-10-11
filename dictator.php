<?php

class Dictator {
  private $conn = null;
  private $table = null;
  private $title = null;
  private $fields = array();
  private $filters = array();
  private $fileFilelds = array();
  private $textFilelds = array();
  private $relations = array();
  private $manyRelations = array();
  private $searchs = array();
  private $options = array(
    'ipp' => '10',
    'Actions' => 'Actions',
    'Edit' => 'Edit',
    'Delete' => 'Delete',
    'New row' => 'New row',
    'Edit row' => 'Edit row',
    'Page' => 'Page',
    'Sure?' => 'Sure?',
    'Search' => 'Search',
    'Save' => 'Save',
    'List rows' => 'List rows',
    'Saved successful' => 'Saved successful',
  );

  public function setConnection( \PDO $conn ) {
    $this->conn = $conn;

    return $this;
  }

  public function setTable( $table, $title = null ) {
    $this->table = $table;
    $this->title = $title ?: $table;

    return $this;
  }

  public function addField( $name, $title = null ) {
    $this->fields[$name] = $title ?: $name;

    return $this;
  }

  public function addFileWidget( $name, $uploadPath ) {
    $this->fileFilelds[$name] = $uploadPath;

    return $this;
  }

  public function addTextWidget( $name ) {
    $this->textFilelds[$name] = true;

    return $this;
  }

  public function addFilter( $name, $filter ) {
    $this->filters[$name] = $filter;

    return $this;
  }

  public function addSearch( $name ) {
    $this->searchs[] = $name;

    return $this;
  }

  public function addRelation( $name, $table, $field ) {
    $this->relations[$name] = array(
      'table' => $table,
      'field' => $field,
    );

    return $this;
  }

  public function addManyToManyRelation( $table, $externalName, $title, $relationTable, $internalField, $externalField ) {
    $this->manyRelations[] = array(
      'table' => $table,
      'externalName' => $externalName,
      'title' => $title,
      'relationTable' => $relationTable,
      'internalField' => $internalField,
      'externalField' => $externalField,
    );

    return $this;
  }

  private function generateForm() {
    $id = isset($_REQUEST['edit'])
      ? $_REQUEST['edit']
      : null;

    if ( isset($_REQUEST['save']) && $_REQUEST['save'] ) {
      if ( $id ) {
        $sql = "UPDATE `$this->table` SET ";

        $params = array();
        $sqlParams = array();
        foreach ( $_REQUEST['item'] as $key => $value ) {
          if ( !$value ) $value = null;

          $sqlParams[] = "`{$key}` = :$key";
          $params[$key] = $value;
        }
        $sql .= join(', ', $sqlParams);

        $sql .= " WHERE `id` = {$id}";

        $statement = $this->conn->prepare($sql);
        $statement->execute($params);
      } else {
        $sql = "INSERT INTO `$this->table` ";

        $keys = array();
        $values = array();
        $params = array();
        foreach ( $_REQUEST['item'] as $key => $value ) {
          if ( !$value ) $value = null;

          $keys[] = $key;
          $values[] = ":{$key}";
          $params[$key] = $value;
        }

        $sql .= '(`' . join('`, `', $keys) . '`) VALUES ';
        $sql .= '(' . join(', ', $values) . ')';

        $statement = $this->conn->prepare($sql);
        $statement->execute($params);

        $id = $this->conn->lastinsertId();
      }

      if ( isset($_FILES['file']['tmp_name']) ) {
        foreach ( $_FILES['file']['tmp_name'] as $key => $value ) {
          if ( !$value ) continue;
          $ext = pathinfo($_FILES['file']['name'][$key], PATHINFO_EXTENSION);
          $fileName = uniqid() . '.' . $ext;
          move_uploaded_file($value, $this->fileFilelds[$key] . '/' . $fileName );

          $sql = "UPDATE `$this->table` SET `{$key}` = :{$key} WHERE `id` = :id";
          $statement = $this->conn->prepare($sql);
          $statement->execute(array(
            'id' => $id,
            $key => $fileName
          ));
        }
      }

      if ( isset($_REQUEST['fileremove']) && $_REQUEST['fileremove'] ) {
        foreach ( $_REQUEST['fileremove'] as $key => $value) {
          $sql = "UPDATE `$this->table` SET `{$key}` = :{$key} WHERE `id` = :id";
          $statement = $this->conn->prepare($sql);
          $statement->execute(array(
            'id' => $id,
            $key => null
          ));
        }
      }

      foreach ( $this->manyRelations as $relation ) {
        $sql = "DELETE FROM `{$relation['relationTable']}` WHERE `{$relation['internalField']}` = :id";
        $statement = $this->conn->prepare($sql);
        $statement->execute(array(
          'id' => $id
        ));

        if ( isset($_REQUEST['relation'][$relation['table']]) ) {
          foreach ( $_REQUEST['relation'][$relation['table']] as $eId ) {
            $fields = array($relation['internalField'], $relation['externalField']);
            $fields = '(`' . join('`, `', $fields) . '`)';

            $sql = "INSERT INTO `{$relation['relationTable']}` {$fields} VALUES (:id, :eId)";
            $statement = $this->conn->prepare($sql);
            $statement->execute(array(
              'id' => $id,
              'eId' => $eId
            ));
          }
        }
      }

      header("Location:?edit={$id}&saved=1");
      die;
    }

    $data = new stdClass;
    if ( $id ) {
      $sql = "SELECT * FROM `{$this->table}` WHERE `id` = :id";

      $statement = $this->conn->prepare($sql);
      $statement->execute(array(
        'id' => $id
      ));
      $data = $statement->fetchObject();
    }

    $form = '<h1>' . $this->title . ' (' . ( $id ? $this->t('Edit row') : $this->t('New row') ) . ')</h1>';
    if ( isset($_REQUEST['saved']) ) {
      $form .= '<div class="dictator-saved">' . $this->t('Saved successful') . '</div>';
    }
    $form .= '<form action="" method="POST" enctype="multipart/form-data">';

    foreach ( $this->fields as $name => $title ) {
      // values from DB
      if ( 'id' == $name ) {
        continue;
      }

      $value = null;
      if ( isset($data->{$name}) ) {
        $value = $data->{$name};
      }

      $form .= '<div class="dictator-row">';
      if ( isset($this->relations[$name]) ) {
        $rel = $this->relations[$name];
        $sql = "SELECT * FROM `{$rel['table']}` ORDER BY `{$rel['field']}`";

        $statement = $this->conn->prepare($sql);
        $statement->execute();
        $related = $statement->fetchAll(\PDO::FETCH_OBJ);

        $form .= '<label for="dictatod' . $name . '">' . $title . ':</label><br/>' .
          '<select name="item[' . $name . ']" id="dictatod' . $name . '">';
        $form .= '<option value=""> — </option>';
        foreach ( $related as $item ) {
          $selected = '';
          if ( $value == $item->id ) {
            $selected = 'selected="selected"';
          }
          $form .= '<option ' . $selected . ' value="' . $item->id .'">' . $item->{$rel['field']} . '</option>';
        }
        $form .= "</select>";
      } elseif ( isset($this->fileFilelds[$name]) ) {
        $form .= '<label for="dictatod' . $name . '">' . $title . ':</label><br/>';
        $form .= '<input type="file" name="file[' . $name . ']" id="dictatod' . $name . '"/>';

        if ( $value ) {
          $form .= '<input type="checkbox" name="fileremove[' . $name . ']" id="dictatodl' . $name . '"/>';
          $form .= '<label for="dictatodl' . $name . '">Удалить изображение (' . $value . ')</label> ';
        }

      } elseif ( isset($this->textFilelds[$name]) ) {
        $form .= '<label for="dictatod' . $name . '">' . $title . ':</label><br/>' .
          '<textarea name="item[' . $name . ']" id="dictatod' . $name . '">' . $value . '</textarea>';
      } else {
        $form .= '<label for="dictatod' . $name . '">' . $title . ':</label><br/>' .
          '<input type="text" name="item[' . $name . ']" id="dictatod' . $name . '" value="' . $value . '"/>';
      }
      $form .= '</div>';
    }

    foreach ( $this->manyRelations as $relation ) {
      $relatedIds = array();
      if ( $id ) {
        $sql = "SELECT * FROM `{$relation['relationTable']}` WHERE `{$relation['internalField']}` = {$id}";
        $statement = $this->conn->prepare($sql);
        $statement->execute();
        $relatedObjects = $statement->fetchAll(\PDO::FETCH_OBJ);

        foreach ( $relatedObjects as $object ) {
          $relatedIds[] = $object->{$relation['externalField']};
        }
      }

      $form .= '<div class="dictator-row">' . $relation['title'] . ':<br/><div class="dictator-row-m2m">';

      $statement = $this->conn->prepare("SELECT * FROM `{$relation['table']}`");
      $statement->execute();
      $related = $statement->fetchAll(\PDO::FETCH_OBJ);

      foreach ( $related as $related ) {
        $checked = '';
        if ( in_array($related->id, $relatedIds) ) {
          $checked = 'checked="checked"';
        }

        $form .= '<label>';
        $form .= '<input type="checkbox" ' . $checked . ' name="relation[' . $relation['table'] . '][]" value="' . $related->id . '"/>';
        $form .= $related->{$relation['externalName']};
        $form .= '</label><br/>';
      }

      $form .= '</div></div>';
    }

    $form .= '<input type="submit" name="save" value="' . $this->t('Save') . '"/>';
    $form .= '<a class="dictator-list-rows" href="?">' . $this->t('List rows') . '</a>';
    $form .= '</form>';

    return $form;
  }

  public function generate() {
    if ( isset($_REQUEST['new']) ) {
      return $this->generateForm();
    }

    if ( isset($_REQUEST['edit']) ) {
      return $this->generateForm();
    }

    if ( isset($_REQUEST['delete']) && $_REQUEST['delete'] ) {
      if ( !is_array($_REQUEST['delete']) ) {
        $todel = array($_REQUEST['delete']);
      } else {
        $todel = $_REQUEST['delete'];
      }
      foreach ( $todel as $todelid ) {
        $sql = "DELETE FROM `{$this->table}` WHERE `id` = :id";
        $statement = $this->conn->prepare($sql);
        $statement->execute(array(
          'id' => $todelid
        ));
      }

      header("Location:{$_SERVER['HTTP_REFERER']}");
      die;
    }

    $requestSearch = isset($_REQUEST['search'])
      ? $_REQUEST['search']
      : array();

    $p = ( isset($_REQUEST['p']) && $_REQUEST['p'] > 0 )
      ? $_REQUEST['p']
      : 1;

    $ipp = $this->getOption('ipp');

    $sort = ( isset($_REQUEST['sort']) && $_REQUEST['sort'] )
      ? $_REQUEST['sort']
      : 'id';

    $type = ( isset($_REQUEST['type']) && $_REQUEST['type'] )
      ? $_REQUEST['type']
      : 'DESC';

    if ( !in_array($type, array('ASC', 'DESC')) ) {
      $type = 'DESC';
    }

    $tmpp = $p - 1;
    $offset = $tmpp * $ipp;
    $limit = "{$offset}, {$ipp}";

    $where = array('1');
    foreach ( $requestSearch as $key => $value) {
      $where[] = "`{$key}` LIKE '%{$value}%'";
    }
    $where = join(' AND ', $where);

    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$this->table}` WHERE {$where} ORDER BY `{$sort}` {$type} LIMIT {$limit}";

    $statement = $this->conn->prepare($sql);
    $statement->execute();
    $rows = $statement->fetchAll(\PDO::FETCH_OBJ);

    $statement = $this->conn->prepare("SELECT FOUND_ROWS();");
    $statement->execute();
    $cnt = $statement->fetchColumn(); 

    $table = '<table class="dictator">';
    $table .= '<tr>';
    $table .= '<th><p><input type="checkbox" value="1"/></p></th>';

    foreach ( $this->fields as $name => $title ) {
      $classname = '';
      $totype = 'DESC';
      if ( $sort == $name ) {
        if ( $type != 'ASC' ) {
          $totype = 'ASC';
        }

        $classname = 'dictator-sort-' . strtolower($totype);
      }

      $table .= '<th><p>' .
        '<a class="' . $classname . '" href="?sort=' . $name . '&type=' . $totype . '">' .
          $title .
        '</a>' .
      '</p></th>';
    }
    foreach ( $this->manyRelations as $relation ) {
      $table .= '<th><p>' . $relation['title'] . '</p></th>';
    }
    $table .= '<th><p>' . $this->t('Actions') . '</p></th>';
    $table .= '</tr>';

    foreach ( $rows as $row ) {
      $table .= '<tr>';
      $table .= '<td><p><input type="checkbox" name="delete[]" value="' . $row->id . '"/></p></td>';

      foreach ( $this->fields as $name => $title ) {
        $val = $row->{$name};

        if ( isset($this->relations[$name]) && $val ) {
          $rel = $this->relations[$name];

          $sql = "SELECT * FROM `{$rel['table']}` WHERE `id` = {$val}";
          $statement = $this->conn->prepare($sql);
          $statement->execute();
          $object = $statement->fetchObject();

          $val = $object->{$rel['field']};
        }

        if ( isset($this->filters[$name]) ) {
          $val = $this->filters[$name]($val);
        }

        $table .= '<td><p>' . $val . '</p></td>';
      }

      foreach ( $this->manyRelations as $relation ) {
        $sql = "SELECT `{$relation['table']}`.* FROM `{$relation['relationTable']}` INNER JOIN `{$relation['table']}` ON `{$relation['table']}`.id = `{$relation['relationTable']}`.`{$relation['externalField']}` WHERE `{$relation['internalField']}` = {$row->id}";

        $statement = $this->conn->prepare($sql);
        $statement->execute();
        $related = $statement->fetchAll(\PDO::FETCH_OBJ);

        $vals = array();
        foreach ( $related as $item ) {
          $vals[] = $item->{$relation['externalName']};
        }

        $val = join(', ', $vals);

        $table .= '<td><p>' . $val . '</p></td>';
      }

      $table .= '<td><p>' .
        '<a href="?edit=' . $row->id . '">' . $this->t('Edit') . '</a> <br/>' .
        '<a href="?delete=' . $row->id . '" onclick="return confirm(\'' . $this->t('Sure?') . '\')">' . $this->t('Delete') . '</a>' .
      '</p></td>';

      $table .= '</tr>';
    }
    $table .= '</table>';


    $allpages = ceil($cnt / $ipp);
    $pager = '';
    if ( $allpages > 1 ) {
      $pager = $this->t('Page') . ": ";
      for ( $i = 1; $i <= $allpages; $i++ ) {
        if ( $i == $p ) {
          $pager .= '<span class="dictator-current">' . $i . '</span>';
        } else {
          $params = $_REQUEST;
          $params['p'] = $i;
          $href = http_build_query($params);
          $pager .= '<a class="dictator-page" href="?' . $href . '">' . $i . '</a>';
        }
      }
    }

    $searchForm = '<form class="dictator-search" action="" method="GET">';
    foreach ( $this->searchs as $search ) {
      $value = isset($requestSearch[$search])
        ? $requestSearch[$search]
        : null;
      
      $label = $this->fields[$search];
      $searchForm .= '<label>' . $label . ': <input name="search[' . $search . ']" type="text" value="' . $value . '" placeholder="' . $label . '"/></label>';
    }
    $searchForm .= '<input type="submit" value="' . $this->t('Search') . '"/>';
    $searchForm .= '</form>';

    return 
      '<h1>' . $this->title . '</h1>' .
      $searchForm .
      '<form action="" method="POST" onsubmit="return confirm(\'' . $this->t('Sure?') . '\')">' .
        $table .
        '<br/><input type="submit" value="' . $this->t('Delete') . '"/>' .
        '&nbsp;&nbsp;<a href="?new=1">' . $this->t('New row') . '</a>' .
      '</form>'.
      '<div class="dictator-pager">' .
        $pager .
      '</div>';
  }

  private function t( $key ) {
    return $this->getOption($key);
  }

  public function setOptions( $options ) {
    if ( $options ) {
      $this->options = array_merge(
        $this->options, $options
      );
    }

    return $this;
  }

  private function getOption( $key ) {
    return $this->options[$key];
  }

}