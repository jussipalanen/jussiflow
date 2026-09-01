<?php
/**
 * Landing page.
 *
 * Replaces the CakePHP skeleton's status dashboard. Once authentication exists
 * this should redirect signed-in users straight to their invoice list.
 *
 * @var \App\View\AppView $this
 */
$this->assign('title', __('Home'));
?>
<div class="mx-auto max-w-2xl py-10 text-center">
    <h1 class="text-4xl font-bold tracking-tight text-navy-900">
        <?= __('Invoicing, without the mess.') ?>
    </h1>
    <p class="mt-4 text-lg leading-relaxed text-navy-600">
        <?= __('JussiFlow keeps your clients, invoices and totals in one place.') ?>
    </p>
    <div class="mt-8">
        <a href="<?= $this->Url->build('/pages/login') ?>"
           class="inline-flex items-center justify-center rounded-xl bg-navy-800 px-6 py-3
                  font-bold tracking-wide text-cream-50 transition hover:bg-navy-700
                  focus:outline-none focus:ring-4 focus:ring-navy-500/30">
            <?= __('Sign in to your account') ?>
        </a>
    </div>
</div>
