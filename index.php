<?php
require "./vendor/autoload.php";
$redis = new Predis\Client();

 $request_method = strtolower($_SERVER['REQUEST_METHOD']);
if ($_SERVER['REQUEST_URI'] == "/api/redis") {
    $values =array();
    $keys = $redis->keys('*');
    $res=(object)array();
    foreach ($keys as $key => $value) {
        $v=$value.":".$redis->get($value);
        array_push($values,$v );
    }
    if (count($values)>0) {
        $res->status=true;
        $res->code=200;
        $res->data=$values;
    }else{
        $res->status=false;
        $res->code=500;
        $res->data=$values;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($res);
}

if (isset(explode("redis/",$_SERVER['REQUEST_URI'])[1])) {
    $url = $_SERVER['REQUEST_URI'];
    $tokens = explode('/', $url);
    $key = $tokens[sizeof($tokens)-1];
    $res=(object)array();
    if ($redis->exists($key)) {
        $res->status=true;
        $res->code=200;
        $res->data=(object)array();
        $redis->del( explode(':',$key)[0]);
    }else{
        $res->status=false;
        $res->code=500;
        $res->data=(object)array();
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($res);
}
    // Класс обработки действий
    class action {
        // Функция добавления
        public function add() {

                system("clear");
                echo "             | |\n";
                echo " ___ ___  ___|    ___\n";
                echo "|   |___||   | | |___ \n";
                echo "|   |___ |___| |  ___|\n";
            $redis = new Predis\Client();
            // Задаём вопросы и записываем данные в переменные
            echo "Введите  {key}: \n";
            $key = readline();
            echo "Введите {value} : \n";
            $value = readline();
            $redis->set($key, $value);
            $redis->expire($key, 3600);
            echo "Запись добавлена \n";
            echo $redis->get($key);
        }
        // Функция удаления
        public function delete($id){
            system("clear");
            $redis = new Predis\Client();
            if ($redis->exists($id)) {
                echo "Вы действительно хотите удалить? (y/n) \n";
                $del = readline();
                if ($del == "y") {
                    $redis->del($id);
                    echo "Запись удалена \n";
                }
                else echo "Ну нет, так нет. \n";
            }
            else echo "{key} не существует \n";
        }

        // Функция показа справки
        public function help() {
            system("clear");
            echo "==============================================\n";
            echo "Справка по программе\n";
            echo "==============================================\n";
            echo "php index.php add - добавление \n";
            echo "php index.php delete {key} - удаление с определённым идентификатором\n";
            echo "php index.php help - показать эту подсказку\n";
        }
    }
    switch ($argv[1]) {
        case 'add':
            $do = new action();
            $do ->add();
            break;
        case 'delete':
            if (isset($argv[2])) {
                $do = new action();
                $do ->delete($argv[2]);
            }
            break;
        case 'help':
            $do = new action();
            $do ->help();
            break;
        default:
            break;
    }
    echo "\n";
