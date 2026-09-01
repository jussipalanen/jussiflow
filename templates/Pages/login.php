<?php
/**
 * Sign-in screen.
 *
 * Presentation only: there is no authentication behind it yet, so the form
 * posts back to this same page and simply re-renders. Point the Form->create()
 * URL at the real action once the Users controller exists.
 *
 * @var \App\View\AppView $this
 */
$this->setLayout('auth');
$this->assign('title', __('Sign in'));
?>
<div class="rounded-3xl border border-cream-300 bg-white p-8 shadow-xl shadow-navy-900/5 sm:p-10">
    <h1 class="text-2xl font-bold tracking-tight text-navy-900">
        <?= __('Welcome back') ?>
    </h1>
    <p class="mt-3 text-sm leading-relaxed text-navy-600">
        <?= __('Please login your username and password to JussiFlow.') ?>
    </p>

    <?= $this->Form->create(null, ['class' => 'mt-8 space-y-5']) ?>
        <div>
            <label for="identity" class="field-label"><?= __('Email or username') ?></label>
            <input type="text" id="identity" name="identity" class="field-input"
                   autocomplete="username" autofocus required
                   placeholder="<?= h(__('you@example.com')) ?>">
        </div>

        <div>
            <div class="flex items-baseline justify-between gap-3">
                <label for="password" class="field-label"><?= __('Password') ?></label>
                <a href="<?= $this->Url->build('/pages/forgot-password') ?>"
                   target="_blank" rel="noopener"
                   class="text-xs font-bold text-navy-500 underline-offset-2 hover:text-navy-700 hover:underline">
                    <?= __('Forgot password?') ?>
                </a>
            </div>
            <input type="password" id="password" name="password" class="field-input"
                   autocomplete="current-password" required
                   placeholder="<?= h(__('••••••••')) ?>">
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-primary"><?= __('Sign in') ?></button>
        </div>
    <?= $this->Form->end() ?>
</div>
