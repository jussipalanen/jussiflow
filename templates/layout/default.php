<?php
/**
 * Application shell: slim header, content well, footer.
 *
 * @var \App\View\AppView $this
 */
$title = $this->fetch('title') ?: __('JussiFlow');
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> &middot; <?= __('JussiFlow') ?></title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css('app') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="flex min-h-full flex-col font-sans">
    <header class="border-b border-cream-300 bg-cream-50">
        <div class="mx-auto flex w-full max-w-5xl items-center justify-between px-6 py-4">
            <a href="<?= $this->Url->build('/') ?>" class="text-lg font-bold tracking-tight text-navy-800">
                Jussi<span class="text-navy-500">Flow</span>
            </a>
            <a href="<?= $this->Url->build('/pages/login') ?>"
               class="text-sm font-bold text-navy-600 hover:text-navy-800">
                <?= __('Sign in') ?>
            </a>
        </div>
    </header>

    <main class="flex-1">
        <div class="mx-auto w-full max-w-5xl px-6 py-10">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <footer class="border-t border-cream-300 bg-cream-50">
        <div class="mx-auto w-full max-w-5xl px-6 py-5 text-sm text-navy-500">
            <?= __('JussiFlow — invoicing demo') ?>
        </div>
    </footer>
</body>
</html>
