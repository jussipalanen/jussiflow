<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
$tone = $params['tone'] ?? 'border-navy-200 bg-navy-50 text-navy-800';
?>
<div class="mb-5 cursor-pointer rounded-xl border px-4 py-3 text-sm <?= h($tone) ?>"
     role="status" onclick="this.remove();"><?= $message ?></div>
