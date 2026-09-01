<?php
/**
 * Centred single-card layout for the unauthenticated screens (sign in, password
 * reset). No navigation: there is nowhere to go until you are signed in.
 *
 * @var \App\View\AppView $this
 */
$title = $this->fetch('title') ?: __('Sign in');
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
<body class="min-h-full font-sans">
    <!-- Soft navy wash behind the card, so the cream ground is not perfectly flat. -->
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-12">
        <div aria-hidden="true"
             class="pointer-events-none absolute -top-40 left-1/2 h-[36rem] w-[36rem] -translate-x-1/2
                    rounded-full bg-navy-200/35 blur-3xl"></div>
        <div aria-hidden="true"
             class="pointer-events-none absolute -bottom-48 right-[-6rem] h-[28rem] w-[28rem]
                    rounded-full bg-navy-300/20 blur-3xl"></div>

        <div class="relative w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="<?= $this->Url->build('/') ?>"
                   class="text-2xl font-bold tracking-tight text-navy-800">
                    Jussi<span class="text-navy-500">Flow</span>
                </a>
            </div>

            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>

            <p class="mt-8 text-center text-xs text-navy-400">
                <?= __('JussiFlow — invoicing demo') ?>
            </p>
        </div>
    </div>
</body>
</html>
