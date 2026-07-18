<?php

if (!function_exists('sm_card_class')) {

    function sm_card_class(): string
    {
        return 'sm-card';
    }

}

if (!function_exists('sm_button_class')) {

    function sm_button_class(string $type='primary'): string
    {
        return 'btn btn-' . $type . ' sm-btn';
    }

}
if (!function_exists('dashboard_card')) {

    function dashboard_card(
        string $icon,
        string $title,
        string $value,
        string $url,
        string $color = 'primary'
    ): string {

        ob_start();
?>

<div class="col-6 col-md-3">

    <div class="<?= sm_card_class(); ?> dashboard-card">

        <a href="<?= htmlspecialchars($url); ?>" class="text-decoration-none text-dark">

            <div class="dashboard-icon text-<?= htmlspecialchars($color); ?>">

                <i class="<?= sm_icon($icon); ?>"></i>

            </div>

            <h5 class="mt-3">

                <?= htmlspecialchars($title); ?>

            </h5>

            <div class="fs-4 fw-bold text-<?= htmlspecialchars($color); ?>">

                <?= htmlspecialchars($value); ?>

            </div>

        </a>

    </div>

</div>

<?php
        return ob_get_clean();
    }

}