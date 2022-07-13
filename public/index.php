<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    
<?php

require '../vendor/autoload.php';
//use Faker\Factory;
use Aura\SqlQuery\QueryFactory;
use JasonGrimes\Paginator;

// подключаем Класс, можем используем
// $faker = Faker\Factory::create();


//d($faker->text); exit;

// PDO connection
$pdo = new PDO("mysql:host=127.0.0.1;dbname=my_php;charset=utf8", "root", "");
$queryFactory = new QueryFactory('mysql');


// // использование метода Экземпляра для записи в таблицу
// $insert = $queryFactory->newInsert();
// // циклом составляем запрос, рандомными записями из Faker
// $insert->into('posts');         // куда записываем
// for ($i=0; $i<5; $i++) {
//     $insert->cols([
//         'title' => $faker->words(3, true), // метод генерит строку из 3 слов
//         'content' => $faker->text
//     ]);
//     $insert->addRow();  // добавляем строчку за строчкой, в один длинный запрос
// }
// // подготовка и отправка безопасного запроса
// $sth = $pdo->prepare($insert->getStatement());
// $sth->execute($insert->getBindValues());


// получение всех записей
$select = $queryFactory->newSelect();
$select
    ->cols(['*'])
    ->from('posts');
$sth = $pdo->prepare($select->getStatement());
$sth->execute($select->getBindValues());
$totalItems = $sth->fetchAll(PDO::FETCH_ASSOC);


// получение 3-х записей из базы
$select = $queryFactory->newSelect();
$select
    ->cols(['*'])
    ->from('posts')
    ->setPaging(3)                // принимает сколько записей вывести
    ->page($_GET['page'] ?? 1);   // если $_GET['page']==2 - то записи с 4 по 6, а если отсутствует, то 1 страница по умолчанию, то есть первые три записи
$sth = $pdo->prepare($select->getStatement());
$sth->execute($select->getBindValues());
$items = $sth->fetchAll(PDO::FETCH_ASSOC);


// компонент Пагинации
$itemsPerPage = 3;     // количество выводимых элементом
$currentPage = $_GET['page'] ?? 1;
$urlPattern = '?page=(:num)';

$paginator = new Paginator(count($totalItems), $itemsPerPage, $currentPage, $urlPattern);
foreach($items as $item) {
    echo $item['id'] . PHP_EOL . $item['title'] . '<br>';
}
?>

<ul class="paginator">
    <?php if ($paginator->getPrevURL()): ?>
        <li><a href="<?php echo $paginator->getPrevURL();?>"> &laquo; Предыдущая </a> </li>
    <?php endif; ?>

    <?php foreach ($paginator->getPages() as $page): ?>
        <?php if ($page['url']): ?>
            <li <?php echo $page['isCurrent'] ? 'class="active"' : ''; ?>>
                <a href="<?php echo $page['url']; ?>"> <?php echo $page['num']; ?> </a
                > 
            </li>
        <?php else: ?>
            <li class="disabled"><span><?php echo $page['num']; ?> </span> </li>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($paginator->getNextURL()): ?>
        <li><a href="<?php echo $paginator->getNextURL();?>"> Следующая &raquo </a> </li>
    <?php endif; ?>
</ul>   

<p>
    <?php echo $paginator->getTotalItems(); ?> найдено записей.

    Показаны
    <?php echo $paginator->getCurrentPageFirstItem(); ?>
    -
    <?php echo $paginator->getCurrentPageLastItem(); ?>
    .
</p>



</body>
</html>

<?php

