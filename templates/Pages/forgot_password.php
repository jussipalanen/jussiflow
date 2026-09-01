<?php
/**
 * Password reset request screen.
 *
 * Reached at /pages/forgot-password — PagesController underscores the URL
 * segment, so the hyphenated route maps to this underscored filename.
 *
 * Opened in a new window from the sign-in page. Presentation only — no mail is
 * sent yet. The copy deliberately does not confirm whether an account exists,
 * so the form cannot be used to enumerate registered users.
 *
 * @var \App\View\AppView $this
 */
$this->setLayout('auth');
$this->assign('title', __('Reset your password'));
?>
<div class="rounded-3xl border border-cream-300 bg-white p-8 shadow-xl shadow-navy-900/5 sm:p-10">
    <h1 class="text-2xl font-bold tracking-tight text-navy-900">
        <?= __('Reset your password') ?>
    </h1>
    <p class="mt-3 text-sm leading-relaxed text-navy-600">
        <?= __('Enter your username or email address and we will send you a link to choose a new password.') ?>
    </p>

    <?= $this->Form->create(null, ['class' => 'mt-8 space-y-5']) ?>
        <div>
            <label for="identity" class="field-label"><?= __('Username or email') ?></label>
            <input type="text" id="identity" name="identity" class="field-input"
                   autocomplete="username" autofocus required
                   placeholder="<?= h(__('you@example.com')) ?>">
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-primary"><?= __('Send reset link') ?></button>
        </div>
    <?= $this->Form->end() ?>

    <p class="mt-6 border-t border-cream-200 pt-6 text-center text-sm text-navy-500">
        <?= __('Remembered it?') ?>
        <a href="<?= $this->Url->build('/pages/login') ?>"
           class="font-bold text-navy-600 underline-offset-2 hover:text-navy-800 hover:underline">
            <?= __('Back to sign in') ?>
        </a>
    </p>
</div>
