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
        <!-- Stylesheets Remote -->
        <link rel="stylesheet" href="<?= config('Urls')->assets ?>assets/css/vendor/bootstrap.css"/>
        <link rel="stylesheet" href="<?= config('Urls')->assets ?>assets/css/vendor/bootstrap-icons.css"/>
        <?php if(isset($datatables) && $datatables): ?>
        <link rel="stylesheet" href="<?= config('Urls')->assets ?>assets/css/vendor/datatables.bootstrap5.min.css"/>
        <?php endif; ?>
        <!-- Stylesheets Local -->
        <link rel="stylesheet" href="/assets/css/templates/default.css"/>
        <?php if(isset($css)): foreach ($css as $file): $cssPath = FCPATH . 'assets/css/' . $file . '.css'; ?>
        <link rel="stylesheet" href="/assets/css/<?= $file ?>.css<?= file_exists($cssPath) ? '?v=' . filemtime($cssPath) : '' ?>">
        <?php endforeach; endif; ?>
        <!-- JavaScript Remote -->
        <script defer src="<?= config('Urls')->assets ?>assets/js/vendor/bootstrap.bundle.min.js"></script>
        <script defer src="<?= config('Urls')->assets ?>assets/js/shared/logout.js"></script>
        <script defer src="<?= config('Urls')->assets ?>assets/js/shared/appmenu.js"></script>
        <script defer src="<?= config('Urls')->assets ?>assets/js/shared/notifications.js"></script>
        <?php if(isset($datatables) && $datatables): ?>
        <script defer src="<?= config('Urls')->assets ?>assets/js/vendor/jquery.min.js"></script>
        <script defer src="<?= config('Urls')->assets ?>assets/js/vendor/datatables.min.js"></script>
        <script defer src="<?= config('Urls')->assets ?>assets/js/vendor/datatables.bootstrap5.min.js"></script>
        <?php endif; ?>
        <!-- JavaScript Local -->
        <script defer src="/assets/js/templates/default.js"></script>
        <?php if(isset($js)): foreach ($js as $file): $jsPath = FCPATH . 'assets/js/' . $file . '.js'; ?>
        <script defer src="/assets/js/<?= $file ?>.js<?= file_exists($jsPath) ? '?v=' . filemtime($jsPath) : '' ?>"></script>
        <?php endforeach; endif; ?>
    </head>
    <body class="d-flex flex-column vh-100">
        <!-- Skip link -->
        <a class="visually-hidden-focusable" href="#main">Skip to main content</a>

        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-top sticky-top shadow py-0">
            <div class="container-fluid px-0">
                <a class="navbar-brand d-flex align-items-center gap-2 ms-3" href="<?= site_url() ?>">
                    <img src="/icon.svg" alt="Logo" width="45" height="45" class="d-inline-block align-text-top rounded-circle my-1">
                    Philip Newborough
                </a>

                <div>
                    <button class="btn btn-outline-primary btn d-lg-none me-3" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="bi bi-three-dots-vertical"></i> <span class="d-none d-sm-inline">Menu</span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="topNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item topnav-item">
                            <a class="nav-link text-white-50 py-3 py-lg-0 px-3" href="<?= config('Urls')->tld ?>"><i class="bi bi-house-fill me-1"></i> Homepage</a>
                        </li>
                        <?php // is_admin session is set and true
                        if( session()->get('is_admin') ):
                        ?>
                        <li class="nav-item topnav-item">
                            <a class="nav-link text-white-50 py-3 py-lg-0 px-3" href="<?= config('Urls')->startpage ?>"><i class="bi bi-slash-square-fill me-1"></i> Startpage</a>
                        </li>
                        <?php endif; ?>
                        <?php // is_admin session is set and true
                        if( session()->get('is_admin') ):
                        ?>
                        <li class="nav-item topnav-item">
                            <a class="nav-link text-white-50 py-3 py-lg-0 px-3 trigger-appmenu" href="#"><i class="bi bi-grid-3x3-gap-fill me-1"></i> App Menu</a>
                        </li>
                        <?php endif; ?>
                        <?php // is_admin session is set and true
                        if( session()->get('is_admin') ):
                        ?>
                        <li class="nav-item topnav-item">
                            <a class="nav-link text-white-50 py-3 py-lg-0 px-3" href="/admin"><i class="bi bi-shield-lock-fill me-1"></i> Admin</a>
                        </li>
                        <?php endif; ?>
                        <?php // If user_uuid session is set, show notification bell and logout link, otherwise show login link
                        if( session()->get('user_uuid') ): ?>
                        <li class="nav-item topnav-item">
                            <a data-api-url="<?= config('Urls')->notifications ?>" data-apikey="<?= esc(service('request')->getCookie('apikey') ?? '') ?>" data-user-uuid="<?= session()->get('user_uuid') ?>" class="nav-link text-white-50 py-3 py-lg-0 px-3 trigger-notifications" href="#"><i id="notification-bell" class="bi bi-bell-fill me-1"></i><span class="d-lg-none me-1"> Notifications</span></a>
                        </li>
                        <li class="nav-item topnav-item">
                            <a class="nav-link text-white-50 py-3 py-lg-0 px-3 trigger-logout" href="#"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item topnav-item">
                            <a class="nav-link text-white-50 py-3 py-lg-0 px-3" href="<?= config('Urls')->auth ?>login?redirect=<?= urlencode(current_url()) ?>"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page wrapper -->
        <div class="d-flex flex-grow-1 overflow-hidden">

            <!-- MAIN CONTENT + FOOTER -->
            <div class="d-flex flex-column flex-grow-1 overflow-auto">
                <main id="main" class="flex-grow-1 pt-3 pb-5 px-2">
                    <?= $this->renderSection('content') ?>
                </main>

                <!-- FOOTER -->
                <footer class="border-top py-4 px-3 mx-4 text-center">
                    <span class="text-secondary">
                        <span class="flip-horizontal">&copy;</span> <?= date('Y') ?> Philip Newborough. All rights reserved.<br>
                        <a class="text-decoration-none me-2" href="<?= config('Urls')->license ?>"><i class="bi bi-file-earmark-text-fill"></i> License</a>
                        <a class="text-decoration-none" href="<?= config('Urls')->github ?>"><i class="bi bi-github"></i> GitHub</a>
                    </span>
                    <?php // is_admin session is set and true
                    if( session()->get('is_admin') ):
                    ?>
                    <br>
                    <span class="text-secondary d-inline-block pt-2"><strong>Hostname:</strong> <?= gethostname() ?><br><strong>PHP version:</strong> <?= phpversion() ?> / <strong>CodeIgniter version:</strong> <?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span>
                    <?php endif; ?>
                </footer>
                <!-- /FOOTER -->
            </div>
            <!-- /MAIN CONTENT + FOOTER -->

        </div>
        <!-- /Page wrapper -->
    </body>
</html>