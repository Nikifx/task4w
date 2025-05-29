<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();

    // Сообщение об успешном сохранении
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
    }

    // Массив для хранения ошибок
    $errors = array();
    $errors['fio'] = !empty($_COOKIE['fio_error']);
    $errors['phone'] = !empty($_COOKIE['phone_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['dob'] = !empty($_COOKIE['dob_error']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['languages'] = !empty($_COOKIE['languages_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);
    $errors['contract'] = !empty($_COOKIE['contract_error']);

    // Сообщения об ошибках
    if ($errors['fio']) {
        setcookie('fio_error', '', 100000);
        $messages[] = '<div class="error">Заполните ФИО.</div>';
    }
    if ($errors['phone']) {
        setcookie('phone_error', '', 100000);
        $messages[] = '<div class="error">Некорректный телефон.</div>';
    }
    if ($errors['email']) {
        setcookie('email_error', '', 100000);
        $messages[] = '<div class="error">Некорректный email.</div>';
    }
    if ($errors['dob']) {
        setcookie('dob_error', '', 100000);
        $messages[] = '<div class="error">Некорректная дата рождения.</div>';
    }
    if ($errors['gender']) {
        setcookie('gender_error', '', 100000);
        $messages[] = '<div class="error">Выберите пол.</div>';
    }
    if ($errors['languages']) {
        setcookie('languages_error', '', 100000);
        $messages[] = '<div class="error">Выберите хотя бы один язык программирования.</div>';
    }
    if ($errors['bio']) {
        setcookie('bio_error', '', 100000);
        $messages[] = '<div class="error">Заполните биографию.</div>';
    }
    if ($errors['contract']) {
        setcookie('contract_error', '', 100000);
        $messages[] = '<div class="error">Необходимо ознакомиться с контрактом.</div>';
    }

    // Массив для хранения значений полей
    $values = array();
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
    $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['dob'] = empty($_COOKIE['dob_value']) ? '' : $_COOKIE['dob_value'];
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
    $values['languages'] = empty($_COOKIE['languages_value']) ? array() : json_decode($_COOKIE['languages_value'], true);
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
    $values['contract'] = !empty($_COOKIE['contract_value']);

    // Подключаем форму
    include('form.php');
} else {
    // Обработка POST-запроса
    $errors = FALSE;

    // Валидация ФИО
    if (empty($_POST['fio']) || !preg_match('/^[a-zA-Zа-яА-Я\s]{1,150}$/u', $_POST['fio'])) {
        setcookie('fio_error', '1', 0);
        $errors = TRUE;
    }
    setcookie('fio_value', $_POST['fio'], time() + 365 * 24 * 60 * 60);

    // Валидация телефона
    if (empty($_POST['phone']) || !preg_match('/^\+?\d{10,15}$/', $_POST['phone'])) {
        setcookie('phone_error', '1', 0);
        $errors = TRUE;
    }
    setcookie('phone_value', $_POST['phone'], time() + 365 * 24 * 60 * 60);

    // Валидация email
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', 0);
        $errors = TRUE;
    }
    setcookie('email_value', $_POST['email'], time() + 365 * 24 * 60 * 60);

    // Валидация даты рождения
    if (empty($_POST['dob']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['dob'])) {
        setcookie('dob_error', '1', 0);
        $errors = TRUE;
    }
    setcookie('dob_value', $_POST['dob'], time() + 365 * 24 * 60 * 60);

    // Валидация пола
    if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
        setcookie('gender_error', '1', 0);
        $errors = TRUE;
    }
    setcookie('gender_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);

    // Валидация языков программирования
    if (empty($_POST['languages'])) {
        setcookie('languages_error', '1', 0);
        $errors = TRUE;
    }
    setcookie('languages_value', json_encode($_POST['languages']), time() + 365 * 24 * 60 * 60);

    // Валидация биографии
    if (empty($_POST['bio'])) {
        setcookie('bio_error', '1',0);
        $errors = TRUE;
    }
    setcookie('bio_value', $_POST['bio'], time() + 365 * 24 * 60 * 60);

    // Валидация чекбокса
    if (empty($_POST['contract'])) {
        setcookie('contract_error', '1',0);
        $errors = TRUE;
    }
    setcookie('contract_value', $_POST['contract'], time() + 365 * 24 * 60 * 60);

    if ($errors) {
        // При наличии ошибок перезагружаем страницу
        header('Location: index.php');
        exit();
    } else {
        // Удаляем Cookies с ошибками
        setcookie('fio_error', '', 100000);
        setcookie('phone_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('dob_error', '', 100000);
        setcookie('gender_error', '', 100000);
        setcookie('languages_error', '', 100000);
        setcookie('bio_error', '', 100000);
        setcookie('contract_error', '', 100000);
    }

    // Сохранение в БД
    $user = 'u68818';
    $pass = '9972335';
    $db = new PDO('mysql:host=localhost;dbname=u68818', $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    try {
        $db->beginTransaction();

        // Сохранение основной информации
        $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, dob, gender, bio, contract) 
                              VALUES (:fio, :phone, :email, :dob, :gender, :bio, :contract)");
        $stmt->execute([
            ':fio' => $_POST['fio'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':dob' => $_POST['dob'],
            ':gender' => $_POST['gender'],
            ':bio' => $_POST['bio'],
            ':contract' => isset($_POST['contract']) ? 1 : 0
        ]);

        $application_id = $db->lastInsertId();

        // Сохранение языков программирования
        $stmt = $db->prepare("SELECT id FROM programming_languages WHERE name = :name");
        $insertLang = $db->prepare("INSERT INTO programming_languages (name) VALUES (:name)");
        $linkStmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) 
                                  VALUES (:application_id, :language_id)");

        foreach ($_POST['languages'] as $language) {
            $stmt->execute([':name' => $language]);
            $languageData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$languageData) {
                $insertLang->execute([':name' => $language]);
                $language_id = $db->lastInsertId();
            } else {
                $language_id = $languageData['id'];
            }

            $linkStmt->execute([
                ':application_id' => $application_id,
                ':language_id' => $language_id
            ]);
        }

        $db->commit();

        // Сохранение куки об успешном сохранении
        setcookie('save', '1', time() + 365 * 24 * 60 * 60);

        // Перенаправление
        header('Location: index.php');
    } catch (PDOException $e) {
        $db->rollBack();
        print('Ошибка при сохранении данных: ' . $e->getMessage());
        exit();
    }
}
