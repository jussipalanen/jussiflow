<?php
/**
 * Layout for framework error pages.
 *
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->fetch('title') ?> &middot; <?= __('JussiFlow') ?></title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css('app') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="min-h-full font-sans">
    <div class="mx-auto w-full max-w-3xl px-6 py-16">
        <?= $this->Flash->render() ?>
        <div class="rounded-2xl border border-cream-300 bg-white p-8 shadow-sm">
            <?= $this->fetch('content') ?>
            <p class="mt-6">
                <a href="javascript:history.back()" class="text-sm font-bold text-navy-600 hover:text-navy-800">
                    &larr; <?= __('Back') ?>
                </a>
            </p>
        </div>
    </div>
</body>
</html>
