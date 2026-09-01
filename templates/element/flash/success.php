<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
$params['tone'] = 'border-emerald-200 bg-emerald-50 text-emerald-900';
echo $this->element('flash/default', compact('message', 'params'));
