<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
$params['tone'] = 'border-rose-200 bg-rose-50 text-rose-900';
echo $this->element('flash/default', compact('message', 'params'));
