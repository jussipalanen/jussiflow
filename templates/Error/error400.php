<?php
/**
 * @var \App\View\AppView $this
 * @var string $message
 */
use Cake\Core\Configure;

$this->assign('title', $message);
?>
<p class="text-xs font-bold uppercase tracking-widest text-navy-400"><?= __('Error') ?></p>
<h1 class="text-2xl font-bold tracking-tight text-navy-900"><?= h($message) ?></h1>
<p class="mt-3 text-navy-600"><?= __d('cake', 'The requested page could not be found.') ?></p>
<?php if (Configure::read('debug') && isset($url)) : ?>
    <p class="mt-4 text-sm text-navy-500"><?= __d('cake', 'Attempted URL') ?>: <code><?= h($url) ?></code></p>
<?php endif; ?>
