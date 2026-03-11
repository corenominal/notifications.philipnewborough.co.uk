<!doctype html>
<html lang="en-GB" data-bs-theme="dark">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?= esc($title) ?> - <?= esc(config('App')->siteName) ?></title>
        <meta name="theme-color" content="#282A36">
        <!-- Favicon and touch icons -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/icon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/icon-16x16.png">
        <!-- Stylesheets -->
        <link rel="stylesheet" href="<?= config('Urls')->assets ?>assets/css/vendor/bootstrap.css"/>
        <?php if(isset($css)): foreach ($css as $file): $cssPath = FCPATH . 'assets/css/' . $file . '.css'; ?>
        <link rel="stylesheet" href="/assets/css/<?= $file ?>.css<?= file_exists($cssPath) ? '?v=' . filemtime($cssPath) : '' ?>">
        <?php endforeach; endif; ?>
        <!-- JavaScript -->
        <script defer src="<?= config('Urls')->assets ?>assets/js/vendor/bootstrap.bundle.min.js"></script>
        <?php if(isset($js)): foreach ($js as $file): $jsPath = FCPATH . 'assets/js/' . $file . '.js'; ?>
        <script defer src="/assets/js/<?= $file ?>.js<?= file_exists($jsPath) ? '?v=' . filemtime($jsPath) : '' ?>"></script>
        <?php endforeach; endif; ?>
    </head>

    <body class="">
        <main class="unauthorised-wrap">
            <div class="unauthorised-content text-center">
                <img src="/assets/img/skull.svg" alt="" class="unauthorised-img" aria-hidden="true">
                <h1 class="glitch fw-bold" data-text="Access Denied">Access Denied</h1>
                <p class="lead unauthorised-lead">You do not have permission to view this page.</p>

                <a href="<?= config('Urls')->tld ?>" class="unauthorised-link">Return to Home</a>
            </div>
        </main>
    </body>
</html>