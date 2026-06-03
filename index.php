<?php
declare(strict_types=1);

session_start();
date_default_timezone_set('Europe/Moscow');
header('Content-Type: text/html; charset=UTF-8');

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$business = [
    'name' => 'СТО',
    'rating' => '5,0',
    'ratings_count' => '40 оценок',
    'reviews_count' => '17 отзывов',
    'phone' => '+7 (977) 385-05-72',
    'phone_href' => '+79773850572',
    'address' => 'Петровская ул., 52, д. Шишкино',
    'full_address' => 'Московская область, городской округ Домодедово, деревня Шишкино, Петровская улица, 52',
    'hours' => 'Ежедневно 09:00–20:00',
    'maps_url' => 'https://yandex.ru/maps/-/CPXYAT7P',
    'primary_service' => 'Ремонт коммерческого транспорта',
    'primary_price' => 'от 1000 ₽',
];

$services = [
    [
        'title' => 'Ремонт коммерческого транспорта',
        'note' => 'Услуга из карточки: от 1000 ₽.',
        'details' => 'Подходит, если машине нужно вернуться в работу без долгого ожидания и лишних согласований.',
    ],
    [
        'title' => 'Шиномонтаж',
        'note' => 'Указан в карточке и подтверждается отзывами.',
        'details' => 'Переобувка, ремонт прокола, срочная помощь с колесом перед дальнейшей поездкой.',
    ],
    [
        'title' => 'Замена масла',
        'note' => 'Клиенты отмечали замену масла за один визит.',
        'details' => 'Для планового обслуживания и подготовки автомобиля к сезону или дальней дороге.',
    ],
    [
        'title' => 'Тормозная система',
        'note' => 'В отзывах есть замена передних и задних колодок.',
        'details' => 'Когда скрипят тормоза, увеличился тормозной путь или пришло время обслуживания.',
    ],
    [
        'title' => 'Подвеска',
        'note' => 'Клиенты писали о шаровых опорах, стойках и рекомендациях.',
        'details' => 'Для стуков, вибраций, люфтов и подготовки машины к спокойной эксплуатации.',
    ],
    [
        'title' => 'Двигатель и диагностика',
        'note' => 'В отзывах упоминали сложную проблему с двигателем.',
        'details' => 'Когда автомобиль плохо заводится, потерял тягу или требуется понятный план ремонта.',
    ],
    [
        'title' => 'Автоэлектрика и сложные неисправности',
        'note' => 'Клиенты отдельно отмечали работу специалистов.',
        'details' => 'Для ошибок, нестабильной работы систем и ситуаций, где нужна диагностика причины.',
    ],
    [
        'title' => 'Помощь с запчастями',
        'note' => 'По отзывам, сервис помогает найти и привезти детали.',
        'details' => 'Полезно, когда нужно быстрее начать ремонт и не искать запчасть самостоятельно.',
    ],
];

$reviews = [
    [
        'name' => 'Раяна',
        'date' => '6 августа 2025',
        'text' => 'Позвонили — ответили сразу, заранее попросили VIN, помогли найти нужную деталь и доставили ее. Ремонт сравнительно по нормальной цене.',
    ],
    [
        'name' => 'Евгений',
        'date' => '3 марта 2025',
        'text' => 'Была проблема с двигателем, притащили почти мертвый автомобиль. Подошли с пониманием, цены приемлемые, дали машине вторую жизнь.',
    ],
    [
        'name' => 'Николай Осипов',
        'date' => '29 ноября 2024',
        'text' => 'Обслуживаю 2 машины в данном сервисе, профессиональный подход мастеров, оперативный привоз запчастей.',
    ],
    [
        'name' => 'Роман Борисов',
        'date' => '3 ноября 2024',
        'text' => 'За один визит переобули, поменяли масло, заменили элементы подвески и дали рекомендации.',
    ],
    [
        'name' => 'Татьяна',
        'date' => '15 января',
        'text' => 'Позвонила, сориентировали по времени, подъехала, сделали заплатку на пробитом колесе оперативно.',
    ],
    [
        'name' => 'Евгений Анатольевич',
        'date' => '29 марта 2025',
        'text' => 'Все отлично, качественно и на совесть. Главное — исполняют заявленные сроки ремонта.',
    ],
];

$serviceOptions = array_column($services, 'title');
$values = [
    'name' => '',
    'phone' => '',
    'service' => $serviceOptions[0],
    'car' => '',
    'message' => '',
];
$errors = [];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name'] = trim((string)($_POST['name'] ?? ''));
    $values['phone'] = trim((string)($_POST['phone'] ?? ''));
    $values['service'] = trim((string)($_POST['service'] ?? ''));
    $values['car'] = trim((string)($_POST['car'] ?? ''));
    $values['message'] = trim((string)($_POST['message'] ?? ''));

    $token = (string)($_POST['csrf_token'] ?? '');
    $honeypot = trim((string)($_POST['website'] ?? ''));
    $consent = (string)($_POST['consent'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors[] = 'Обновите страницу и отправьте заявку еще раз.';
    }

    if ($honeypot !== '') {
        $errors[] = 'Заявка не прошла проверку.';
    }

    if ($values['name'] === '' || mb_strlen($values['name']) > 80) {
        $errors[] = 'Укажите имя до 80 символов.';
    }

    if (!preg_match('/^[0-9+\-\s()]{7,32}$/u', $values['phone'])) {
        $errors[] = 'Укажите телефон в формате +7 999 000-00-00.';
    }

    if (!in_array($values['service'], $serviceOptions, true)) {
        $errors[] = 'Выберите услугу из списка.';
    }

    if (mb_strlen($values['car']) > 120) {
        $errors[] = 'Поле с автомобилем должно быть короче 120 символов.';
    }

    if (mb_strlen($values['message']) > 800) {
        $errors[] = 'Описание проблемы должно быть короче 800 символов.';
    }

    if ($consent !== 'yes') {
        $errors[] = 'Нужно согласие на обработку заявки.';
    }

    if ($errors === []) {
        $lead = [
            'created_at' => date('c'),
            'name' => $values['name'],
            'phone' => $values['phone'],
            'service' => $values['service'],
            'car' => $values['car'],
            'message' => $values['message'],
            'source' => 'landing',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
        ];

        $dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        $leadFile = $dataDir . DIRECTORY_SEPARATOR . 'leads.php';
        if (!is_file($leadFile)) {
            file_put_contents($leadFile, "<?php http_response_code(403); exit; ?>\n", LOCK_EX);
        }

        file_put_contents(
            $leadFile,
            json_encode($lead, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        $leadEmail = getenv('LEAD_EMAIL') ?: '';
        if ($leadEmail !== '') {
            $subject = 'Новая заявка с сайта СТО';
            $body = "Имя: {$lead['name']}\nТелефон: {$lead['phone']}\nУслуга: {$lead['service']}\nАвто: {$lead['car']}\nОписание: {$lead['message']}\n";
            @mail($leadEmail, $subject, $body);
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: ' . strtok((string)($_SERVER['REQUEST_URI'] ?? '/'), '?') . '?sent=1#booking');
        exit;
    }
}

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string)($_SERVER['HTTP_HOST'] ?? '');
$canonical = $host !== '' ? $scheme . '://' . $host . strtok((string)($_SERVER['REQUEST_URI'] ?? '/'), '?') : '';
$sent = isset($_GET['sent']);

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'AutoRepair',
    'name' => $business['name'],
    'telephone' => $business['phone'],
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Петровская улица, 52',
        'addressLocality' => 'деревня Шишкино',
        'addressRegion' => 'Московская область',
        'addressCountry' => 'RU',
    ],
    'openingHoursSpecification' => [[
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        'opens' => '09:00',
        'closes' => '20:00',
    ]],
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => '5.0',
        'ratingCount' => '40',
        'reviewCount' => '17',
    ],
    'priceRange' => 'от 1000 ₽',
    'url' => $business['maps_url'],
];
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>СТО в Шишкино на Петровской, 52 | Ремонт авто и шиномонтаж</title>
    <meta name="description" content="СТО в деревне Шишкино: рейтинг 5,0 по 40 оценкам, ремонт коммерческого транспорта от 1000 ₽, шиномонтаж, обслуживание авто. Ежедневно 09:00–20:00.">
    <meta name="robots" content="index, follow">
    <?php if ($canonical !== ''): ?>
        <link rel="canonical" href="<?= e($canonical) ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:title" content="СТО на Петровской, 52 в Шишкино">
    <meta property="og:description" content="Ремонт коммерческого транспорта, шиномонтаж и обслуживание авто. Рейтинг 5,0, ежедневная работа 09:00–20:00.">
    <meta property="og:image" content="assets/industrial-hero.jpg">
    <meta name="theme-color" content="#151713">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
    <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</head>
<body>
<a class="skip-link" href="#main">Перейти к содержанию</a>

<header class="site-header" id="top">
    <div class="top-strip">
        <div class="container top-strip__inner">
            <span><?= e($business['hours']) ?></span>
            <a href="<?= e($business['maps_url']) ?>" target="_blank" rel="noopener">Открыть карточку на Яндекс.Картах</a>
        </div>
    </div>

    <div class="container nav">
        <a class="brand" href="#top" aria-label="СТО, перейти в начало">
            <span class="brand__mark" aria-hidden="true">СТО</span>
            <span>
                <strong><?= e($business['name']) ?></strong>
                <small><?= e($business['address']) ?></small>
            </span>
        </a>

        <nav class="nav__links" aria-label="Основная навигация">
            <a href="#services">Услуги</a>
            <a href="#price">Цена</a>
            <a href="#reviews">Отзывы</a>
            <a href="#contacts">Контакты</a>
        </nav>

        <a class="btn btn--small btn--primary" href="tel:<?= e($business['phone_href']) ?>">Позвонить</a>
    </div>
</header>

<main id="main">
    <section class="hero hero--industrial">
        <div class="container hero__grid">
            <div class="hero__content" data-reveal>
                <div class="rating-pill" aria-label="Рейтинг 5,0 по данным Яндекс.Карт">
                    <span class="rating-pill__stars" aria-hidden="true">★★★★★</span>
                    <strong><?= e($business['rating']) ?></strong>
                    <span><?= e($business['ratings_count']) ?> · <?= e($business['reviews_count']) ?></span>
                </div>

                <p class="eyebrow">Автосервис в деревне Шишкино</p>
                <h1>Ремонт и обслуживание авто на Петровской, 52</h1>
                <p class="hero__lead">
                    Ежедневно с 09:00 до 20:00. В отзывах клиенты чаще всего отмечают быстрый ответ,
                    оперативный ремонт, помощь с запчастями и исполнение заявленных сроков.
                </p>

                <div class="hero__actions" aria-label="Быстрые действия">
                    <a class="btn btn--primary" href="tel:<?= e($business['phone_href']) ?>">Позвонить <?= e($business['phone']) ?></a>
                    <a class="btn btn--secondary" href="#booking">Записаться онлайн</a>
                    <a class="btn btn--ghost" href="<?= e($business['maps_url']) ?>" target="_blank" rel="noopener">Построить маршрут</a>
                </div>

                <dl class="hero__facts">
                    <div>
                        <dt>Основная услуга</dt>
                        <dd><?= e($business['primary_service']) ?></dd>
                    </div>
                    <div>
                        <dt>Ориентир по карточке</dt>
                        <dd><?= e($business['primary_price']) ?></dd>
                    </div>
                    <div>
                        <dt>Адрес</dt>
                        <dd><?= e($business['address']) ?></dd>
                    </div>
                </dl>
            </div>

            <aside class="booking-card" id="booking" aria-labelledby="booking-title">
                <div class="booking-card__media">
                    <img src="assets/industrial-hero.jpg" alt="Тематическая фотография ремонтной зоны автосервиса с коммерческим автомобилем" width="960" height="540">
                    <span>Тематическая фотография направления работ</span>
                </div>

                <div class="booking-card__body">
                    <h2 id="booking-title">Уточнить стоимость и время</h2>
                    <p>Опишите автомобиль и проблему. Так мастеру проще сразу понять детали, сроки и возможные запчасти.</p>

                    <?php if ($sent): ?>
                        <div class="notice notice--success" role="status">
                            Заявка сохранена. Если нужен самый быстрый ответ, позвоните напрямую: <?= e($business['phone']) ?>.
                        </div>
                    <?php endif; ?>

                    <?php if ($errors !== []): ?>
                        <div class="notice notice--error" role="alert">
                            <strong>Проверьте заявку:</strong>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form class="lead-form" method="post" action="#booking" accept-charset="UTF-8" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <label class="hidden-field" aria-hidden="true">
                            Сайт
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </label>

                        <div class="form-grid">
                            <label for="lead-name">
                                <span>Имя</span>
                                <input id="lead-name" type="text" name="name" value="<?= e($values['name']) ?>" autocomplete="name" required maxlength="80">
                            </label>

                            <label for="lead-phone">
                                <span>Телефон</span>
                                <input id="lead-phone" type="tel" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel" inputmode="tel" required placeholder="+7 999 000-00-00">
                            </label>
                        </div>

                        <label for="lead-service">
                            <span>Что нужно сделать</span>
                            <select id="lead-service" name="service" required>
                                <?php foreach ($serviceOptions as $service): ?>
                                    <option value="<?= e($service) ?>" <?= $values['service'] === $service ? 'selected' : '' ?>><?= e($service) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label for="lead-car">
                            <span>Автомобиль, VIN или симптом</span>
                            <input id="lead-car" type="text" name="car" value="<?= e($values['car']) ?>" maxlength="120" placeholder="Например: Газель, стук спереди, срочно">
                        </label>

                        <label for="lead-message">
                            <span>Комментарий</span>
                            <textarea id="lead-message" name="message" rows="3" maxlength="800" placeholder="Кратко опишите, что произошло и когда удобно приехать"><?= e($values['message']) ?></textarea>
                        </label>

                        <label class="checkbox" for="lead-consent">
                            <input id="lead-consent" type="checkbox" name="consent" value="yes" required>
                            <span>Согласен на обработку данных для ответа по заявке.</span>
                        </label>

                        <button class="btn btn--primary btn--full" type="submit">Отправить заявку</button>
                    </form>
                </div>
            </aside>
        </div>
    </section>

    <section class="section section--tight">
        <div class="container pain-grid" aria-label="Что закрывает сайт для клиента">
            <article>
                <span class="card-kicker">Цена</span>
                <h2>Не хотите переплатить?</h2>
                <p>Перед поездкой укажите работу, авто и симптомы. В карточке указано: ремонт коммерческого транспорта — от 1000 ₽; остальные работы лучше уточнять по объему.</p>
            </article>
            <article>
                <span class="card-kicker">Качество</span>
                <h2>Нужно доверие к мастерам?</h2>
                <p>У сервиса рейтинг 5,0, 40 оценок и отзывы о профессиональном подходе, оперативности и работе “на совесть”.</p>
            </article>
            <article>
                <span class="card-kicker">Сроки</span>
                <h2>Важно понять, когда забрать авто?</h2>
                <p>Клиенты отмечают, что их сориентировали по времени и исполняли заявленные сроки ремонта.</p>
            </article>
        </div>
    </section>

    <section class="section section--media">
        <div class="container industrial-proof">
            <div class="industrial-proof__copy" data-reveal>
                <p class="eyebrow">Визуально по делу</p>
                <h2>Сервис должен быть понятен еще до звонка</h2>
                <p class="section-lead">
                    В интерфейсе выделены два частых сценария: техническая диагностика и быстрые работы с колесами,
                    подвеской и расходниками. Так проще выбрать направление и описать задачу мастеру.
                </p>
            </div>

            <div class="industrial-proof__photos" aria-label="Тематические фотографии направлений ремонта">
                <figure class="photo-card photo-card--large" data-reveal>
                    <img src="assets/industrial-diagnostics.jpg" alt="Тематическая фотография диагностики автомобиля и автоэлектрики" width="900" height="675" loading="lazy">
                    <figcaption>
                        <strong>Диагностика и электрика</strong>
                        <span>Для ошибок, сложных симптомов и поиска причины неисправности.</span>
                    </figcaption>
                </figure>

                <figure class="photo-card" data-reveal>
                    <img src="assets/industrial-tyres.jpg" alt="Тематическая фотография шиномонтажа и обслуживания подвески" width="900" height="675" loading="lazy">
                    <figcaption>
                        <strong>Шины, подвеска, срочные работы</strong>
                        <span>Когда нужно быстро вернуть машину в ход.</span>
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section class="section" id="services">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow">Услуги без лишних формулировок</p>
                <h2>С чем можно обратиться</h2>
                <p>Список собран из карточки и отзывов клиентов. Если работа не указана, лучше позвонить и описать проблему.</p>
            </div>

            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                    <article class="service-card">
                        <h3><?= e($service['title']) ?></h3>
                        <p class="service-card__note"><?= e($service['note']) ?></p>
                        <p><?= e($service['details']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section--muted" id="price">
        <div class="container split">
            <div>
                <p class="eyebrow">Прозрачность до визита</p>
                <h2>Как быстрее получить понятную стоимость</h2>
                <p class="section-lead">
                    Точная цена зависит от автомобиля, детали и объема работ. Чтобы разговор был конкретным,
                    отправьте марку, симптом, VIN при наличии и удобное время визита.
                </p>
            </div>

            <div class="price-panel" aria-label="Ориентир по цене">
                <span>Ориентир из карточки</span>
                <strong><?= e($business['primary_service']) ?> — <?= e($business['primary_price']) ?></strong>
                <p>По другим работам стоимость стоит уточнить по телефону перед приездом.</p>
                <a class="btn btn--primary btn--full" href="#booking">Уточнить стоимость</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow">Понятный процесс</p>
                <h2>Как проходит обращение</h2>
            </div>

            <ol class="steps">
                <li>
                    <strong>Свяжитесь удобным способом</strong>
                    <span>Позвоните или оставьте заявку, если хотите заранее описать проблему.</span>
                </li>
                <li>
                    <strong>Назовите авто и симптомы</strong>
                    <span>VIN, марка, срочность и что уже известно помогают быстрее понять детали и стоимость.</span>
                </li>
                <li>
                    <strong>Уточните время и объем работ</strong>
                    <span>Перед визитом зафиксируйте, что нужно сделать и когда удобно подъехать.</span>
                </li>
                <li>
                    <strong>Получите ремонт и рекомендации</strong>
                    <span>В отзывах клиенты отмечают оперативность, рекомендации и помощь с деталями.</span>
                </li>
            </ol>
        </div>
    </section>

    <section class="section section--dark" id="reviews">
        <div class="container">
            <div class="section-head section-head--dark">
                <p class="eyebrow">Отзывы с Яндекс.Карт</p>
                <h2>Что клиенты уже проверили на практике</h2>
                <p>Короткие выдержки из отзывов: скорость ответа, сроки, запчасти, цена и качество ремонта.</p>
            </div>

            <div class="reviews-grid">
                <?php foreach ($reviews as $review): ?>
                    <article class="review-card">
                        <div class="review-card__top">
                            <strong><?= e($review['name']) ?></strong>
                            <span><?= e($review['date']) ?></span>
                        </div>
                        <div class="review-card__stars" aria-label="Положительный отзыв">★★★★★</div>
                        <p>“<?= e($review['text']) ?>”</p>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="reviews-footer">
                <span><?= e($business['rating']) ?> · <?= e($business['ratings_count']) ?> · <?= e($business['reviews_count']) ?></span>
                <a class="btn btn--secondary" href="<?= e($business['maps_url']) ?>" target="_blank" rel="noopener">Смотреть карточку</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container faq-grid">
            <div>
                <p class="eyebrow">Коротко перед звонком</p>
                <h2>Ответы на частые вопросы</h2>
            </div>

            <div class="faq-list">
                <details>
                    <summary>Можно ли приехать без записи?</summary>
                    <p>Лучше сначала позвонить. В отзывах клиенты писали, что при свободном месте их принимали оперативно, но график лучше уточнить перед поездкой.</p>
                </details>
                <details>
                    <summary>Можно ли узнать точную цену по телефону?</summary>
                    <p>Можно получить ориентир. Точная сумма зависит от автомобиля, запчасти и объема работ, поэтому полезно заранее назвать VIN, симптом и желаемую услугу.</p>
                </details>
                <details>
                    <summary>Работают ли с коммерческим транспортом?</summary>
                    <p>Да. В карточке указана услуга “ремонт коммерческого транспорта” с ориентиром от 1000 ₽.</p>
                </details>
                <details>
                    <summary>Какие данные указать в заявке?</summary>
                    <p>Имя, телефон, марку автомобиля, симптом, срочность и удобное время. Если есть VIN, добавьте его в поле с автомобилем или комментарием.</p>
                </details>
            </div>
        </div>
    </section>

    <section class="section section--contact" id="contacts">
        <div class="container contact-grid">
            <div>
                <p class="eyebrow">Контакты</p>
                <h2>СТО на Петровской улице, 52</h2>
                <p class="section-lead"><?= e($business['full_address']) ?>. Открыто каждый день с 09:00 до 20:00.</p>
                <div class="contact-actions">
                    <a class="btn btn--primary" href="tel:<?= e($business['phone_href']) ?>">Позвонить <?= e($business['phone']) ?></a>
                    <a class="btn btn--secondary" href="<?= e($business['maps_url']) ?>" target="_blank" rel="noopener">Открыть Яндекс.Карты</a>
                </div>
            </div>

            <address class="contact-card">
                <dl>
                    <div>
                        <dt>Телефон</dt>
                        <dd><a href="tel:<?= e($business['phone_href']) ?>"><?= e($business['phone']) ?></a></dd>
                    </div>
                    <div>
                        <dt>Режим работы</dt>
                        <dd><?= e($business['hours']) ?></dd>
                    </div>
                    <div>
                        <dt>Адрес</dt>
                        <dd><?= e($business['address']) ?></dd>
                    </div>
                    <div>
                        <dt>Рейтинг</dt>
                        <dd><?= e($business['rating']) ?> на основе <?= e($business['ratings_count']) ?></dd>
                    </div>
                </dl>
            </address>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container footer__inner">
        <p>© <?= date('Y') ?> <?= e($business['name']) ?>. Данные основаны на предоставленной карточке Яндекс.Карт.</p>
        <a href="#top">Наверх</a>
    </div>
</footer>

<div class="mobile-cta" aria-label="Быстрая связь">
    <a class="btn btn--primary" href="tel:<?= e($business['phone_href']) ?>">Позвонить</a>
    <a class="btn btn--secondary" href="#booking">Записаться</a>
</div>

<script src="assets/app.js" defer></script>
</body>
</html>
