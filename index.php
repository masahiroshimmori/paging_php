<?php

define('DB_DSN','mysql:dbname=paging_php;host=localhost;charset=utf8mb4');
define('DB_USER','dbuser');
define('DB_PASSWORD','2483');
define('COMMENTS_PER_PAGE',5);
$opt = array (
    PDO::ATTR_EMULATE_PREPARES => false,
);

if(preg_match('/^[1-9][0-9]*$/',$_GET['page'])){
  $page = (int)$_GET['page'];
  }else{
  $page = 1;
}

function h($s){
  return htmlspecialchars($s, ENT_QUOTES,'UTF-8');
}

try{
  $dbh = new PDO(DB_DSN, DB_USER, DB_PASSWORD, $opt);
}catch(PDOexception $e){
  echo $e->getMessage();
  exit;
}

//select * from comments limit OFFSET, COUNT
// page offset count
//1     0      5
//2     5      5
//3    10      5
//4    15      5

$offset = COMMENTS_PER_PAGE * ($page - 1);
$sql = "select * from comments limit ".$offset.",".COMMENTS_PER_PAGE;
$pre = $dbh->prepare($sql);

$r = $pre->execute();

if(false === $r){
  echo エラーですよ;
}else{
  $comments = array();
  foreach($pre as $row){
    array_push($comments, $row);
  }
}

$total = $dbh->query("select count(*) from comments")->fetchColumn();
$totalPages = ceil($total / COMMENTS_PER_PAGE);
//var_dump($comments);
//exit;

$from = $offset + 1;
$to = ($offset + COMMENTS_PER_PAGE) < $total ? ($offset + COMMENTS_PER_PAGE) : $total;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>投稿一覧</title>
</head>
<body>
  <h1>投稿一覧</h1>
  <p>全<?php echo $total; ?>件中、<?php echo $from ;?>件<?php echo $to ;?>件を表示しています。</p>
    <ul>
          <?php foreach($comments as $comment) : ?>
            <li><?php echo h($comment['comment']); ?></li>
          <?php endforeach; ?>
  </ul>
      <?php if ($page > 1) : ?>
      <a href="?page=<?php echo $page-1;?>">前へ</a>
      <?php endif ;?>
      <?php for($i = 1; $i <= $totalPages; $i++) :?>
        <?php if($page == $i) : ?>
          <strong><a href="?page=<?php echo $i ;?>"><?php echo $i;?></a></strong>
        <?php else : ?>
          <a href="?page=<?php echo $i ;?>"><?php echo $i;?></a>
        <?php endif; ?>
      <?php endfor ;?>
      <?php if ($page < $totalPages) :?>
      <a href="?page=<?php echo $page+1;?>">次へ</a>
      <?php endif ;?>
</body>
</html>
